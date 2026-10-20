<?php

namespace App\Services;

use App\Enums\IssueStatus;
use App\Models\GithubPullRequest;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class GitHubWebhookService
{
    /**
     * Verify HMAC SHA-256 signature from GitHub webhook.
     */
    public function verifySignature(string $rawPayload, ?string $signatureHeader, ?string $secret): bool
    {
        // If project does not configure a secret, skip HMAC check
        if (empty($secret)) {
            return true;
        }

        if (empty($signatureHeader)) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $rawPayload, $secret);

        return hash_equals($expected, $signatureHeader);
    }

    /**
     * Handle incoming GitHub webhook payload.
     *
     * @param array $payload
     * @param string|null $event e.g. 'pull_request', 'ping'
     * @param Project|null $specificProject
     * @return array
     */
    public function handleWebhook(array $payload, ?string $event = 'pull_request', ?Project $specificProject = null): array
    {
        // Handle ping event
        if ($event === 'ping') {
            return [
                'success' => true,
                'message' => 'Webhook ping received successfully.',
                'zen' => $payload['zen'] ?? null,
                'hook_id' => $payload['hook_id'] ?? null,
            ];
        }

        // Determine target repository full name
        $repoFullName = $payload['repository']['full_name'] ?? null;

        // Resolve Project: either explicit or by repository full_name
        $project = $specificProject;
        if (!$project && $repoFullName) {
            $project = Project::where('github_repo', $repoFullName)->first();
        }

        if (!$project) {
            return [
                'success' => false,
                'message' => "Tidak ada proyek yang terhubung dengan repositori: " . ($repoFullName ?? 'Unknown'),
                'code' => 404,
            ];
        }

        // Handle Pull Request events
        if (isset($payload['pull_request'])) {
            return $this->handlePullRequestEvent($project, $payload);
        }

        return [
            'success' => true,
            'message' => "Event '{$event}' diterima dan diabaikan (hanya pull_request yang diproses).",
            'project' => $project->name,
        ];
    }

    /**
     * Process pull_request webhook payload.
     */
    protected function handlePullRequestEvent(Project $project, array $payload): array
    {
        $action = $payload['action'] ?? 'opened';
        $prData = $payload['pull_request'];

        $prNumber = (int) ($prData['number'] ?? 0);
        $title = $prData['title'] ?? 'Untitled PR';
        $htmlUrl = $prData['html_url'] ?? '';
        $state = $prData['state'] ?? 'open';
        $isMerged = (bool) ($prData['merged'] ?? false);
        $userUsername = $prData['user']['login'] ?? null;
        $userAvatar = $prData['user']['avatar_url'] ?? null;
        $branchHead = $prData['head']['ref'] ?? null;
        $branchBase = $prData['base']['ref'] ?? null;
        $mergedAt = $prData['merged_at'] ?? null;
        $prCreatedAt = $prData['created_at'] ?? null;
        $prUpdatedAt = $prData['updated_at'] ?? null;
        $body = $prData['body'] ?? '';

        // Detect linked issue key from project key (e.g. PRK-12)
        $linkedIssue = $this->findLinkedIssue($project, $title, $branchHead, $body);

        $statusState = $isMerged ? 'merged' : $state;

        // Create or update pull request record
        $prRecord = GithubPullRequest::updateOrCreate(
            [
                'project_id' => $project->id,
                'pr_number' => $prNumber,
            ],
            [
                'issue_id' => $linkedIssue?->id,
                'title' => $title,
                'html_url' => $htmlUrl,
                'state' => $statusState,
                'user_username' => $userUsername,
                'user_avatar' => $userAvatar,
                'branch_head' => $branchHead,
                'branch_base' => $branchBase,
                'is_merged' => $isMerged,
                'merged_at' => $mergedAt,
                'pr_created_at' => $prCreatedAt,
                'pr_updated_at' => $prUpdatedAt,
                'raw_payload' => $payload,
            ]
        );

        $automationApplied = [];

        // Apply automations to linked issue
        if ($linkedIssue) {
            // Automation 1: Move to IN_REVIEW when PR is opened or synchronized
            if (in_array($action, ['opened', 'reopened', 'synchronize']) && $project->github_auto_move_pr) {
                if ($linkedIssue->status === IssueStatus::TO_DO) {
                    $linkedIssue->update(['status' => IssueStatus::IN_REVIEW]);
                    $automationApplied[] = "Issue {$linkedIssue->issue_key} dipindahkan ke 'In Review'.";
                }
            }

            // Automation 2: Move to DONE when PR is merged
            if ($isMerged && $project->github_auto_close_pr) {
                if ($linkedIssue->status !== IssueStatus::DONE) {
                    $linkedIssue->update(['status' => IssueStatus::DONE]);
                    $automationApplied[] = "Issue {$linkedIssue->issue_key} otomatis diselesaikan ('Done').";
                }
            }

            // Post automated collaboration comment on action
            $this->addCollaborationComment($project, $linkedIssue, $action, $prNumber, $title, $htmlUrl, $userUsername, $branchHead, $branchBase, $isMerged);
        }

        return [
            'success' => true,
            'message' => "Pull Request #{$prNumber} berhasil diproses untuk proyek {$project->name}.",
            'pr_id' => $prRecord->id,
            'pr_number' => $prNumber,
            'linked_issue' => $linkedIssue?->issue_key,
            'automations' => $automationApplied,
        ];
    }

    /**
     * Search for issue key reference in title, branch, or body.
     */
    protected function findLinkedIssue(Project $project, string $title, ?string $branchHead, ?string $body): ?Issue
    {
        $searchString = "{$title} {$branchHead} {$body}";
        $pattern = '/' . preg_quote($project->key, '/') . '-(\d+)/i';

        if (preg_match($pattern, $searchString, $matches)) {
            $issueKey = strtoupper($matches[0]);
            return Issue::where('project_id', $project->id)
                ->where('issue_key', $issueKey)
                ->first();
        }

        return null;
    }

    /**
     * Add collaborative log comment to issue when PR activity occurs.
     */
    protected function addCollaborationComment(
        Project $project,
        Issue $issue,
        string $action,
        int $prNumber,
        string $title,
        string $htmlUrl,
        ?string $userUsername,
        ?string $branchHead,
        ?string $branchBase,
        bool $isMerged
    ): void {
        $actor = $userUsername ? "@{$userUsername}" : "Developer";
        $commentContent = null;

        if ($action === 'opened') {
            $commentContent = "🔗 **GitHub Pull Request #{$prNumber}** dibuka oleh {$actor}:\n[{$title}]({$htmlUrl})\nCabang: `{$branchHead}` ➔ `{$branchBase}`";
        } elseif ($isMerged && in_array($action, ['closed', 'merged'])) {
            $commentContent = "🎉 **GitHub Pull Request #{$prNumber}** berhasil di-merge ke `{$branchBase}` oleh {$actor}!\n[{$title}]({$htmlUrl})";
        }

        if ($commentContent) {
            $issue->comments()->create([
                'user_id' => $project->owner_id,
                'content' => $commentContent,
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Services\GitHubWebhookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GitHubIntegrationController extends Controller
{
    /**
     * Display GitHub integration configuration and PR list for project.
     */
    public function index(Project $project): View
    {
        $project->load([
            'pullRequests' => fn ($query) => $query->with('issue')->latest(),
            'issues' => fn ($query) => $query->select('id', 'project_id', 'issue_key', 'title', 'status')->latest(),
        ]);

        $webhookUrl = $project->getGithubWebhookUrl();
        $pullRequests = $project->pullRequests;
        $projectIssues = $project->issues;
        $isProjectManager = $this->isProjectManager();

        return view('projects.github.index', compact('project', 'webhookUrl', 'pullRequests', 'projectIssues', 'isProjectManager'));
    }

    /**
     * Update GitHub repository settings for the project.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Akses ditolak. Hanya Project Manager yang berwenang mengubah konfigurasi repositori GitHub.');
        }

        $validated = $request->validate([
            'github_repo' => ['required', 'string', 'max:255'],
            'github_auto_move_pr' => ['nullable', 'boolean'],
            'github_auto_close_pr' => ['nullable', 'boolean'],
        ]);

        // Clean repository string (supports "owner/repo" or "https://github.com/owner/repo")
        $repo = trim($validated['github_repo']);
        $repo = preg_replace('#^https?://github\.com/#i', '', $repo);
        $repo = rtrim($repo, '/');

        // Ensure webhook secret exists
        $secret = $project->github_webhook_secret;
        if (empty($secret)) {
            $secret = $project->generateWebhookSecret();
        }

        $project->update([
            'github_repo' => $repo,
            'github_webhook_secret' => $secret,
            'github_auto_move_pr' => $request->boolean('github_auto_move_pr', true),
            'github_auto_close_pr' => $request->boolean('github_auto_close_pr', true),
        ]);

        return redirect()->route('projects.github.index', $project)
            ->with('success', "Integrasi GitHub untuk repositori '{$repo}' berhasil disimpan.");
    }

    /**
     * Regenerate webhook secret for this project.
     */
    public function regenerateSecret(Project $project): RedirectResponse
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Akses ditolak. Hanya Project Manager yang berwenang memperbarui secret webhook GitHub.');
        }

        $newSecret = $project->generateWebhookSecret();
        $project->update(['github_webhook_secret' => $newSecret]);

        return redirect()->route('projects.github.index', $project)
            ->with('success', 'Secret Webhook baru berhasil di-generate. Silakan perbarui secret di pengaturan Webhook GitHub.');
    }

    /**
     * Disconnect project from GitHub repository.
     */
    public function disconnect(Project $project): RedirectResponse
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Akses ditolak. Hanya Project Manager yang berwenang memutus integrasi repositori GitHub.');
        }

        $oldRepo = $project->github_repo;
        $project->update([
            'github_repo' => null,
            'github_webhook_secret' => null,
        ]);

        return redirect()->route('projects.github.index', $project)
            ->with('success', "Proyek berhasil diputus dari repositori GitHub ({$oldRepo}).");
    }

    /**
     * Simulate an incoming Pull Request webhook directly from within the application.
     */
    public function simulate(Request $request, Project $project, GitHubWebhookService $webhookService): RedirectResponse
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Akses ditolak. Hanya Project Manager yang berwenang menjalankan simulator webhook.');
        }

        $validated = $request->validate([
            'action' => ['required', 'in:opened,merged'],
            'issue_id' => ['nullable', 'exists:issues,id'],
            'pr_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $action = $validated['action'];
        $issue = !empty($validated['issue_id']) ? Issue::find($validated['issue_id']) : $project->issues()->first();

        $prNumber = $validated['pr_number'] ?? random_int(10, 99);
        $issueTag = $issue ? "[{$issue->issue_key}] " : "[{$project->key}-1] ";
        $title = $issueTag . ($action === 'merged' ? 'Resolve bug and finalize changes' : 'Implement requested feature');
        $branchHead = 'feature/' . ($issue ? strtolower($issue->issue_key) : strtolower($project->key) . '-1') . '-patch';
        $repoName = $project->github_repo ?: "org/{$project->key}-repo";

        $currentUser = $request->user();
        $developerLogin = ($currentUser && $currentUser->name)
            ? strtolower(str_replace(' ', '', $currentUser->name))
            : 'developer';

        $payload = [
            'action' => $action === 'merged' ? 'closed' : 'opened',
            'repository' => [
                'full_name' => $repoName,
                'name' => basename($repoName),
                'html_url' => "https://github.com/{$repoName}",
            ],
            'pull_request' => [
                'number' => $prNumber,
                'title' => $title,
                'html_url' => "https://github.com/{$repoName}/pull/{$prNumber}",
                'state' => $action === 'merged' ? 'closed' : 'open',
                'merged' => ($action === 'merged'),
                'merged_at' => $action === 'merged' ? now()->toIso8601String() : null,
                'created_at' => now()->subMinutes(15)->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
                'head' => [
                    'ref' => $branchHead,
                ],
                'base' => [
                    'ref' => 'main',
                ],
                'user' => [
                    'login' => $developerLogin,
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/9919?v=4',
                ],
                'body' => "Simulasi Pull Request untuk tiket " . ($issue ? $issue->issue_key : $project->key . '-1') . " via Webhook Simulator.",
            ],
        ];

        $result = $webhookService->handleWebhook($payload, 'pull_request', $project);

        if ($result['success']) {
            $automations = !empty($result['automations']) ? implode(' ', $result['automations']) : '';
            return redirect()->route('projects.github.index', $project)
                ->with('success', "Simulasi Webhook berhasil! Pull Request #{$prNumber} ({$action}) telah diterima. {$automations}");
        }

        return redirect()->route('projects.github.index', $project)
            ->with('error', "Gagal memproses simulasi: " . ($result['message'] ?? 'Error tidak diketahui'));
    }
}

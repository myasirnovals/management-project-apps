<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IssueController extends Controller
{
    /**
     * Store a newly created issue in storage.
     */
    public function store(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(IssueType::class)],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:issues,id'],
            'priority' => ['nullable', Rule::enum(IssuePriority::class)],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'component_id' => ['nullable', 'exists:components,id'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
            'version_id' => ['nullable', 'exists:release_versions,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        $issueKey = $project->generateNextIssueKey();

        $maxOrder = Issue::where('project_id', $project->id)->max('order_index') ?? 0;

        $issue = Issue::create([
            'project_id' => $project->id,
            'issue_key' => $issueKey,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'status' => IssueStatus::TO_DO,
            'priority' => $validated['priority'] ?? IssuePriority::MEDIUM,
            'story_points' => $validated['story_points'] ?? null,
            'reporter_id' => Auth::id(),
            'assignee_id' => $validated['assignee_id'] ?? null,
            'component_id' => $validated['component_id'] ?? null,
            'sprint_id' => $validated['sprint_id'] ?? null,
            'version_id' => $validated['version_id'] ?? null,
            'order_index' => $maxOrder + 1,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Issue {$issue->issue_key} berhasil dibuat!",
                'issue' => $issue->load(['assignee', 'component', 'parent', 'version']),
            ]);
        }

        return back()->with('success', "Issue {$issue->issue_key} berhasil dibuat!");
    }

    /**
     * Show issue details (JSON or view).
     */
    public function show(Project $project, Issue $issue): JsonResponse|View
    {
        $issue->load([
            'assignee', 
            'reporter', 
            'component', 
            'parent', 
            'version', 
            'sprint', 
            'subtasks.assignee',
            'attachments.user',
            'comments.user',
        ]);

        if (request()->wantsJson() || request()->ajax()) {
            $waService = app(\App\Services\WhatsAppService::class);
            $hasAssigneeWa = (bool) ($issue->assignee && $issue->assignee->hasWhatsApp());
            $currentUserId = Auth::id();
            $isPm = $this->isProjectManager();

            $attachments = $issue->attachments->map(fn ($att) => [
                'id' => $att->id,
                'filename' => $att->filename,
                'url' => $att->url(),
                'download_url' => route('projects.issues.attachments.download', [$project, $issue, $att]),
                'is_image' => $att->isImage(),
                'file_type' => $att->file_type,
                'formatted_size' => $att->formattedSize(),
                'user_name' => $att->user?->name ?? 'User',
                'user_initials' => $att->user?->initials() ?? 'U',
                'created_at_human' => $att->created_at->diffForHumans(),
                'can_delete' => $currentUserId === $att->user_id || $isPm,
            ]);

            $comments = $issue->comments->map(fn ($com) => [
                'id' => $com->id,
                'content' => $com->content,
                'user_id' => $com->user_id,
                'user_name' => $com->user?->name ?? 'User',
                'user_role' => $com->user?->role ?? 'developer',
                'user_initials' => $com->user?->initials() ?? 'U',
                'created_at_human' => $com->created_at->diffForHumans(),
                'can_delete' => $currentUserId === $com->user_id || $isPm,
            ]);

            return response()->json([
                'issue' => $issue,
                'type_badge' => $issue->type->badgeClasses(),
                'status_badge' => $issue->status->badgeClasses(),
                'priority_badge' => $issue->priority->badgeClasses(),
                'attachments' => $attachments,
                'comments' => $comments,
                'is_pm' => $isPm,
                'current_user_id' => $currentUserId,
                'whatsapp' => [
                    'has_phone' => $hasAssigneeWa,
                    'phone' => $issue->assignee?->phone,
                    'assignee_name' => $issue->assignee?->name,
                    'reminder_url' => $hasAssigneeWa ? $waService->generateWhatsAppUrl(
                        $issue->assignee->phone,
                        $waService->buildMessage($issue->assignee, $issue, 'reminder')
                    ) : null,
                    'overdue_url' => $hasAssigneeWa ? $waService->generateWhatsAppUrl(
                        $issue->assignee->phone,
                        $waService->buildMessage($issue->assignee, $issue, 'overdue')
                    ) : null,
                    'unresponsive_url' => $hasAssigneeWa ? $waService->generateWhatsAppUrl(
                        $issue->assignee->phone,
                        $waService->buildMessage($issue->assignee, $issue, 'unresponsive')
                    ) : null,
                ],
            ]);
        }

        return view('issues.show', compact('project', 'issue'));
    }

    /**
     * Update the specified issue in storage.
     */
    public function update(Request $request, Project $project, Issue $issue): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::enum(IssueType::class)],
            'status' => ['required', Rule::enum(IssueStatus::class)],
            'priority' => ['required', Rule::enum(IssuePriority::class)],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'original_estimate_hours' => ['nullable', 'numeric', 'min:0'],
            'time_spent_hours' => ['nullable', 'numeric', 'min:0'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'component_id' => ['nullable', 'exists:components,id'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
            'version_id' => ['nullable', 'exists:release_versions,id'],
            'parent_id' => ['nullable', 'exists:issues,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        $issue->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Issue {$issue->issue_key} berhasil diperbarui.",
                'issue' => $issue->fresh(['assignee', 'component', 'parent', 'version']),
            ]);
        }

        return back()->with('success', "Issue {$issue->issue_key} berhasil diperbarui.");
    }

    /**
     * Add a sub-task to an issue (Modul II.C Gambar 2.20 & 2.21).
     */
    public function storeSubtask(Request $request, Project $project, Issue $issue): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        $subtaskKey = $project->generateNextIssueKey();
        $maxOrder = $issue->subtasks()->max('order_index') ?? 0;

        $subtask = Issue::create([
            'project_id' => $project->id,
            'issue_key' => $subtaskKey,
            'title' => $validated['title'],
            'type' => IssueType::SUBTASK,
            'parent_id' => $issue->id,
            'status' => IssueStatus::TO_DO,
            'priority' => $issue->priority,
            'reporter_id' => Auth::id(),
            'assignee_id' => $validated['assignee_id'] ?? $issue->assignee_id,
            'sprint_id' => $issue->sprint_id,
            'component_id' => $issue->component_id,
            'version_id' => $issue->version_id,
            'order_index' => $maxOrder + 1,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Subtask {$subtask->issue_key} ditambahkan.",
                'subtask' => $subtask->load('assignee'),
            ]);
        }

        return back()->with('success', "Subtask {$subtask->issue_key} ditambahkan.");
    }

    /**
     * Remove the specified issue from storage.
     */
    public function destroy(Project $project, Issue $issue): JsonResponse|RedirectResponse
    {
        $key = $issue->issue_key;
        $issue->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Issue {$key} berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Issue {$key} berhasil dihapus.");
    }
}

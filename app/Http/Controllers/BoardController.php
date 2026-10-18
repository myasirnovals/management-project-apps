<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\SprintStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BoardController extends Controller
{
    /**
     * Display the Scrum Board for a project.
     */
    public function index(Request $request, Project $project): View|RedirectResponse
    {
        // For Kanban projects, redirect to kanban board
        if ($project->type === 'kanban') {
            return $this->kanban($request, $project);
        }

        // Active sprint or selected sprint
        $sprintId = $request->query('sprint_id');
        if ($sprintId) {
            $currentSprint = $project->sprints()->where('id', $sprintId)->first();
        } else {
            $currentSprint = $project->activeSprint;
        }

        // Available sprints for dropdown
        $allSprints = $project->sprints;

        // Query issues in current sprint
        $issuesQuery = Issue::with(['assignee', 'reporter', 'component', 'parent', 'version', 'subtasks'])
            ->where('project_id', $project->id);

        if ($currentSprint) {
            $issuesQuery->where('sprint_id', $currentSprint->id);
        } else {
            // If no active sprint, show issues in backlog or empty
            $issuesQuery->whereNull('sprint_id');
        }

        // Filter: Only standard issues and bugs (epics are shown as swimlanes or badges)
        $issuesQuery->where('type', '!=', IssueType::EPIC->value);

        // Apply Quick Filters (Modul IV.D)
        $quickFilter = $request->query('filter');
        if ($quickFilter === 'only_my_issues') {
            $issuesQuery->where('assignee_id', Auth::id());
        } elseif ($quickFilter === 'recently_updated') {
            $issuesQuery->where('updated_at', '>=', Carbon::now()->subDays(2));
        } elseif ($quickFilter === 'bugs_only') {
            $issuesQuery->where('type', IssueType::BUG->value);
        } elseif ($quickFilter === 'high_priority') {
            $issuesQuery->whereIn('priority', [IssuePriority::HIGH->value, IssuePriority::HIGHEST->value]);
        }

        // Search
        if ($search = $request->query('search')) {
            $issuesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('issue_key', 'like', "%{$search}%");
            });
        }

        $issues = $issuesQuery->orderBy('order_index')->get();

        // Columns definition (Modul IV.B)
        $columns = [
            'to_do' => [
                'title' => 'To Do',
                'status' => IssueStatus::TO_DO,
                'min' => 0,
                'max' => 10,
                'issues' => $issues->where('status', IssueStatus::TO_DO),
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'status' => IssueStatus::IN_PROGRESS,
                'min' => 0,
                'max' => 5,
                'issues' => $issues->where('status', IssueStatus::IN_PROGRESS),
            ],
            'in_review' => [
                'title' => 'In Review',
                'status' => IssueStatus::IN_REVIEW,
                'min' => 0,
                'max' => 5,
                'issues' => $issues->where('status', IssueStatus::IN_REVIEW),
            ],
            'done' => [
                'title' => 'Done',
                'status' => IssueStatus::DONE,
                'min' => 0,
                'max' => null,
                'issues' => $issues->where('status', IssueStatus::DONE),
            ],
        ];

        // Swimlanes Setting (Modul IV.C): 'none', 'stories', 'assignee', 'epics'
        $swimlaneMode = $request->query('swimlane', 'none');

        $users = $project->members()->orderBy('name')->get();
        if ($users->isEmpty()) {
            $users = User::where('is_active', true)->get();
        }

        $epics = $project->epics;

        return view('board.scrum', compact(
            'project',
            'currentSprint',
            'allSprints',
            'columns',
            'issues',
            'swimlaneMode',
            'quickFilter',
            'users',
            'epics'
        ));
    }

    /**
     * Display Kanban Board (Modul III.C).
     */
    public function kanban(Request $request, Project $project): View
    {
        $issuesQuery = Issue::with(['assignee', 'reporter', 'component', 'parent', 'version'])
            ->where('project_id', $project->id)
            ->where('type', '!=', IssueType::EPIC->value);

        if ($search = $request->query('search')) {
            $issuesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('issue_key', 'like', "%{$search}%");
            });
        }

        $issues = $issuesQuery->orderBy('order_index')->get();

        $columns = [
            'to_do' => [
                'title' => 'Backlog & To Do',
                'status' => IssueStatus::TO_DO,
                'issues' => $issues->where('status', IssueStatus::TO_DO),
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'status' => IssueStatus::IN_PROGRESS,
                'issues' => $issues->where('status', IssueStatus::IN_PROGRESS),
            ],
            'in_review' => [
                'title' => 'In Review / QA',
                'status' => IssueStatus::IN_REVIEW,
                'issues' => $issues->where('status', IssueStatus::IN_REVIEW),
            ],
            'done' => [
                'title' => 'Done',
                'status' => IssueStatus::DONE,
                'issues' => $issues->where('status', IssueStatus::DONE),
            ],
        ];

        $users = $project->members;
        $epics = $project->epics;

        return view('board.kanban', compact('project', 'columns', 'issues', 'users', 'epics'));
    }

    /**
     * Update issue status via Drag & Drop or Ajax quick action.
     */
    public function updateStatus(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'issue_id' => ['required', 'exists:issues,id'],
            'status' => ['required', 'in:to_do,in_progress,in_review,done'],
            'order_index' => ['nullable', 'integer'],
        ]);

        $issue = Issue::where('project_id', $project->id)->findOrFail($validated['issue_id']);
        $issue->status = IssueStatus::from($validated['status']);
        
        if (isset($validated['order_index'])) {
            $issue->order_index = (int) $validated['order_index'];
        }

        $issue->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Issue {$issue->issue_key} dipindahkan ke {$issue->status->label()}.",
                'issue' => $issue,
            ]);
        }

        return back()->with('success', "Status {$issue->issue_key} berhasil diubah.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(Request $request, Project $project): View|RedirectResponse
    {
        if ($project->type === 'kanban') {
            return $this->kanban($request, $project);
        }

        $sprintId = $request->query('sprint_id');
        $currentSprint = $sprintId
            ? $project->sprints()->where('id', $sprintId)->first()
            : $project->activeSprint;

        $allSprints = $project->sprints;

        $issuesQuery = Issue::with(['assignee', 'reporter', 'component', 'parent.parent', 'version', 'subtasks', 'pullRequests'])
            ->where('project_id', $project->id);

        if ($currentSprint) {
            $issuesQuery->where('sprint_id', $currentSprint->id);
        } else {
            $issuesQuery->whereNull('sprint_id');
        }

        $issuesQuery->where('type', '!=', IssueType::EPIC->value);

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

        if ($search = $request->query('search')) {
            $issuesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('issue_key', 'like', "%{$search}%");
            });
        }

        $issues = $issuesQuery->orderBy('order_index')->get();

        $columns = $this->buildStatusColumns($issues, true);

        $rawSwimlane = $request->query('swimlane', 'none');
        $swimlaneMode = $rawSwimlane === 'epic' ? 'epics' : $rawSwimlane;
        if (!in_array($swimlaneMode, ['none', 'stories', 'assignee', 'epics'], true)) {
            $swimlaneMode = 'none';
        }

        $users = $project->members()->orderBy('name')->get();
        if ($users->isEmpty()) {
            $users = User::where('is_active', true)->orderBy('name')->get();
        }

        $epics = $project->epics;
        $components = $project->components;
        $versions = $project->versions;

        $swimlanes = $this->buildSwimlanes($swimlaneMode, $issues, $users, $epics);

        return view('board.scrum', compact(
            'project',
            'currentSprint',
            'allSprints',
            'columns',
            'swimlanes',
            'issues',
            'swimlaneMode',
            'quickFilter',
            'users',
            'epics',
            'components',
            'versions'
        ));
    }

    public function kanban(Request $request, Project $project): View
    {
        $issuesQuery = Issue::with(['assignee', 'reporter', 'component', 'parent', 'version', 'subtasks', 'pullRequests'])
            ->where('project_id', $project->id)
            ->where('type', '!=', IssueType::EPIC->value);

        $quickFilter = $request->query('filter');
        if ($quickFilter === 'only_my_issues') {
            $issuesQuery->where('assignee_id', Auth::id());
        } elseif ($quickFilter === 'bugs_only') {
            $issuesQuery->where('type', IssueType::BUG->value);
        } elseif ($quickFilter === 'high_priority') {
            $issuesQuery->whereIn('priority', [IssuePriority::HIGH->value, IssuePriority::HIGHEST->value]);
        }

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
                'max' => null,
                'issues' => $issues->where('status', IssueStatus::TO_DO),
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'status' => IssueStatus::IN_PROGRESS,
                'max' => 5,
                'issues' => $issues->where('status', IssueStatus::IN_PROGRESS),
            ],
            'in_review' => [
                'title' => 'In Review / QA',
                'status' => IssueStatus::IN_REVIEW,
                'max' => 5,
                'issues' => $issues->where('status', IssueStatus::IN_REVIEW),
            ],
            'done' => [
                'title' => 'Done',
                'status' => IssueStatus::DONE,
                'max' => null,
                'issues' => $issues->where('status', IssueStatus::DONE),
            ],
        ];

        $users = $project->members()->orderBy('name')->get();
        if ($users->isEmpty()) {
            $users = User::where('is_active', true)->orderBy('name')->get();
        }
        $epics = $project->epics;
        $components = $project->components;
        $versions = $project->versions;

        return view('board.kanban', compact(
            'project',
            'columns',
            'issues',
            'quickFilter',
            'users',
            'epics',
            'components',
            'versions'
        ));
    }

    public function updateStatus(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (!Auth::user()?->canMutateIssues()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Peran Viewer hanya memiliki izin baca (read-only).'], 403);
            }
            abort(403, 'Akses ditolak. Peran Viewer hanya memiliki hak akses lihat (read-only).');
        }

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

    protected function buildStatusColumns(Collection $issues, bool $withWipLimits = true): array
    {
        return [
            'to_do' => [
                'title' => 'To Do',
                'status' => IssueStatus::TO_DO,
                'min' => 0,
                'max' => $withWipLimits ? 10 : null,
                'issues' => $issues->where('status', IssueStatus::TO_DO),
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'status' => IssueStatus::IN_PROGRESS,
                'min' => 0,
                'max' => $withWipLimits ? 5 : null,
                'issues' => $issues->where('status', IssueStatus::IN_PROGRESS),
            ],
            'in_review' => [
                'title' => 'In Review',
                'status' => IssueStatus::IN_REVIEW,
                'min' => 0,
                'max' => $withWipLimits ? 5 : null,
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
    }

    protected function buildSwimlanes(string $mode, Collection $issues, Collection $users, Collection $epics): array
    {
        if ($mode === 'none' || $issues->isEmpty()) {
            return [];
        }

        $lanes = [];

        if ($mode === 'assignee') {
            $grouped = $issues->groupBy(fn (Issue $i) => $i->assignee_id ? (string) $i->assignee_id : 'unassigned');
            foreach ($users as $member) {
                $key = (string) $member->id;
                if ($grouped->has($key)) {
                    $laneIssues = $grouped->get($key);
                    $lanes[] = [
                        'id' => 'assignee-' . $member->id,
                        'title' => $member->name,
                        'meta' => $member->role->label(),
                        'count' => $laneIssues->count(),
                        'columns' => $this->buildStatusColumns($laneIssues, false),
                    ];
                }
            }
            if ($grouped->has('unassigned')) {
                $laneIssues = $grouped->get('unassigned');
                $lanes[] = [
                    'id' => 'assignee-unassigned',
                    'title' => 'Belum Ditugaskan',
                    'meta' => 'Unassigned',
                    'count' => $laneIssues->count(),
                    'columns' => $this->buildStatusColumns($laneIssues, false),
                ];
            }
        } elseif ($mode === 'epics') {
            $grouped = $issues->groupBy(function (Issue $issue) {
                if ($issue->parent && $issue->parent->type === IssueType::EPIC) {
                    return (string) $issue->parent->id;
                }
                if ($issue->parent?->parent && $issue->parent->parent->type === IssueType::EPIC) {
                    return (string) $issue->parent->parent->id;
                }
                return 'no_epic';
            });

            foreach ($epics as $epic) {
                $key = (string) $epic->id;
                if ($grouped->has($key)) {
                    $laneIssues = $grouped->get($key);
                    $lanes[] = [
                        'id' => 'epic-' . $epic->id,
                        'title' => "{$epic->issue_key}: {$epic->title}",
                        'meta' => 'Epic',
                        'count' => $laneIssues->count(),
                        'columns' => $this->buildStatusColumns($laneIssues, false),
                    ];
                }
            }
            if ($grouped->has('no_epic')) {
                $laneIssues = $grouped->get('no_epic');
                $lanes[] = [
                    'id' => 'epic-none',
                    'title' => 'Tanpa Epic',
                    'meta' => 'Mandiri',
                    'count' => $laneIssues->count(),
                    'columns' => $this->buildStatusColumns($laneIssues, false),
                ];
            }
        } elseif ($mode === 'stories') {
            $grouped = $issues->groupBy(function (Issue $issue) {
                if ($issue->type === IssueType::SUBTASK && $issue->parent && $issue->parent->type !== IssueType::EPIC) {
                    return 'story-' . $issue->parent->id;
                }
                if ($issue->type === IssueType::STORY || $issue->subtasks->isNotEmpty()) {
                    return 'story-' . $issue->id;
                }
                return 'other';
            });

            foreach ($grouped as $key => $laneIssues) {
                if ($key === 'other') {
                    continue;
                }
                $first = $laneIssues->first();
                $story = ($first->type === IssueType::SUBTASK && $first->parent) ? $first->parent : $first;
                $lanes[] = [
                    'id' => $key,
                    'title' => "{$story->issue_key}: {$story->title}",
                    'meta' => $story->type->label(),
                    'count' => $laneIssues->count(),
                    'columns' => $this->buildStatusColumns($laneIssues, false),
                ];
            }

            if ($grouped->has('other')) {
                $laneIssues = $grouped->get('other');
                $lanes[] = [
                    'id' => 'story-other',
                    'title' => 'Tugas & Bug Mandiri',
                    'meta' => 'Tanpa Sub-task',
                    'count' => $laneIssues->count(),
                    'columns' => $this->buildStatusColumns($laneIssues, false),
                ];
            }
        }

        return $lanes;
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\SprintStatus;
use App\Enums\VersionStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ProjectFeedback;
use App\Models\ReleaseVersion;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $isPm = $user->isProjectManager();

        $projectsQuery = Project::with(['activeSprint', 'members'])
            ->withCount([
                'issues as open_issues_count' => fn ($query) => $query->where('status', '!=', IssueStatus::DONE->value),
                'issues as done_issues_count' => fn ($query) => $query->where('status', IssueStatus::DONE->value),
                'feedbacks as pending_feedback_count' => fn ($query) => $query->where('status', 'pending'),
            ]);

        if (!$isPm) {
            $projectsQuery->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('members', fn ($m) => $m->where('users.id', $user->id));
            });
        }

        $projects = $projectsQuery->latest()->get();
        $accessibleProjectIds = $projects->pluck('id');
        $firstProject = $projects->first();

        $activeSprints = Sprint::with('project')
            ->whereIn('project_id', $accessibleProjectIds)
            ->where('status', SprintStatus::ACTIVE->value)
            ->get();

        $myIssues = Issue::with(['project', 'sprint'])
            ->whereIn('project_id', $accessibleProjectIds)
            ->where('assignee_id', $user->id)
            ->where('status', '!=', IssueStatus::DONE->value)
            ->orderByRaw("CASE priority WHEN 'highest' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->take(8)
            ->get();

        $recentIssues = Issue::with(['project', 'assignee', 'reporter'])
            ->whereIn('project_id', $accessibleProjectIds)
            ->latest()
            ->take(6)
            ->get();

        $totalProjectsCount = $projects->count();
        $activeSprintsCount = $activeSprints->count();
        $myTasksCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->where('assignee_id', $user->id)
            ->where('status', '!=', IssueStatus::DONE->value)
            ->count();
        $myInReviewTasksCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->where('assignee_id', $user->id)
            ->where('status', IssueStatus::IN_REVIEW->value)
            ->count();
        $myCompletedTasksCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->where('assignee_id', $user->id)
            ->where('status', IssueStatus::DONE->value)
            ->count();
        $completedTasksCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->where('status', IssueStatus::DONE->value)
            ->count();
        $totalIssuesCount = Issue::whereIn('project_id', $accessibleProjectIds)->count();
        $openIssuesCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->where('status', '!=', IssueStatus::DONE->value)
            ->count();
        $workspaceCompletionRate = $totalIssuesCount > 0
            ? (int) round(($completedTasksCount / $totalIssuesCount) * 100)
            : 0;

        $pendingFeedbackCount = ProjectFeedback::whereIn('project_id', $accessibleProjectIds)
            ->where('status', 'pending')
            ->count();
        $pendingFeedbacks = $isPm
            ? ProjectFeedback::with(['project', 'user'])->where('status', 'pending')->latest()->take(5)->get()
            : collect();

        $myFeedbacks = ProjectFeedback::with('project')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $inReviewIssuesCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->where('status', IssueStatus::IN_REVIEW->value)
            ->count();
        $activeSprintIssuesCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->whereHas('sprint', fn ($q) => $q->where('status', SprintStatus::ACTIVE->value))
            ->where('status', '!=', IssueStatus::DONE->value)
            ->count();
        $backlogIssuesCount = Issue::whereIn('project_id', $accessibleProjectIds)
            ->whereNull('sprint_id')
            ->where('status', '!=', IssueStatus::DONE->value)
            ->count();
        $activeReleasesCount = ReleaseVersion::whereIn('project_id', $accessibleProjectIds)
            ->where('status', VersionStatus::UNRELEASED->value)
            ->count();

        $stats = [
            'total_projects' => $totalProjectsCount,
            'active_sprints' => $activeSprintsCount,
            'my_tasks_count' => $myTasksCount,
            'my_in_review_tasks' => $myInReviewTasksCount,
            'my_completed_tasks' => $myCompletedTasksCount,
            'completed_tasks_count' => $completedTasksCount,
            'open_issues_count' => $openIssuesCount,
            'workspace_completion_rate' => $workspaceCompletionRate,
            'pending_feedback_count' => $pendingFeedbackCount,
            'in_review_issues_count' => $inReviewIssuesCount,
            'active_sprint_issues_count' => $activeSprintIssuesCount,
            'backlog_issues_count' => $backlogIssuesCount,
            'active_releases_count' => $activeReleasesCount,
        ];

        return view('dashboard.index', compact(
            'projects',
            'firstProject',
            'activeSprints',
            'myIssues',
            'recentIssues',
            'pendingFeedbacks',
            'myFeedbacks',
            'stats'
        ));
    }
}

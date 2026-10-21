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

        // Projects the user owns or belongs to
        $projects = Project::with(['activeSprint', 'members'])
            ->withCount([
                'issues as open_issues_count' => function ($query) {
                    $query->where('status', '!=', IssueStatus::DONE->value);
                },
                'issues as done_issues_count' => function ($query) {
                    $query->where('status', IssueStatus::DONE->value);
                },
                'feedbacks as pending_feedback_count' => function ($query) {
                    $query->where('status', 'pending');
                },
            ])
            ->latest()
            ->get();

        $firstProject = $projects->first();

        $activeSprints = Sprint::with('project')
            ->where('status', SprintStatus::ACTIVE->value)
            ->get();

        // Issues assigned to logged in user (excluding Done)
        $myIssues = Issue::with(['project', 'sprint'])
            ->where('assignee_id', $user->id)
            ->where('status', '!=', IssueStatus::DONE->value)
            ->orderByRaw("CASE priority WHEN 'highest' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->take(8)
            ->get();

        $recentIssues = Issue::with(['project', 'assignee', 'reporter'])
            ->latest()
            ->take(6)
            ->get();

        // Common counters
        $totalProjectsCount = $projects->count();
        $activeSprintsCount = $activeSprints->count();
        $myTasksCount = Issue::where('assignee_id', $user->id)->where('status', '!=', IssueStatus::DONE->value)->count();
        $myInReviewTasksCount = Issue::where('assignee_id', $user->id)->where('status', IssueStatus::IN_REVIEW->value)->count();
        $myCompletedTasksCount = Issue::where('assignee_id', $user->id)->where('status', IssueStatus::DONE->value)->count();
        $completedTasksCount = Issue::where('status', IssueStatus::DONE->value)->count();
        $totalIssuesCount = Issue::count();
        $openIssuesCount = Issue::where('status', '!=', IssueStatus::DONE->value)->count();
        $workspaceCompletionRate = $totalIssuesCount > 0 ? (int) round(($completedTasksCount / $totalIssuesCount) * 100) : 0;

        // Feedback data
        $pendingFeedbackCount = ProjectFeedback::where('status', 'pending')->count();
        $pendingFeedbacks = $user->isProjectManager()
            ? ProjectFeedback::with(['project', 'user'])->where('status', 'pending')->latest()->take(5)->get()
            : collect();

        $myFeedbacks = ProjectFeedback::with('project')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Role-specific metrics
        $inReviewIssuesCount = Issue::where('status', IssueStatus::IN_REVIEW->value)->count();
        $activeSprintIssuesCount = Issue::whereHas('sprint', function ($q) {
            $q->where('status', SprintStatus::ACTIVE->value);
        })->where('status', '!=', IssueStatus::DONE->value)->count();

        $backlogIssuesCount = Issue::whereNull('sprint_id')
            ->where('status', '!=', IssueStatus::DONE->value)
            ->count();

        $activeReleasesCount = ReleaseVersion::where('status', VersionStatus::UNRELEASED->value)->count();

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

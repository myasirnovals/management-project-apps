<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\SprintStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
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
            ])
            ->latest()
            ->get();

        $activeSprints = Sprint::with('project')
            ->where('status', SprintStatus::ACTIVE->value)
            ->get();

        $myIssues = Issue::with(['project', 'sprint'])
            ->where('assignee_id', $user->id)
            ->where('status', '!=', IssueStatus::DONE->value)
            ->orderBy('priority', 'desc')
            ->take(8)
            ->get();

        $recentIssues = Issue::with(['project', 'assignee', 'reporter'])
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'total_projects' => $projects->count(),
            'active_sprints' => $activeSprints->count(),
            'my_tasks_count' => Issue::where('assignee_id', $user->id)->where('status', '!=', IssueStatus::DONE->value)->count(),
            'completed_tasks_count' => Issue::where('status', IssueStatus::DONE->value)->count(),
        ];

        return view('dashboard.index', compact('projects', 'activeSprints', 'myIssues', 'recentIssues', 'stats'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ProjectFeedback;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Akses ditolak. Panel Admin hanya dapat diakses oleh Project Manager.');
        }

        $projects = Project::with(['owner', 'activeSprint'])
            ->withCount([
                'members',
                'issues',
                'issues as open_issues_count' => fn ($q) => $q->where('status', '!=', IssueStatus::DONE->value),
                'feedbacks as pending_feedback_count' => fn ($q) => $q->where('status', 'pending'),
            ])
            ->latest()
            ->get();

        $usersCount = User::count();
        $activeUsersCount = User::where('is_active', true)->count();
        $pendingFeedbacks = ProjectFeedback::with(['project', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(8)
            ->get();

        $settingsConfigured = file_exists(storage_path('app/app_settings.json'));
        $githubConnectedCount = $projects->filter(fn (Project $p) => $p->hasGithubIntegration())->count();

        $adminStats = [
            'projects_total' => $projects->count(),
            'projects_active' => $projects->where('status', 'active')->count(),
            'users_total' => $usersCount,
            'users_active' => $activeUsersCount,
            'issues_total' => Issue::count(),
            'pending_feedback' => ProjectFeedback::where('status', 'pending')->count(),
            'github_connected' => $githubConnectedCount,
            'settings_configured' => $settingsConfigured,
        ];

        return view('admin.index', compact('projects', 'pendingFeedbacks', 'adminStats'));
    }
}


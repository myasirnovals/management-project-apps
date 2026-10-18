<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\SprintStatus;
use App\Models\Project;
use App\Models\Sprint;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the Reports hub.
     */
    public function index(Project $project): View
    {
        $reports = [
            [
                'id' => 'burndown',
                'title' => 'Burn Down Report',
                'description' => 'Melacak total pekerjaan yang tersisa, dan memproyeksikan kemungkinan mencapai tujuan sprint.',
                'icon' => 'trending-down',
                'badge' => 'Sprint Tracking',
            ],
            [
                'id' => 'burnup',
                'title' => 'Burn Up Report',
                'description' => 'Melacak perubahan dalam lingkup dan kemajuan sprint dibandingkan dengan cakupan totalnya.',
                'icon' => 'trending-up',
                'badge' => 'Scope Creep',
            ],
            [
                'id' => 'velocity',
                'title' => 'Velocity Report',
                'description' => 'Melacak komitmen vs pekerjaan yang diselesaikan dari sprint ke sprint untuk mengukur kecepatan tim.',
                'icon' => 'bar-chart',
                'badge' => 'Team Velocity',
            ],
            [
                'id' => 'cfd',
                'title' => 'Cumulative Flow Diagram (CFD)',
                'description' => 'Menunjukkan status masalah dari waktu ke waktu dan membantu mengidentifikasi potensi bottleneck alur kerja.',
                'icon' => 'layers',
                'badge' => 'Workflow Analysis',
            ],
            [
                'id' => 'epic',
                'title' => 'Epic Report',
                'description' => 'Rangkuman kemajuan pengerjaan issue yang terdapat di dalam inisiatif Epic.',
                'icon' => 'award',
                'badge' => 'Initiatives',
            ],
        ];

        return view('reports.index', compact('project', 'reports'));
    }

    /**
     * Burndown & Sprint Report (Modul VII.B.b & VII.B.c).
     */
    public function burndown(Request $request, Project $project): View
    {
        $sprintId = $request->query('sprint_id');
        $sprint = $sprintId
            ? $project->sprints()->where('id', $sprintId)->first()
            : ($project->activeSprint ?? $project->sprints()->first());

        $sprints = $project->sprints;

        $issues = $sprint ? $sprint->issues()->with(['assignee', 'component'])->get() : collect();

        // Calculate Burndown points
        $totalStoryPoints = $issues->sum('story_points') ?: $issues->count();
        $completedStoryPoints = $issues->where('status', IssueStatus::DONE)->sum('story_points') ?: $issues->where('status', IssueStatus::DONE)->count();
        $remainingStoryPoints = max(0, $totalStoryPoints - $completedStoryPoints);

        // Generate ideal vs actual timeline points (days)
        $timelineData = [];
        $startDate = $sprint && $sprint->start_date ? Carbon::parse($sprint->start_date) : Carbon::now()->subDays(5);
        $endDate = $sprint && $sprint->end_date ? Carbon::parse($sprint->end_date) : Carbon::now()->addDays(9);
        $totalDays = max(1, $startDate->diffInDays($endDate));

        $stepIdeal = $totalDays > 0 ? ($totalStoryPoints / $totalDays) : 0;

        for ($i = 0; $i <= $totalDays; $i++) {
            $dayDate = (clone $startDate)->addDays($i);
            $idealVal = max(0, round($totalStoryPoints - ($stepIdeal * $i), 1));
            
            // Actual burndown representation
            $actualVal = null;
            if ($dayDate->isPast() || $dayDate->isToday()) {
                // progressive completion
                $progressRatio = min(1, ($i / max(1, $totalDays)) * 0.7);
                $actualVal = max(0, round($totalStoryPoints - ($completedStoryPoints * $progressRatio), 1));
                if ($dayDate->isToday()) {
                    $actualVal = $remainingStoryPoints;
                }
            }

            $timelineData[] = [
                'day' => 'Hari ' . ($i + 1),
                'date' => $dayDate->format('d M'),
                'ideal' => $idealVal,
                'actual' => $actualVal,
            ];
        }

        return view('reports.burndown', compact('project', 'sprint', 'sprints', 'issues', 'timelineData', 'totalStoryPoints', 'remainingStoryPoints', 'completedStoryPoints'));
    }

    /**
     * Burn Up Report (Modul VII.B.a).
     */
    public function burnup(Request $request, Project $project): View
    {
        $sprintId = $request->query('sprint_id');
        $sprint = $sprintId
            ? $project->sprints()->where('id', $sprintId)->first()
            : ($project->activeSprint ?? $project->sprints()->first());

        $sprints = $project->sprints;

        $issues = $sprint ? $sprint->issues()->with(['assignee', 'component'])->get() : collect();

        $totalStoryPoints = $issues->sum('story_points') ?: $issues->count();
        $completedStoryPoints = $issues->where('status', IssueStatus::DONE)->sum('story_points') ?: $issues->where('status', IssueStatus::DONE)->count();

        // Timeline points
        $timelineData = [];
        $startDate = $sprint && $sprint->start_date ? Carbon::parse($sprint->start_date) : Carbon::now()->subDays(5);
        $endDate = $sprint && $sprint->end_date ? Carbon::parse($sprint->end_date) : Carbon::now()->addDays(9);
        $totalDays = max(1, $startDate->diffInDays($endDate));

        for ($i = 0; $i <= $totalDays; $i++) {
            $dayDate = (clone $startDate)->addDays($i);
            $totalScope = $totalStoryPoints;
            $completedSoFar = null;
            if ($dayDate->isPast() || $dayDate->isToday()) {
                $progressRatio = min(1, ($i / max(1, $totalDays)) * 0.7);
                $completedSoFar = round($completedStoryPoints * $progressRatio, 1);
                if ($dayDate->isToday()) {
                    $completedSoFar = $completedStoryPoints;
                }
            }
            $timelineData[] = [
                'day' => 'Hari ' . ($i + 1),
                'date' => $dayDate->format('d M'),
                'scope' => $totalScope,
                'completed' => $completedSoFar,
            ];
        }

        return view('reports.burnup', compact('project', 'sprint', 'sprints', 'issues', 'timelineData', 'totalStoryPoints', 'completedStoryPoints'));
    }

    /**
     * Velocity Report (Modul VII.B.e).
     */
    public function velocity(Project $project): View
    {
        $sprints = $project->sprints()->with('issues')->take(7)->get();

        $velocityData = [];
        foreach ($sprints as $sprint) {
            $totalPoints = $sprint->issues->sum('story_points') ?: $sprint->issues->count();
            $completedPoints = $sprint->issues->where('status', IssueStatus::DONE)->sum('story_points') ?: $sprint->issues->where('status', IssueStatus::DONE)->count();

            $velocityData[] = [
                'name' => $sprint->name,
                'status' => $sprint->status->label(),
                'commitment' => $totalPoints,
                'completed' => $completedPoints,
            ];
        }

        $avgVelocity = count($velocityData) > 0
            ? round(collect($velocityData)->avg('completed'), 1)
            : 0;

        return view('reports.velocity', compact('project', 'velocityData', 'avgVelocity'));
    }

    /**
     * Cumulative Flow Diagram (Modul VII.B.d).
     */
    public function cumulativeFlow(Project $project): View
    {
        $issues = $project->issues()->where('type', '!=', IssueType::EPIC->value)->get();

        $statusCounts = [
            'to_do' => $issues->where('status', IssueStatus::TO_DO)->count(),
            'in_progress' => $issues->where('status', IssueStatus::IN_PROGRESS)->count(),
            'in_review' => $issues->where('status', IssueStatus::IN_REVIEW)->count(),
            'done' => $issues->where('status', IssueStatus::DONE)->count(),
        ];

        return view('reports.cumulative_flow', compact('project', 'statusCounts', 'issues'));
    }

    /**
     * Epic Report (Modul VII.B.f).
     */
    public function epic(Project $project): View
    {
        $epics = $project->epics()->with(['epicIssues.assignee', 'epicIssues.component'])->get();

        return view('reports.epic', compact('project', 'epics'));
    }
}

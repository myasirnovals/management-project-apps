<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Project $project): View
    {
        $reports = [
            [
                'id' => 'burndown',
                'title' => 'Burn Down Report',
                'description' => 'Melacak sisa pekerjaan harian berdasarkan penyelesaian tiket nyata terhadap garis target sprint.',
                'badge' => 'Pelacakan Sprint',
            ],
            [
                'id' => 'burnup',
                'title' => 'Burn Up Report',
                'description' => 'Membandingkan akumulasi pekerjaan selesai terhadap total cakupan (scope) sprint.',
                'badge' => 'Cakupan & Progres',
            ],
            [
                'id' => 'velocity',
                'title' => 'Velocity Report',
                'description' => 'Membandingkan komitmen story points dengan realisasi penyelesaian antar-sprint.',
                'badge' => 'Kecepatan Tim',
            ],
            [
                'id' => 'cfd',
                'title' => 'Distribusi Alur Kerja (CFD)',
                'description' => 'Menampilkan komposisi status tiket untuk mengidentifikasi antrean atau hambatan proses.',
                'badge' => 'Analisis Alur',
            ],
            [
                'id' => 'epic',
                'title' => 'Epic Report',
                'description' => 'Rangkuman persentase penyelesaian seluruh tiket di bawah setiap inisiatif Epic.',
                'badge' => 'Inisiatif',
            ],
        ];

        return view('reports.index', compact('project', 'reports'));
    }

    public function burndown(Request $request, Project $project): View
    {
        $sprintId = $request->query('sprint_id');
        $sprint = $sprintId
            ? $project->sprints()->where('id', $sprintId)->first()
            : ($project->activeSprint ?? $project->sprints()->first());

        $sprints = $project->sprints;
        $issues = $sprint ? $sprint->issues()->with(['assignee', 'component'])->get() : collect();

        $usesPoints = $issues->sum('story_points') > 0;
        $metricUnit = $usesPoints ? 'SP' : 'Tiket';
        $weightFn = fn ($item) => $usesPoints ? (int) ($item->story_points ?? 0) : 1;

        $totalStoryPoints = $issues->sum($weightFn);
        $doneIssues = $issues->where('status', IssueStatus::DONE);
        $completedStoryPoints = $doneIssues->sum($weightFn);
        $remainingStoryPoints = max(0, $totalStoryPoints - $completedStoryPoints);

        $timelineData = [];
        if ($sprint && $sprint->start_date && $sprint->end_date) {
            $startDate = Carbon::parse($sprint->start_date)->startOfDay();
            $endDate = Carbon::parse($sprint->end_date)->startOfDay();
            $totalDays = max(1, (int) $startDate->diffInDays($endDate));
            $stepIdeal = $totalDays > 0 ? ($totalStoryPoints / $totalDays) : 0;

            for ($i = 0; $i <= $totalDays; $i++) {
                $dayDate = (clone $startDate)->addDays($i);
                $idealVal = max(0, round($totalStoryPoints - ($stepIdeal * $i), 1));

                $actualVal = null;
                if ($dayDate->lte(Carbon::today())) {
                    $doneByDay = $doneIssues->filter(function ($issue) use ($dayDate, $startDate) {
                        if (!$issue->updated_at) {
                            return false;
                        }
                        $completedAt = Carbon::parse($issue->updated_at);
                        return $completedAt->lte($dayDate->copy()->endOfDay()) || $dayDate->equalTo($startDate) && $completedAt->lt($startDate);
                    })->sum($weightFn);

                    $actualVal = max(0, round($totalStoryPoints - $doneByDay, 1));
                }

                $timelineData[] = [
                    'day' => 'Hari ' . ($i + 1),
                    'date' => $dayDate->format('d M'),
                    'ideal' => $idealVal,
                    'actual' => $actualVal,
                ];
            }
        }

        return view('reports.burndown', compact(
            'project',
            'sprint',
            'sprints',
            'issues',
            'timelineData',
            'totalStoryPoints',
            'remainingStoryPoints',
            'completedStoryPoints',
            'metricUnit'
        ));
    }

    public function burnup(Request $request, Project $project): View
    {
        $sprintId = $request->query('sprint_id');
        $sprint = $sprintId
            ? $project->sprints()->where('id', $sprintId)->first()
            : ($project->activeSprint ?? $project->sprints()->first());

        $sprints = $project->sprints;
        $issues = $sprint ? $sprint->issues()->with(['assignee', 'component'])->get() : collect();

        $usesPoints = $issues->sum('story_points') > 0;
        $metricUnit = $usesPoints ? 'SP' : 'Tiket';
        $weightFn = fn ($item) => $usesPoints ? (int) ($item->story_points ?? 0) : 1;

        $totalStoryPoints = $issues->sum($weightFn);
        $doneIssues = $issues->where('status', IssueStatus::DONE);
        $completedStoryPoints = $doneIssues->sum($weightFn);

        $timelineData = [];
        if ($sprint && $sprint->start_date && $sprint->end_date) {
            $startDate = Carbon::parse($sprint->start_date)->startOfDay();
            $endDate = Carbon::parse($sprint->end_date)->startOfDay();
            $totalDays = max(1, (int) $startDate->diffInDays($endDate));

            for ($i = 0; $i <= $totalDays; $i++) {
                $dayDate = (clone $startDate)->addDays($i);
                $scopeByDay = $issues->filter(function ($issue) use ($dayDate, $startDate) {
                    if (!$issue->created_at) {
                        return true;
                    }
                    return Carbon::parse($issue->created_at)->lte($dayDate->copy()->endOfDay()) || $dayDate->equalTo($startDate);
                })->sum($weightFn);

                $completedSoFar = null;
                if ($dayDate->lte(Carbon::today())) {
                    $completedSoFar = $doneIssues->filter(function ($issue) use ($dayDate, $startDate) {
                        if (!$issue->updated_at) {
                            return false;
                        }
                        $completedAt = Carbon::parse($issue->updated_at);
                        return $completedAt->lte($dayDate->copy()->endOfDay()) || ($dayDate->equalTo($startDate) && $completedAt->lt($startDate));
                    })->sum($weightFn);
                }

                $timelineData[] = [
                    'day' => 'Hari ' . ($i + 1),
                    'date' => $dayDate->format('d M'),
                    'scope' => max($scopeByDay, $completedSoFar ?? 0),
                    'completed' => $completedSoFar,
                ];
            }
        }

        return view('reports.burnup', compact(
            'project',
            'sprint',
            'sprints',
            'issues',
            'timelineData',
            'totalStoryPoints',
            'completedStoryPoints',
            'metricUnit'
        ));
    }

    public function velocity(Project $project): View
    {
        $sprints = $project->sprints()->with('issues')->take(8)->get();

        $velocityData = [];
        foreach ($sprints as $sprint) {
            $totalPoints = (int) $sprint->issues->sum('story_points');
            $completedPoints = (int) $sprint->issues->where('status', IssueStatus::DONE)->sum('story_points');
            $totalCount = $sprint->issues->count();
            $completedCount = $sprint->issues->where('status', IssueStatus::DONE)->count();

            $velocityData[] = [
                'name' => $sprint->name,
                'status' => $sprint->status->label(),
                'commitment' => $totalPoints,
                'completed' => $completedPoints,
                'total_issues' => $totalCount,
                'completed_issues' => $completedCount,
            ];
        }

        $avgVelocity = count($velocityData) > 0
            ? round(collect($velocityData)->avg('completed'), 1)
            : 0;

        return view('reports.velocity', compact('project', 'velocityData', 'avgVelocity'));
    }

    public function cumulativeFlow(Project $project): View
    {
        $issues = $project->issues()->with(['assignee', 'sprint'])->where('type', '!=', IssueType::EPIC->value)->get();

        $statusCounts = [
            'to_do' => $issues->where('status', IssueStatus::TO_DO)->count(),
            'in_progress' => $issues->where('status', IssueStatus::IN_PROGRESS)->count(),
            'in_review' => $issues->where('status', IssueStatus::IN_REVIEW)->count(),
            'done' => $issues->where('status', IssueStatus::DONE)->count(),
        ];

        return view('reports.cumulative_flow', compact('project', 'statusCounts', 'issues'));
    }

    public function epic(Project $project): View
    {
        $epics = $project->epics()->with(['epicIssues.assignee', 'epicIssues.component'])->get();

        return view('reports.epic', compact('project', 'epics'));
    }
}

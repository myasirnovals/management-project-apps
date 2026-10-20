<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\SprintStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Sprint;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        try {
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        } catch (\Exception $e) {
            $monthDate = now()->startOfMonth();
            $selectedMonth = $monthDate->format('Y-m');
        }

        $prevMonth = $monthDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthDate->copy()->addMonth()->format('Y-m');

        $selectedProjectId = $request->input('project_id');

        $projects = Project::orderBy('name')->get();

        // Calendar grid boundaries (start of week: Monday)
        $startOfGrid = $monthDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfGrid = $monthDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Fetch Sprints overlapping grid
        $sprintsQuery = Sprint::with('project')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where(function ($q) use ($startOfGrid, $endOfGrid) {
                $q->whereBetween('start_date', [$startOfGrid, $endOfGrid])
                  ->orWhereBetween('end_date', [$startOfGrid, $endOfGrid])
                  ->orWhere(function ($sub) use ($startOfGrid, $endOfGrid) {
                      $sub->where('start_date', '<=', $startOfGrid)
                          ->where('end_date', '>=', $endOfGrid);
                  });
            });

        if ($selectedProjectId) {
            $sprintsQuery->where('project_id', $selectedProjectId);
        }

        $sprints = $sprintsQuery->get();

        // Fetch Issues with due date in grid
        $issuesQuery = Issue::with(['project', 'assignee'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startOfGrid->toDateString(), $endOfGrid->toDateString()]);

        if ($selectedProjectId) {
            $issuesQuery->where('project_id', $selectedProjectId);
        }

        $issues = $issuesQuery->get();

        // Group events by Y-m-d
        $eventsByDate = [];
        $period = CarbonPeriod::create($startOfGrid, $endOfGrid);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $eventsByDate[$dateStr] = [
                'sprints' => [],
                'issues' => [],
            ];
        }

        foreach ($sprints as $sprint) {
            $sprintStart = $sprint->start_date ? $sprint->start_date->copy()->startOfDay() : null;
            $sprintEnd = $sprint->end_date ? $sprint->end_date->copy()->endOfDay() : null;

            if ($sprintStart && $sprintEnd) {
                foreach ($period as $date) {
                    if ($date->betweenIncluded($sprintStart, $sprintEnd)) {
                        $dateStr = $date->toDateString();
                        if (isset($eventsByDate[$dateStr])) {
                            $eventsByDate[$dateStr]['sprints'][] = $sprint;
                        }
                    }
                }
            }
        }

        foreach ($issues as $issue) {
            $dueStr = $issue->due_date ? Carbon::parse($issue->due_date)->toDateString() : null;
            if ($dueStr && isset($eventsByDate[$dueStr])) {
                $eventsByDate[$dueStr]['issues'][] = $issue;
            }
        }

        // Active sprints and upcoming deadlines for summary
        $activeSprints = Sprint::with('project')
            ->where('status', SprintStatus::ACTIVE->value)
            ->when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))
            ->get();

        $upcomingIssues = Issue::with('project')
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now()->toDateString())
            ->where('status', '!=', IssueStatus::DONE->value)
            ->when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Load Google Calendar settings
        $settingsFile = storage_path('app/calendar_settings.json');
        $calendarSettings = [
            'calendar_id' => '',
            'timezone' => 'Asia/Jakarta',
            'view_mode' => 'MONTH',
            'custom_embed_url' => '',
        ];

        if (file_exists($settingsFile)) {
            $saved = json_decode(file_get_contents($settingsFile), true);
            if (is_array($saved)) {
                $calendarSettings = array_merge($calendarSettings, $saved);
            }
        }

        // Generate embed URL
        $calendarId = trim($calendarSettings['calendar_id'] ?? '');
        $timezone = $calendarSettings['timezone'] ?? 'Asia/Jakarta';
        $viewMode = $calendarSettings['view_mode'] ?? 'MONTH';

        if (!empty($calendarSettings['custom_embed_url'])) {
            $embedUrl = $calendarSettings['custom_embed_url'];
        } elseif (!empty($calendarId)) {
            $cid = urlencode($calendarId);
            $tz = urlencode($timezone);
            $embedUrl = "https://calendar.google.com/calendar/embed?src={$cid}&ctz={$tz}&mode={$viewMode}&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=1";
        } else {
            // Default public demonstration calendar (Indonesian Holidays & workspace default)
            $cid = urlencode('id.indonesian#holiday@group.v.calendar.google.com');
            $tz = urlencode($timezone);
            $embedUrl = "https://calendar.google.com/calendar/embed?src={$cid}&ctz={$tz}&mode={$viewMode}&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=1";
        }

        // Direct action URLs
        $createEventUrl = !empty($calendarId)
            ? "https://calendar.google.com/calendar/u/0/r/eventedit?src=" . urlencode($calendarId)
            : "https://calendar.google.com/calendar/u/0/r/eventedit";

        $openCalendarUrl = !empty($calendarId)
            ? "https://calendar.google.com/calendar/u/0/r?cid=" . urlencode($calendarId)
            : "https://calendar.google.com/calendar";

        $isProjectManager = $this->isProjectManager();

        return view('calendar.index', compact(
            'monthDate',
            'selectedMonth',
            'prevMonth',
            'nextMonth',
            'projects',
            'selectedProjectId',
            'startOfGrid',
            'endOfGrid',
            'period',
            'eventsByDate',
            'activeSprints',
            'upcomingIssues',
            'calendarSettings',
            'embedUrl',
            'createEventUrl',
            'openCalendarUrl',
            'isProjectManager'
        ));
    }

    /**
     * Display Google Calendar and timeline focused specifically for a single project.
     */
    public function projectTimeline(Request $request, Project $project): View
    {
        // Load Google Calendar settings
        $settingsFile = storage_path('app/calendar_settings.json');
        $calendarSettings = [
            'calendar_id' => '',
            'timezone' => 'Asia/Jakarta',
            'view_mode' => 'MONTH',
            'custom_embed_url' => '',
        ];

        if (file_exists($settingsFile)) {
            $saved = json_decode(file_get_contents($settingsFile), true);
            if (is_array($saved)) {
                $calendarSettings = array_merge($calendarSettings, $saved);
            }
        }

        $viewMode = $request->input('mode', $calendarSettings['view_mode'] ?? 'MONTH');
        $calendarId = trim($calendarSettings['calendar_id'] ?? '');
        $timezone = $calendarSettings['timezone'] ?? 'Asia/Jakarta';

        if (!empty($calendarSettings['custom_embed_url'])) {
            $embedUrl = $calendarSettings['custom_embed_url'];
        } elseif (!empty($calendarId)) {
            $cid = urlencode($calendarId);
            $tz = urlencode($timezone);
            $embedUrl = "https://calendar.google.com/calendar/embed?src={$cid}&ctz={$tz}&mode={$viewMode}&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=1";
        } else {
            $cid = urlencode('id.indonesian#holiday@group.v.calendar.google.com');
            $tz = urlencode($timezone);
            $embedUrl = "https://calendar.google.com/calendar/embed?src={$cid}&ctz={$tz}&mode={$viewMode}&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=1";
        }

        // Direct action URLs with project context
        $eventTitle = urlencode("[{$project->key}] Jadwal Proyek {$project->name}");
        $createEventUrl = !empty($calendarId)
            ? "https://calendar.google.com/calendar/u/0/r/eventedit?text={$eventTitle}&src=" . urlencode($calendarId)
            : "https://calendar.google.com/calendar/u/0/r/eventedit?text={$eventTitle}";

        $openCalendarUrl = !empty($calendarId)
            ? "https://calendar.google.com/calendar/u/0/r?cid=" . urlencode($calendarId)
            : "https://calendar.google.com/calendar";

        // Fetch project sprints and issues
        $sprints = $project->sprints()->orderByDesc('created_at')->get();
        $activeSprint = $sprints->where('status', SprintStatus::ACTIVE)->first();

        $issuesWithDue = $project->issues()
            ->whereNotNull('due_date')
            ->with(['assignee', 'sprint'])
            ->orderBy('due_date')
            ->get();

        $upcomingIssues = $issuesWithDue->where('status', '!=', IssueStatus::DONE->value);
        $doneIssues = $issuesWithDue->where('status', IssueStatus::DONE->value);

        $isProjectManager = $this->isProjectManager();

        return view('projects.timeline', compact(
            'project',
            'calendarSettings',
            'embedUrl',
            'createEventUrl',
            'openCalendarUrl',
            'viewMode',
            'sprints',
            'activeSprint',
            'issuesWithDue',
            'upcomingIssues',
            'doneIssues',
            'isProjectManager'
        ));
    }

    /**
     * Update Google Calendar configuration settings (PM only).
     */
    public function updateSettings(Request $request)
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Hanya Project Manager atau Scrum Master yang memiliki wewenang untuk mengubah pengaturan kalender proyek.');
        }

        $validated = $request->validate([
            'calendar_id' => ['nullable', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:100'],
            'view_mode' => ['required', 'in:MONTH,WEEK,AGENDA'],
            'custom_embed_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $settingsFile = storage_path('app/calendar_settings.json');
        $directory = dirname($settingsFile);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($settingsFile, json_encode($validated, JSON_PRETTY_PRINT));

        return redirect()->route('calendar.index')
            ->with('success', 'Konfigurasi Google Calendar bersama berhasil diperbarui!');
    }
}

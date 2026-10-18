<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\SprintStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ReleaseVersion;
use App\Models\Sprint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BacklogController extends Controller
{
    /**
     * Display the Project Backlog page.
     */
    public function index(Request $request, Project $project): View
    {
        // Epics for sidebar filter (Modul II.A & VI)
        $epics = $project->epics()->withCount('epicIssues')->get();

        $selectedEpicId = $request->query('epic_id');

        // Sprints (Active and Planning)
        $sprints = $project->sprints()
            ->with(['issues' => function ($q) use ($selectedEpicId) {
                if ($selectedEpicId) {
                    $q->where('parent_id', $selectedEpicId);
                }
                $q->with(['assignee', 'component', 'parent', 'version'])->orderBy('order_index');
            }])
            ->whereIn('status', [SprintStatus::ACTIVE->value, SprintStatus::PLANNING->value])
            ->orderBy('status') // Active first
            ->get();

        // Backlog issues (no sprint assigned)
        $backlogQuery = Issue::with(['assignee', 'component', 'parent', 'version'])
            ->where('project_id', $project->id)
            ->whereNull('sprint_id')
            ->where('type', '!=', IssueType::EPIC->value)
            ->where('type', '!=', IssueType::SUBTASK->value);

        if ($selectedEpicId) {
            $backlogQuery->where('parent_id', $selectedEpicId);
        }

        $backlogIssues = $backlogQuery->orderBy('order_index')->get();

        $versions = $project->versions;
        $components = $project->components;
        $users = $project->members;

        return view('backlog.index', compact(
            'project',
            'epics',
            'selectedEpicId',
            'sprints',
            'backlogIssues',
            'versions',
            'components',
            'users'
        ));
    }

    /**
     * Move an issue to a sprint or back to backlog.
     */
    public function moveToSprint(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'issue_id' => ['required', 'exists:issues,id'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
        ]);

        $issue = Issue::where('project_id', $project->id)->findOrFail($validated['issue_id']);
        $issue->sprint_id = $validated['sprint_id'] ? (int) $validated['sprint_id'] : null;
        $issue->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Issue {$issue->issue_key} berhasil dipindahkan.",
            ]);
        }

        return back()->with('success', "Issue {$issue->issue_key} berhasil dipindahkan.");
    }

    /**
     * Clean Up backlog issues (e.g. issues older than X days or completed) (Modul VI.B).
     */
    public function cleanUp(Request $request, Project $project): RedirectResponse
    {
        $days = (int) $request->input('days', 90);
        $thresholdDate = Carbon::now()->subDays($days);

        $deletedCount = Issue::where('project_id', $project->id)
            ->whereNull('sprint_id')
            ->where('status', IssueStatus::DONE->value)
            ->where('updated_at', '<=', $thresholdDate)
            ->delete();

        return back()->with('success', "Clean Up Berhasil: {$deletedCount} backlog yang sudah selesai lebih dari {$days} hari telah dibersihkan.");
    }

    /**
     * Bulk Action on Backlog Issues (Modul VI.B Gambar 6.14 - 6.18).
     */
    public function bulkAction(Request $request, Project $project): RedirectResponse
    {
        $action = $request->input('action'); // 'delete', 'change_status', 'move_sprint'
        $issueIds = $request->input('issue_ids', []);

        if (empty($issueIds)) {
            return back()->with('error', 'Tidak ada issue yang dipilih untuk aksi massal.');
        }

        if ($action === 'delete') {
            Issue::where('project_id', $project->id)->whereIn('id', $issueIds)->delete();
            return back()->with('success', count($issueIds) . ' issue berhasil dihapus secara massal.');
        }

        if ($action === 'change_status') {
            $status = $request->input('target_status');
            if ($status) {
                Issue::where('project_id', $project->id)->whereIn('id', $issueIds)->update([
                    'status' => $status,
                ]);
                return back()->with('success', count($issueIds) . " issue berhasil diubah statusnya menjadi {$status}.");
            }
        }

        if ($action === 'move_sprint') {
            $sprintId = $request->input('target_sprint_id') ?: null;
            Issue::where('project_id', $project->id)->whereIn('id', $issueIds)->update([
                'sprint_id' => $sprintId,
            ]);
            return back()->with('success', count($issueIds) . ' issue berhasil dipindahkan.');
        }

        return back();
    }

    /**
     * Export Backlog to CSV (Modul VI.B Gambar 6.13).
     */
    public function exportCsv(Project $project): StreamedResponse
    {
        $fileName = "backlog-{$project->key}-" . date('Ymd_His') . ".csv";

        $issues = Issue::with(['assignee', 'reporter', 'component', 'sprint', 'version'])
            ->where('project_id', $project->id)
            ->orderBy('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return Response::stream(function () use ($issues) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Key', 'Type', 'Title', 'Status', 'Priority', 'Story Points', 'Assignee', 'Reporter', 'Component', 'Sprint', 'Fix Version', 'Created At']);

            foreach ($issues as $issue) {
                fputcsv($handle, [
                    $issue->issue_key,
                    $issue->type->label(),
                    $issue->title,
                    $issue->status->label(),
                    $issue->priority->label(),
                    $issue->story_points ?? 0,
                    $issue->assignee ? $issue->assignee->name : 'Unassigned',
                    $issue->reporter ? $issue->reporter->name : '',
                    $issue->component ? $issue->component->name : '',
                    $issue->sprint ? $issue->sprint->name : 'Backlog',
                    $issue->version ? $issue->version->name : '',
                    $issue->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

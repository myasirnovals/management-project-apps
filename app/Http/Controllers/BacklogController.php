<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\SprintStatus;
use App\Models\Issue;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BacklogController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $epics = $project->epics()->withCount('epicIssues')->get();
        $selectedEpicId = $request->query('epic_id');

        $sprints = $project->sprints()
            ->with(['issues' => function ($q) use ($selectedEpicId) {
                if ($selectedEpicId) {
                    $q->where('parent_id', $selectedEpicId);
                }
                $q->with(['assignee', 'component', 'parent', 'version'])->orderBy('order_index');
            }])
            ->whereIn('status', [SprintStatus::ACTIVE->value, SprintStatus::PLANNING->value])
            ->orderBy('status')
            ->get();

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

    public function moveToSprint(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (!Auth::user()?->canMutateIssues()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Peran Viewer hanya memiliki hak baca (read-only).'], 403);
            }
            abort(403, 'Akses ditolak. Peran Viewer hanya memiliki hak akses lihat (read-only).');
        }

        $validated = $request->validate([
            'issue_id' => ['required', 'exists:issues,id'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
        ]);

        $issue = Issue::where('project_id', $project->id)->findOrFail($validated['issue_id']);

        if (!empty($validated['sprint_id'])) {
            $sprint = $project->sprints()->where('id', $validated['sprint_id'])->firstOrFail();
            $issue->sprint_id = $sprint->id;
        } else {
            $issue->sprint_id = null;
        }

        $issue->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Issue {$issue->issue_key} berhasil dipindahkan.",
            ]);
        }

        return back()->with('success', "Issue {$issue->issue_key} berhasil dipindahkan.");
    }

    public function cleanUp(Request $request, Project $project): RedirectResponse
    {
        if (!Auth::user()?->canMutateIssues()) {
            abort(403, 'Akses ditolak. Peran Viewer hanya memiliki hak akses lihat (read-only).');
        }

        $days = max(1, (int) $request->input('days', 90));
        $thresholdDate = Carbon::now()->subDays($days);

        $deletedCount = Issue::where('project_id', $project->id)
            ->whereNull('sprint_id')
            ->where('status', IssueStatus::DONE->value)
            ->where('updated_at', '<=', $thresholdDate)
            ->delete();

        return back()->with('success', "Pembersihan selesai: {$deletedCount} tiket backlog berstatus selesai yang berumur lebih dari {$days} hari telah dihapus.");
    }

    public function bulkAction(Request $request, Project $project): RedirectResponse
    {
        if (!Auth::user()?->canMutateIssues()) {
            abort(403, 'Akses ditolak. Peran Viewer hanya memiliki hak akses lihat (read-only).');
        }

        $action = $request->input('action');
        $issueIds = $request->input('issue_ids', []);

        if (empty($issueIds) || !is_array($issueIds)) {
            return back()->with('error', 'Tidak ada issue yang dipilih untuk aksi massal.');
        }

        if ($action === 'delete') {
            DB::transaction(function () use ($project, $issueIds) {
                Issue::where('project_id', $project->id)->whereIn('id', $issueIds)->delete();
            });
            return back()->with('success', count($issueIds) . ' issue berhasil dihapus secara massal.');
        }

        if ($action === 'change_status') {
            $validated = $request->validate([
                'target_status' => ['required', Rule::enum(IssueStatus::class)],
            ], [
                'target_status.required' => 'Pilih status tujuan terlebih dahulu sebelum menjalankan perubahan status massal.',
            ]);

            $statusEnum = IssueStatus::from($validated['target_status']);
            DB::transaction(function () use ($project, $issueIds, $statusEnum) {
                Issue::where('project_id', $project->id)->whereIn('id', $issueIds)->update([
                    'status' => $statusEnum->value,
                ]);
            });
            return back()->with('success', count($issueIds) . " issue berhasil diubah statusnya menjadi {$statusEnum->label()}.");
        }

        if ($action === 'move_sprint') {
            $rawSprint = $request->input('target_sprint_id');
            $sprintId = null;

            if (!empty($rawSprint) && $rawSprint !== 'backlog') {
                $sprint = $project->sprints()->where('id', $rawSprint)->first();
                if (!$sprint) {
                    return back()->with('error', 'Sprint tujuan tidak valid untuk proyek ini.');
                }
                $sprintId = $sprint->id;
            }

            DB::transaction(function () use ($project, $issueIds, $sprintId) {
                Issue::where('project_id', $project->id)->whereIn('id', $issueIds)->update([
                    'sprint_id' => $sprintId,
                ]);
            });

            $destLabel = $sprintId ? 'Sprint terpilih' : 'Backlog';
            return back()->with('success', count($issueIds) . " issue berhasil dipindahkan ke {$destLabel}.");
        }

        return back()->with('error', 'Pilih jenis aksi massal yang valid.');
    }

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

<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\SprintStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Sprint;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SprintController extends Controller
{
    /**
     * Store a newly created sprint in storage.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageSprints()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Scrum Master yang berwenang membuat Sprint baru.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:4'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $endDate = !empty($validated['end_date']) ? Carbon::parse($validated['end_date']) : null;

        if ($startDate && !$endDate && !empty($validated['duration_weeks'])) {
            $endDate = (clone $startDate)->addWeeks((int) $validated['duration_weeks']);
        }

        $sprint = Sprint::create([
            'project_id' => $project->id,
            'name' => $validated['name'],
            'goal' => $validated['goal'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => SprintStatus::PLANNING,
        ]);

        return redirect()->route('projects.backlog', $project)
            ->with('success', "Sprint '{$sprint->name}' berhasil dibuat!");
    }

    /**
     * Start a sprint (Modul III.B Gambar 3.7).
     */
    public function start(Request $request, Project $project, Sprint $sprint): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageSprints()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Scrum Master yang berwenang memulai Sprint.');
        }

        // Check if there is already an active sprint in this project
        $activeSprint = $project->sprints()->where('status', SprintStatus::ACTIVE->value)->first();
        if ($activeSprint && $activeSprint->id !== $sprint->id) {
            return back()->with('error', "Ada sprint aktif yang sedang berjalan ('{$activeSprint->name}'). Harap selesaikan sprint tersebut terlebih dahulu.");
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:4'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $sprint->update([
            'name' => $validated['name'],
            'goal' => $validated['goal'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => SprintStatus::ACTIVE,
        ]);

        return redirect()->route('projects.board', $project)
            ->with('success', "Sprint '{$sprint->name}' telah dimulai! Selamat bekerja menuju Sprint Goal.");
    }

    /**
     * Complete an active sprint.
     */
    public function complete(Request $request, Project $project, Sprint $sprint): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageSprints()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Scrum Master yang berwenang menyelesaikan Sprint.');
        }

        $incompleteIssues = $sprint->issues()->where('status', '!=', IssueStatus::DONE->value)->get();
        $moveTarget = $request->input('incomplete_action', 'backlog'); // 'backlog' or next sprint_id

        DB::transaction(function () use ($sprint, $incompleteIssues, $moveTarget) {
            if ($moveTarget === 'backlog') {
                foreach ($incompleteIssues as $issue) {
                    $issue->update(['sprint_id' => null]);
                }
            } elseif (is_numeric($moveTarget)) {
                foreach ($incompleteIssues as $issue) {
                    $issue->update(['sprint_id' => (int) $moveTarget]);
                }
            }

            $sprint->update([
                'status' => SprintStatus::COMPLETED,
            ]);
        });

        return redirect()->route('projects.backlog', $project)
            ->with('success', "Sprint '{$sprint->name}' telah diselesaikan! Poin cerita yang selesai telah tercatat dalam laporan.");
    }

    /**
     * Delete a sprint.
     */
    public function destroy(Project $project, Sprint $sprint): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageSprints()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Scrum Master yang berwenang menghapus Sprint.');
        }

        // Move any issues in this sprint back to backlog
        $sprint->issues()->update(['sprint_id' => null]);
        $sprint->delete();

        return redirect()->route('projects.backlog', $project)
            ->with('success', "Sprint telah dihapus dan semua issue dikembalikan ke Backlog.");
    }
}

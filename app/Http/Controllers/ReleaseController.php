<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\VersionStatus;
use App\Models\Project;
use App\Models\ReleaseVersion;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReleaseController extends Controller
{
    public function index(Project $project): View
    {
        $versions = $project->versions()
            ->with(['issues.assignee', 'issues.component'])
            ->get();

        return view('releases.index', compact('project', 'versions'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageReleases()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Product Owner yang berwenang membuat versi rilis.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'release_date' => ['nullable', 'date'],
        ]);

        $version = $project->versions()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'release_date' => $validated['release_date'],
            'status' => VersionStatus::UNRELEASED,
        ]);

        return back()->with('success', "Versi Rilis '{$version->name}' berhasil dibuat!");
    }

    public function update(Request $request, Project $project, ReleaseVersion $version): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageReleases()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Product Owner yang berwenang memperbarui versi rilis.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'release_date' => ['nullable', 'date'],
            'status' => ['required', 'in:unreleased,released,archived'],
        ]);

        $version->update($validated);

        return back()->with('success', "Versi '{$version->name}' berhasil diperbarui.");
    }

    /**
     * Perform release (Modul V.L Gambar 5.14 & 5.15).
     */
    public function release(Request $request, Project $project, ReleaseVersion $version): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageReleases()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Product Owner yang berwenang mengeksekusi rilis.');
        }

        $unresolvedIssues = $version->issues()->where('status', '!=', IssueStatus::DONE->value)->count();

        // Release Scope Lock check
        if ($unresolvedIssues > 0 && !$request->boolean('ignore_unresolved')) {
            return back()->with('warning', "Perhatian: Masih ada {$unresolvedIssues} issue yang belum selesai pada versi ini. Anda dapat mencentang 'Abaikan & Tetap Rilis' untuk melanjutkan.");
        }

        $version->update([
            'status' => VersionStatus::RELEASED,
            'release_date' => Carbon::now(),
        ]);

        return back()->with('success', "Versi '{$version->name}' berhasil dirilis (RELEASED)!");
    }

    public function destroy(Project $project, ReleaseVersion $version): RedirectResponse
    {
        if (!\Illuminate\Support\Facades\Auth::user()?->canManageReleases()) {
            abort(403, 'Akses ditolak. Hanya Project Manager atau Product Owner yang berwenang menghapus versi rilis.');
        }

        $name = $version->name;
        // Unlink issues from version
        $version->issues()->update(['version_id' => null]);
        $version->delete();

        return back()->with('success', "Versi '{$name}' telah dihapus.");
    }
}

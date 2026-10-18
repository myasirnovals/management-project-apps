<?php

namespace App\Http\Controllers;

use App\Enums\IssueStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/', 'unique:projects,key'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:scrum,kanban'],
        ], [
            'key.regex' => 'Project Key harus huruf besar dan angka tanpa spasi (contoh: PRK, WEB, SHOP).',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'key' => strtoupper($validated['key']),
            'description' => $validated['description'],
            'type' => $validated['type'],
            'owner_id' => Auth::id(),
            'status' => 'active',
        ]);

        // Attach owner as project manager member
        $project->members()->attach(Auth::id(), ['role' => UserRole::PROJECT_MANAGER->value]);

        return redirect()->route('projects.board', $project)
            ->with('success', "Proyek '{$project->name}' ({$project->key}) berhasil dibuat!");
    }

    public function show(Project $project): RedirectResponse
    {
        return redirect()->route('projects.board', $project);
    }

    public function edit(Project $project): View
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:scrum,kanban'],
            'status' => ['required', 'in:active,archived'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.edit', $project)
            ->with('success', 'Pengaturan proyek berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $name = $project->name;
        $project->delete();

        return redirect()->route('dashboard')
            ->with('success', "Proyek '{$name}' telah dihapus.");
    }

    public function addMember(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'string'],
        ]);

        $project->members()->syncWithoutDetaching([
            $validated['user_id'] => ['role' => $validated['role']],
        ]);

        return back()->with('success', 'Anggota tim berhasil ditambahkan ke proyek.');
    }

    public function removeMember(Project $project, User $user): RedirectResponse
    {
        $project->members()->detach($user->id);
        return back()->with('success', 'Anggota tim telah dikeluarkan dari proyek.');
    }
}

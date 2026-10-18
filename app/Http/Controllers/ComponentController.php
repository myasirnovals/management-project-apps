<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComponentController extends Controller
{
    public function index(Project $project): View
    {
        $components = $project->components()
            ->with(['lead'])
            ->withCount('issues')
            ->latest()
            ->get();

        $users = $project->members;

        return view('components.index', compact('project', 'components', 'users'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'exists:users,id'],
        ]);

        $project->components()->create($validated);

        return back()->with('success', "Komponen '{$validated['name']}' berhasil ditambahkan.");
    }

    public function update(Request $request, Project $project, Component $component): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'exists:users,id'],
        ]);

        $component->update($validated);

        return back()->with('success', "Komponen '{$component->name}' berhasil diperbarui.");
    }

    public function destroy(Project $project, Component $component): RedirectResponse
    {
        $name = $component->name;
        $component->delete();

        return back()->with('success', "Komponen '{$name}' telah dihapus.");
    }
}

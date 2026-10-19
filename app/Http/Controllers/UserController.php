<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the team members.
     */
    public function index(): View
    {
        $users = User::withCount(['assignedIssues', 'projects'])
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Store a newly created team member in storage.
     * (Admin / Project Manager invites/registers users)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'job_title' => $validated['job_title'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Anggota tim '{$user->name}' ({$user->email}) berhasil ditambahkan ke sistem!");
    }

    /**
     * Update the specified team member.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'job_title' => $validated['job_title'],
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "Data anggota tim '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun '{$name}' telah dihapus dari sistem.");
    }
}

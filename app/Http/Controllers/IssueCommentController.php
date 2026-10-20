<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueComment;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueCommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Project $project, Issue $issue): JsonResponse|RedirectResponse
    {
        if (!Auth::user()?->canMutateIssues()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Peran Viewer tidak dapat memberikan komentar.'], 403);
            }
            abort(403, 'Akses ditolak. Peran Viewer hanya memiliki hak akses lihat (read-only).');
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $comment = IssueComment::create([
            'issue_id' => $issue->id,
            'user_id' => Auth::id(),
            'content' => trim($validated['content']),
        ]);

        $comment->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan.',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_id' => $comment->user_id,
                    'user_name' => $comment->user?->name ?? 'User',
                    'user_role' => $comment->user?->role ?? 'developer',
                    'user_initials' => $comment->user?->initials() ?? 'U',
                    'created_at_human' => $comment->created_at->diffForHumans(),
                    'can_delete' => Auth::id() === $comment->user_id || $this->isProjectManager(),
                ],
            ]);
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Request $request, Project $project, Issue $issue, IssueComment $comment): JsonResponse|RedirectResponse
    {
        if ($comment->issue_id !== $issue->id) {
            abort(404, 'Komentar tidak ditemukan pada issue ini.');
        }

        if (Auth::id() !== $comment->user_id && !$this->isProjectManager()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus komentar ini.');
        }

        $comment->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}

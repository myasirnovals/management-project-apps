<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueAttachment;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IssueAttachmentController extends Controller
{
    /**
     * Upload an attachment to an issue.
     */
    public function store(Request $request, Project $project, Issue $issue): JsonResponse|RedirectResponse
    {
        if (!Auth::user()?->canMutateIssues()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Peran Viewer tidak dapat mengunggah berkas.'], 403);
            }
            abort(403, 'Akses ditolak. Peran Viewer hanya memiliki hak akses lihat (read-only).');
        }

        $request->validate([
            'file' => ['required', 'file', 'max:25600'], // max 25MB
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getClientMimeType() ?: $uploadedFile->getClientOriginalExtension();
        $fileSize = $uploadedFile->getSize();

        $path = $uploadedFile->store("attachments/{$project->key}/{$issue->issue_key}", 'public');

        $attachment = IssueAttachment::create([
            'issue_id' => $issue->id,
            'user_id' => Auth::id(),
            'filename' => $originalName,
            'file_path' => $path,
            'file_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        $attachment->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Berkas berhasil diunggah.',
                'attachment' => [
                    'id' => $attachment->id,
                    'filename' => $attachment->filename,
                    'url' => $attachment->url(),
                    'download_url' => route('projects.issues.attachments.download', [$project, $issue, $attachment]),
                    'is_image' => $attachment->isImage(),
                    'file_type' => $attachment->file_type,
                    'formatted_size' => $attachment->formattedSize(),
                    'user_name' => $attachment->user?->name ?? 'User',
                    'user_initials' => $attachment->user?->initials() ?? 'U',
                    'created_at_human' => $attachment->created_at->diffForHumans(),
                    'can_delete' => Auth::id() === $attachment->user_id || $this->isProjectManager(),
                ],
            ]);
        }

        return back()->with('success', 'Berkas berhasil diunggah.');
    }

    /**
     * Download the specified attachment.
     */
    public function download(Project $project, Issue $issue, IssueAttachment $attachment): StreamedResponse
    {
        if ($attachment->issue_id !== $issue->id) {
            abort(404, 'Berkas tidak ditemukan pada issue ini.');
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File fisik tidak ditemukan pada server.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->download($attachment->file_path, $attachment->filename);
    }

    /**
     * Delete the specified attachment.
     */
    public function destroy(Request $request, Project $project, Issue $issue, IssueAttachment $attachment): JsonResponse|RedirectResponse
    {
        if ($attachment->issue_id !== $issue->id) {
            abort(404, 'Berkas tidak ditemukan pada issue ini.');
        }

        if (Auth::id() !== $attachment->user_id && !$this->isProjectManager()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus berkas ini.');
        }

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Berkas berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Berkas berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ProjectFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectFeedbackController extends Controller
{
    /**
     * Display a listing of feedback and suggestions for the project.
     */
    public function index(Request $request, Project $project): View
    {
        $categoryFilter = $request->input('category');
        $statusFilter = $request->input('status');

        $query = $project->feedbacks()
            ->with(['user', 'responder', 'convertedIssue'])
            ->latest();

        if (!empty($categoryFilter) && $categoryFilter !== 'all') {
            $query->where('category', $categoryFilter);
        }

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $feedbacks = $query->paginate(12)->withQueryString();

        // Calculate statistics
        $allFeedbacks = $project->feedbacks;
        $stats = [
            'total' => $allFeedbacks->count(),
            'pending' => $allFeedbacks->where('status', 'pending')->count(),
            'planned' => $allFeedbacks->whereIn('status', ['planned', 'reviewed'])->count(),
            'implemented' => $allFeedbacks->where('status', 'implemented')->count(),
        ];

        $isProjectManager = $this->isProjectManager();

        return view('projects.feedback.index', compact(
            'project',
            'feedbacks',
            'stats',
            'categoryFilter',
            'statusFilter',
            'isProjectManager'
        ));
    }

    /**
     * Store a newly created feedback/suggestion.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'in:feature_request,improvement,bug_report,general'],
        ], [
            'title.required' => 'Judul saran / fitur wajib diisi.',
            'description.required' => 'Uraian saran / masukan wajib diisi.',
            'category.required' => 'Kategori saran wajib dipilih.',
        ]);

        $feedback = $project->feedbacks()->create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'status' => 'pending',
        ]);

        return redirect()->route('projects.feedback.index', $project)
            ->with('success', "Terima kasih! Saran '{$feedback->title}' telah berhasil dikirimkan kepada Project Manager.");
    }

    /**
     * Update PM response and status (Project Manager only).
     */
    public function updateResponse(Request $request, Project $project, ProjectFeedback $feedback): RedirectResponse
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Hanya Project Manager yang dapat memberikan tanggapan dan mengubah status saran.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,planned,rejected,implemented'],
            'pm_response' => ['nullable', 'string', 'max:5000'],
        ]);

        $feedback->update([
            'status' => $validated['status'],
            'pm_response' => $validated['pm_response'],
            'pm_id' => Auth::id(),
            'responded_at' => now(),
        ]);

        return redirect()->route('projects.feedback.index', $project)
            ->with('success', "Tanggapan untuk saran '{$feedback->title}' berhasil diperbarui.");
    }

    /**
     * Convert an approved feedback into a Backlog Issue (Project Manager only).
     */
    public function convertToIssue(Request $request, Project $project, ProjectFeedback $feedback): RedirectResponse
    {
        if (!$this->isProjectManager()) {
            abort(403, 'Hanya Project Manager yang dapat mengonversi saran menjadi tiket Issue.');
        }

        if ($feedback->converted_issue_id) {
            return redirect()->route('projects.feedback.index', $project)
                ->with('info', 'Saran ini telah dikonversi menjadi tiket Issue sebelumnya.');
        }

        // Determine issue type (can be customized from request or defaulted from category)
        $requestedType = $request->input('issue_type');
        $issueType = $requestedType ? IssueType::tryFrom($requestedType) : null;
        if (!$issueType) {
            $issueType = match ($feedback->category) {
                'bug_report' => IssueType::BUG,
                'feature_request', 'improvement' => IssueType::STORY,
                default => IssueType::TASK,
            };
        }

        $requestedPriority = $request->input('priority');
        $priority = $requestedPriority ? (IssuePriority::tryFrom($requestedPriority) ?? IssuePriority::MEDIUM) : IssuePriority::MEDIUM;
        $storyPoints = $request->filled('story_points') ? (int) $request->input('story_points') : null;

        $issueKey = $project->generateNextIssueKey();
        $maxOrder = Issue::where('project_id', $project->id)->max('order_index') ?? 0;

        $desc = "💡 **Masukan dari Anggota:** " . ($feedback->user->name ?? 'Anggota Tim') . "\n\n"
              . $feedback->description;

        if (!empty($feedback->pm_response)) {
            $desc .= "\n\n---\n**Catatan Project Manager:**\n" . $feedback->pm_response;
        }

        $issue = Issue::create([
            'project_id' => $project->id,
            'issue_key' => $issueKey,
            'title' => $feedback->title,
            'type' => $issueType,
            'description' => $desc,
            'status' => IssueStatus::TO_DO,
            'priority' => $priority,
            'story_points' => $storyPoints,
            'reporter_id' => $feedback->user_id ?: Auth::id(),
            'sprint_id' => null, // Placed directly into Backlog
            'order_index' => $maxOrder + 1,
        ]);

        $feedback->update([
            'status' => 'planned',
            'converted_issue_id' => $issue->id,
            'pm_id' => Auth::id(),
            'responded_at' => now(),
            'pm_response' => $feedback->pm_response ?: "Disetujui dan dimasukkan ke Backlog proyek sebagai tiket {$issueKey}.",
        ]);

        return redirect()->route('projects.feedback.index', $project)
            ->with('success', "Saran berhasil dikonversi menjadi tiket Backlog {$issueKey}!");
    }

    /**
     * Delete a feedback.
     */
    public function destroy(Project $project, ProjectFeedback $feedback): RedirectResponse
    {
        $canDelete = $this->isProjectManager() || (Auth::id() === $feedback->user_id && $feedback->status === 'pending');

        if (!$canDelete) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus saran ini.');
        }

        $title = $feedback->title;
        $feedback->delete();

        return redirect()->route('projects.feedback.index', $project)
            ->with('success', "Saran '{$title}' berhasil dihapus.");
    }
}

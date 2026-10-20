<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFeedback extends Model
{
    use HasFactory;

    protected $table = 'project_feedbacks';

    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'pm_response',
        'pm_id',
        'responded_at',
        'converted_issue_id',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Project to which feedback belongs.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * User who submitted the feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Project Manager who responded to the feedback.
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Issue created from this feedback (if converted).
     */
    public function convertedIssue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'converted_issue_id');
    }

    /**
     * Get user-friendly category label.
     */
    public function categoryLabel(): string
    {
        return match ($this->category) {
            'feature_request' => 'Permintaan Fitur',
            'improvement' => 'Peningkatan Sistem',
            'bug_report' => 'Laporan Kendala',
            'general' => 'Saran Umum',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }

    /**
     * Get Tailwind CSS badge class for category.
     */
    public function categoryBadgeClass(): string
    {
        return match ($this->category) {
            'feature_request' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            'improvement' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            'bug_report' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            'general' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            default => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
        };
    }

    /**
     * Get user-friendly status label.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Review',
            'reviewed' => 'Sedang Ditinjau',
            'planned' => 'Direncanakan',
            'rejected' => 'Ditolak',
            'implemented' => 'Diimplementasikan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get Tailwind CSS badge class for status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-500/15 text-amber-300 border border-amber-500/30',
            'reviewed' => 'bg-blue-500/15 text-blue-300 border border-blue-500/30',
            'planned' => 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30',
            'rejected' => 'bg-rose-500/15 text-rose-300 border border-rose-500/30',
            'implemented' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30',
            default => 'bg-slate-500/15 text-slate-300 border border-slate-500/30',
        };
    }
}

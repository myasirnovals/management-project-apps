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
            'feature_request' => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
            'improvement' => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800',
            'bug_report' => 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-200 dark:border-rose-800',
            'general' => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800',
            default => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
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
            'pending' => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800',
            'reviewed' => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
            'planned' => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
            'rejected' => 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-200 dark:border-rose-800',
            'implemented' => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800',
            default => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
        };
    }
}

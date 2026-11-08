<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GithubPullRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'issue_id',
        'pr_number',
        'title',
        'html_url',
        'state',
        'user_username',
        'user_avatar',
        'branch_head',
        'branch_base',
        'is_merged',
        'merged_at',
        'pr_created_at',
        'pr_updated_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'is_merged' => 'boolean',
            'pr_number' => 'integer',
            'merged_at' => 'datetime',
            'pr_created_at' => 'datetime',
            'pr_updated_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function isOpen(): bool
    {
        return $this->state === 'open' && !$this->is_merged;
    }

    public function isMerged(): bool
    {
        return $this->is_merged || $this->state === 'merged';
    }

    public function isClosed(): bool
    {
        return $this->state === 'closed' && !$this->is_merged;
    }

    public function statusBadgeClasses(): string
    {
        if ($this->isMerged()) {
            return 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800';
        }

        if ($this->isOpen()) {
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800';
        }

        return 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-200 dark:border-rose-800';
    }

    public function statusLabel(): string
    {
        if ($this->isMerged()) {
            return 'Merged';
        }

        if ($this->isOpen()) {
            return 'Open';
        }

        return 'Closed';
    }
}

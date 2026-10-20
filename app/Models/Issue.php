<?php

namespace App\Models;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'issue_key',
        'parent_id',
        'type',
        'title',
        'description',
        'status',
        'priority',
        'story_points',
        'original_estimate_hours',
        'time_spent_hours',
        'reporter_id',
        'assignee_id',
        'component_id',
        'sprint_id',
        'version_id',
        'order_index',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'type' => IssueType::class,
            'status' => IssueStatus::class,
            'priority' => IssuePriority::class,
            'due_date' => 'date',
            'story_points' => 'integer',
            'original_estimate_hours' => 'decimal:2',
            'time_spent_hours' => 'decimal:2',
            'order_index' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Issue::class, 'parent_id')->orderBy('order_index');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(ReleaseVersion::class, 'version_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IssueAttachment::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(IssueComment::class)->latest();
    }

    /**
     * Scope for Backlog issues (no sprint assigned and not an epic)
     */
    public function scopeBacklog(Builder $query): Builder
    {
        return $query->whereNull('sprint_id')
            ->where('type', '!=', IssueType::EPIC->value)
            ->where('type', '!=', IssueType::SUBTASK->value)
            ->orderBy('order_index');
    }

    /**
     * Scope for Epics
     */
    public function scopeEpics(Builder $query): Builder
    {
        return $query->where('type', IssueType::EPIC->value)->orderBy('order_index');
    }

    /**
     * Scope for issues in a sprint
     */
    public function scopeInSprint(Builder $query, int $sprintId): Builder
    {
        return $query->where('sprint_id', $sprintId)->orderBy('order_index');
    }

    /**
     * Children stories or tasks belonging to this Epic
     */
    public function epicIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'parent_id')->orderBy('order_index');
    }
}

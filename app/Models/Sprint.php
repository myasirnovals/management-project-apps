<?php

namespace App\Models;

use App\Enums\IssueStatus;
use App\Enums\SprintStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => SprintStatus::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class)->orderBy('order_index');
    }

    public function getTotalStoryPointsAttribute(): int
    {
        return (int) $this->issues->sum('story_points');
    }

    public function getCompletedStoryPointsAttribute(): int
    {
        return (int) $this->issues->where('status', IssueStatus::DONE)->sum('story_points');
    }

    public function getCompletionPercentageAttribute(): int
    {
        $total = $this->issues->count();
        if ($total === 0) {
            return 0;
        }
        $done = $this->issues->where('status', IssueStatus::DONE)->count();
        return (int) round(($done / $total) * 100);
    }
}

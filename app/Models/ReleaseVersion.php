<?php

namespace App\Models;

use App\Enums\IssueStatus;
use App\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReleaseVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'start_date',
        'release_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => VersionStatus::class,
            'start_date' => 'date',
            'release_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'version_id')->orderBy('order_index');
    }

    public function getCompletedIssuesCountAttribute(): int
    {
        return $this->issues->where('status', IssueStatus::DONE)->count();
    }

    public function getRemainingIssuesCountAttribute(): int
    {
        return $this->issues->where('status', '!=', IssueStatus::DONE)->count();
    }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->issues->count();
        if ($total === 0) {
            return 0;
        }
        return (int) round(($this->completed_issues_count / $total) * 100);
    }
}

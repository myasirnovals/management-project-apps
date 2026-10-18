<?php

namespace App\Models;

use App\Enums\IssueType;
use App\Enums\SprintStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
        'type', // scrum, kanban
        'owner_id',
        'status',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class)->orderBy('created_at', 'desc');
    }

    public function activeSprint(): HasOne
    {
        return $this->hasOne(Sprint::class)->where('status', SprintStatus::ACTIVE->value);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ReleaseVersion::class)->orderBy('created_at', 'desc');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class)->orderBy('order_index');
    }

    public function epics(): HasMany
    {
        return $this->hasMany(Issue::class)->where('type', IssueType::EPIC->value);
    }

    /**
     * Generate next issue key for this project, e.g. PRK-15
     */
    public function generateNextIssueKey(): string
    {
        $lastIssue = $this->issues()
            ->orderByRaw('CAST(SUBSTR(issue_key, INSTR(issue_key, "-") + 1) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = 1;
        if ($lastIssue && preg_match('/-(\d+)$/', $lastIssue->issue_key, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return strtoupper($this->key) . '-' . $nextNumber;
    }
}

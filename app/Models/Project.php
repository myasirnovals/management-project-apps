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
        'code',
        'description',
        'type', // scrum, kanban
        'owner_id',
        'status',
        'github_repo',
        'github_webhook_secret',
        'github_auto_move_pr',
        'github_auto_close_pr',
    ];

    protected function casts(): array
    {
        return [
            'github_auto_move_pr' => 'boolean',
            'github_auto_close_pr' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->code)) {
                $project->code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate unique project identifier with 'pjt-' prefix and 6 random digits (e.g. pjt-098342).
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'pjt-' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Route model binding uses unique safe code (e.g. /projects/pjt-098342/kanban).
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Resolve route binding by unique code with backward-compatible numeric ID support.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('code', $value)
            ->orWhere('id', is_numeric($value) ? $value : 0)
            ->firstOrFail();
    }

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

    public function pullRequests(): HasMany
    {
        return $this->hasMany(GithubPullRequest::class)->orderBy('pr_number', 'desc');
    }

    public function hasGithubIntegration(): bool
    {
        return !empty($this->github_repo);
    }

    public function generateWebhookSecret(): string
    {
        return bin2hex(random_bytes(24));
    }

    public function getGithubWebhookUrl(): string
    {
        return url("/api/projects/{$this->code}/github/webhook");
    }

    /**
     * Generate next issue key for this project, e.g. PRK-15
     */
    public function generateNextIssueKey(): string
    {
        $lastIssue = $this->issues()->reorder('id', 'desc')->first();

        $nextNumber = 1;
        if ($lastIssue && preg_match('/-(\d+)$/', $lastIssue->issue_key, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return strtoupper($this->key) . '-' . $nextNumber;
    }
}

<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'job_title',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Projects owned by the user.
     */
    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    /**
     * Projects the user is a member of.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Issues assigned to the user.
     */
    public function assignedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'assignee_id');
    }

    /**
     * Issues reported by the user.
     */
    public function reportedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'reporter_id');
    }

    /**
     * Email selalu disimpan huruf kecil agar login dan reset kata sandi tidak peka huruf besar.
     */
    protected function email(): Attribute
    {
        return Attribute::make(set: fn (string $value) => Str::lower(trim($value)));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Project Manager adalah admin aplikasi. Scrum Master diperlakukan sama.
     */
    public function isProjectManager(): bool
    {
        return in_array($this->role, [UserRole::PROJECT_MANAGER, UserRole::SCRUM_MASTER]);
    }

    public function isProductOwner(): bool
    {
        return $this->role === UserRole::PRODUCT_OWNER;
    }

    public function isDeveloper(): bool
    {
        return $this->role === UserRole::DEVELOPER;
    }

    /**
     * Pengelola backlog dan rilis: Project Manager dan Product Owner.
     */
    public function canManageBacklog(): bool
    {
        return $this->isProjectManager() || $this->isProductOwner();
    }

    /**
     * Get user initials for avatar fallback.
     */
    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return $initials ?: 'U';
    }

    /**
     * Get standardized WhatsApp phone number.
     */
    public function formattedPhone(): ?string
    {
        return app(\App\Services\WhatsAppService::class)->formatPhoneNumber($this->phone);
    }

    /**
     * Check if user has a valid WhatsApp phone number.
     */
    public function hasWhatsApp(): bool
    {
        return !empty($this->formattedPhone());
    }

    /**
     * Generate direct WhatsApp link to this user.
     */
    public function whatsappUrl(string $message = ''): ?string
    {
        if (!$this->hasWhatsApp()) {
            return null;
        }

        return app(\App\Services\WhatsAppService::class)->generateWhatsAppUrl($this->phone, $message);
    }
}

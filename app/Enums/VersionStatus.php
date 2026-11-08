<?php

namespace App\Enums;

enum VersionStatus: string
{
    case UNRELEASED = 'unreleased';
    case RELEASED = 'released';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::UNRELEASED => 'Unreleased',
            self::RELEASED => 'Released',
            self::ARCHIVED => 'Archived',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::UNRELEASED => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800',
            self::RELEASED => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800',
            self::ARCHIVED => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
        };
    }
}

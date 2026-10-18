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
            self::UNRELEASED => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            self::RELEASED => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::ARCHIVED => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}

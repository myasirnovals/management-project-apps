<?php

namespace App\Enums;

enum SprintStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planning',
            self::ACTIVE => 'Active Sprint',
            self::COMPLETED => 'Completed',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::PLANNING => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
            self::ACTIVE => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800',
            self::COMPLETED => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
        };
    }
}

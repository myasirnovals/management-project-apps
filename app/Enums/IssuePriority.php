<?php

namespace App\Enums;

enum IssuePriority: string
{
    case HIGHEST = 'highest';
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
    case LOWEST = 'lowest';

    public function label(): string
    {
        return match ($this) {
            self::HIGHEST => 'Highest',
            self::HIGH => 'High',
            self::MEDIUM => 'Medium',
            self::LOW => 'Low',
            self::LOWEST => 'Lowest',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HIGHEST => '#9f1239',
            self::HIGH => '#92400e',
            self::MEDIUM => '#1e40af',
            self::LOW => '#334155',
            self::LOWEST => '#475569',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::HIGHEST => 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-200 dark:border-rose-800',
            self::HIGH => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800',
            self::MEDIUM => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
            self::LOW => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
            self::LOWEST => 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
        };
    }
}

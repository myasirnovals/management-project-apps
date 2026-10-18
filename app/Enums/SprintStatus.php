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
            self::PLANNING => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
            self::ACTIVE => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::COMPLETED => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        };
    }
}

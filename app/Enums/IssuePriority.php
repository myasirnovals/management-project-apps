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
            self::HIGHEST => '#dc2626', // Red
            self::HIGH => '#ea580c',    // Orange
            self::MEDIUM => '#eab308',  // Yellow
            self::LOW => '#3b82f6',     // Blue
            self::LOWEST => '#64748b',  // Slate
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::HIGHEST => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            self::HIGH => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
            self::MEDIUM => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
            self::LOW => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            self::LOWEST => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}

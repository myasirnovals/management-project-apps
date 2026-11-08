<?php

namespace App\Enums;

enum IssueType: string
{
    case EPIC = 'epic';
    case STORY = 'story';
    case TASK = 'task';
    case BUG = 'bug';
    case SUBTASK = 'subtask';

    public function label(): string
    {
        return match ($this) {
            self::EPIC => 'Epic',
            self::STORY => 'Story',
            self::TASK => 'Task',
            self::BUG => 'Bug',
            self::SUBTASK => 'Sub-task',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EPIC => '#1d4ed8',
            self::STORY => '#065f46',
            self::TASK => '#1e40af',
            self::BUG => '#9f1239',
            self::SUBTASK => '#334155',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::EPIC => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800',
            self::STORY => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800',
            self::TASK => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
            self::BUG => 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-200 dark:border-rose-800',
            self::SUBTASK => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
        };
    }
}

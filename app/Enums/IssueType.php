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
            self::EPIC => '#9333ea',    // Purple
            self::STORY => '#10b981',   // Green
            self::TASK => '#3b82f6',    // Blue
            self::BUG => '#ef4444',     // Red
            self::SUBTASK => '#06b6d4', // Cyan
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::EPIC => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border-purple-200 dark:border-purple-800',
            self::STORY => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
            self::TASK => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            self::BUG => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            self::SUBTASK => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
        };
    }
}

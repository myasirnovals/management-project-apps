<?php

namespace App\Enums;

enum UserRole: string
{
    case PROJECT_MANAGER = 'project_manager';
    case SCRUM_MASTER = 'scrum_master';
    case PRODUCT_OWNER = 'product_owner';
    case DEVELOPER = 'developer';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::PROJECT_MANAGER => 'Project Manager',
            self::SCRUM_MASTER => 'Scrum Master',
            self::PRODUCT_OWNER => 'Product Owner',
            self::DEVELOPER => 'Developer',
            self::VIEWER => 'Viewer',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PROJECT_MANAGER => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
            self::SCRUM_MASTER => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
            self::PRODUCT_OWNER => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            self::DEVELOPER => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::VIEWER => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}

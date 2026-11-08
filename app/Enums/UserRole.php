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
            self::PROJECT_MANAGER => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-200 dark:border-blue-800',
            self::SCRUM_MASTER => 'bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-800',
            self::PRODUCT_OWNER => 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-800',
            self::DEVELOPER => 'bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
            self::VIEWER => 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600',
        };
    }

    public function badgeColor(): string
    {
        return $this->badgeClass();
    }
}

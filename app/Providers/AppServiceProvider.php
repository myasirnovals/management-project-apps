<?php

namespace App\Providers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $savedSettings = [];
        if (file_exists($settingsFile = storage_path('app/app_settings.json'))) {
            $decoded = json_decode(@file_get_contents($settingsFile), true);
            if (is_array($decoded)) {
                $savedSettings = $decoded;
                if (!empty($savedSettings['app_name'])) {
                    config(['app.name' => $savedSettings['app_name']]);
                }
                if (!empty($savedSettings['default_timezone'])) {
                    config(['app.timezone' => $savedSettings['default_timezone']]);
                    @date_default_timezone_set($savedSettings['default_timezone']);
                }
                if (!empty($savedSettings['mail_mailer'])) {
                    config(['mail.default' => $savedSettings['mail_mailer']]);
                }
                if (!empty($savedSettings['mail_host'])) {
                    $encryption = $savedSettings['mail_encryption'] ?? 'tls';
                    config([
                        'mail.mailers.smtp.host' => $savedSettings['mail_host'],
                        'mail.mailers.smtp.port' => (int) ($savedSettings['mail_port'] ?? 587),
                        'mail.mailers.smtp.username' => $savedSettings['mail_username'] ?? '',
                        'mail.mailers.smtp.password' => $savedSettings['mail_password'] ?? '',
                        'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
                    ]);
                }
                if (!empty($savedSettings['mail_from_address'])) {
                    config([
                        'mail.from.address' => $savedSettings['mail_from_address'],
                        'mail.from.name' => $savedSettings['mail_from_name'] ?? config('app.name'),
                    ]);
                }
            }
        }

        View::composer(['layouts.app', 'layouts.guest', 'auth.login'], function ($view) use ($savedSettings) {
            $appBrandName = $savedSettings['app_name'] ?? config('app.name', 'Capstone Project Manager');
            $appOrgName = $savedSettings['organization_name'] ?? 'Workspace Manajemen Proyek';

            $view->with('appBrandName', $appBrandName);
            $view->with('appOrgName', $appOrgName);

            if ($view->getName() === 'layouts.app' && Auth::check()) {
                $user = Auth::user();
                $routeProject = request()->route('project');
                $currentProject = $routeProject instanceof Project
                    ? $routeProject->loadMissing(['components', 'sprints', 'versions', 'epics', 'members'])
                    : null;

                $navProjectsQuery = Project::orderBy('name');
                if (!$user->isProjectManager()) {
                    $navProjectsQuery->where(function ($q) use ($user) {
                        $q->where('owner_id', $user->id)
                            ->orWhereHas('members', fn ($m) => $m->where('users.id', $user->id));
                    });
                }

                $view->with('currentProject', $currentProject);
                $view->with('navProjects', $navProjectsQuery->get(['id', 'name', 'key', 'code', 'type', 'status']));
            }
        });
    }
}

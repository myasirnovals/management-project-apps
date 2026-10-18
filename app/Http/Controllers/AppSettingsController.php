<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AppSettingsController extends Controller
{
    /**
     * Display workspace application settings.
     */
    public function index(): View
    {
        $settings = $this->loadSettings();
        $isProjectManager = auth()->check() && auth()->user()->isProjectManager();

        return view('settings.index', compact('settings', 'isProjectManager'));
    }

    /**
     * Update workspace application settings.
     */
    public function update(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isProjectManager()) {
            abort(403, 'Hanya Project Manager atau Scrum Master yang memiliki wewenang untuk mengubah pengaturan aplikasi.');
        }

        $validated = $request->validate([
            // General
            'app_name' => ['required', 'string', 'max:100'],
            'organization_name' => ['nullable', 'string', 'max:150'],
            'default_timezone' => ['required', 'string', 'max:100'],

            // Google Calendar
            'calendar_id' => ['nullable', 'string', 'max:255'],
            'calendar_timezone' => ['required', 'string', 'max:100'],
            'calendar_view_mode' => ['required', 'in:MONTH,WEEK,AGENDA'],
            'custom_embed_url' => ['nullable', 'url', 'max:1000'],

            // SMTP Email
            'mail_mailer' => ['required', 'in:smtp,log'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'numeric'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        $settingsFile = storage_path('app/app_settings.json');
        $directory = dirname($settingsFile);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($settingsFile, json_encode($validated, JSON_PRETTY_PRINT));

        // Sync to calendar_settings.json
        $calendarSettings = [
            'calendar_id' => $validated['calendar_id'] ?? '',
            'timezone' => $validated['calendar_timezone'] ?? 'Asia/Jakarta',
            'view_mode' => $validated['calendar_view_mode'] ?? 'MONTH',
            'custom_embed_url' => $validated['custom_embed_url'] ?? '',
        ];
        file_put_contents(storage_path('app/calendar_settings.json'), json_encode($calendarSettings, JSON_PRETTY_PRINT));

        return redirect()->route('app.settings')
            ->with('success', 'Pengaturan aplikasi (Google Calendar, SMTP, dan Workspace) berhasil diperbarui!');
    }

    /**
     * Load settings from persistent storage.
     */
    protected function loadSettings(): array
    {
        $defaultSettings = [
            'app_name' => 'AgilePM',
            'organization_name' => 'Pro Project Studio',
            'default_timezone' => 'Asia/Jakarta',

            // Google Calendar
            'calendar_id' => 'agile-team@group.calendar.google.com',
            'calendar_timezone' => 'Asia/Jakarta',
            'calendar_view_mode' => 'MONTH',
            'custom_embed_url' => '',

            // SMTP Email
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@project.local',
            'mail_from_name' => 'AgilePM Notification',
        ];

        // Merge with existing calendar_settings.json if present
        $calFile = storage_path('app/calendar_settings.json');
        if (file_exists($calFile)) {
            $savedCal = json_decode(file_get_contents($calFile), true);
            if (is_array($savedCal)) {
                if (isset($savedCal['calendar_id'])) $defaultSettings['calendar_id'] = $savedCal['calendar_id'];
                if (isset($savedCal['timezone'])) $defaultSettings['calendar_timezone'] = $savedCal['timezone'];
                if (isset($savedCal['view_mode'])) $defaultSettings['calendar_view_mode'] = $savedCal['view_mode'];
                if (isset($savedCal['custom_embed_url'])) $defaultSettings['custom_embed_url'] = $savedCal['custom_embed_url'];
            }
        }

        $settingsFile = storage_path('app/app_settings.json');
        if (file_exists($settingsFile)) {
            $saved = json_decode(file_get_contents($settingsFile), true);
            if (is_array($saved)) {
                $defaultSettings = array_merge($defaultSettings, $saved);
            }
        }

        return $defaultSettings;
    }
}

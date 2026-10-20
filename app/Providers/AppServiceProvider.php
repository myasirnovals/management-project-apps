<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (file_exists($settingsFile = storage_path('app/app_settings.json'))) {
            $settings = json_decode(@file_get_contents($settingsFile), true);
            if (is_array($settings)) {
                if (!empty($settings['mail_mailer'])) {
                    config(['mail.default' => $settings['mail_mailer']]);
                }
                if (!empty($settings['mail_host'])) {
                    config([
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => (int) ($settings['mail_port'] ?? 587),
                        'mail.mailers.smtp.username' => $settings['mail_username'] ?? '',
                        'mail.mailers.smtp.password' => $settings['mail_password'] ?? '',
                    ]);
                }
                if (!empty($settings['mail_from_address'])) {
                    config([
                        'mail.from.address' => $settings['mail_from_address'],
                        'mail.from.name' => $settings['mail_from_name'] ?? config('app.name'),
                    ]);
                }
            }
        }
    }
}

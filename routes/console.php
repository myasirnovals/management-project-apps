<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily automated due date reminders at 08:00 AM
\Illuminate\Support\Facades\Schedule::command('reminders:send-due-dates')->dailyAt('08:00');


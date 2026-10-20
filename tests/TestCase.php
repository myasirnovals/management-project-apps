<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?string $savedAppSettings = null;
    protected ?string $savedCalendarSettings = null;

    protected function setUp(): void
    {
        parent::setUp();

        $appSettingsPath = storage_path('app/app_settings.json');
        if (file_exists($appSettingsPath)) {
            $this->savedAppSettings = file_get_contents($appSettingsPath);
        }

        $calendarSettingsPath = storage_path('app/calendar_settings.json');
        if (file_exists($calendarSettingsPath)) {
            $this->savedCalendarSettings = file_get_contents($calendarSettingsPath);
        }
    }

    protected function tearDown(): void
    {
        $appSettingsPath = storage_path('app/app_settings.json');
        if ($this->savedAppSettings !== null) {
            file_put_contents($appSettingsPath, $this->savedAppSettings);
        }

        $calendarSettingsPath = storage_path('app/calendar_settings.json');
        if ($this->savedCalendarSettings !== null) {
            file_put_contents($calendarSettingsPath, $this->savedCalendarSettings);
        }

        parent::tearDown();
    }
}

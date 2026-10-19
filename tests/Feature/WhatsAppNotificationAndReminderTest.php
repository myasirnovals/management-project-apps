<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WhatsAppNotificationAndReminderTest extends TestCase
{
    use RefreshDatabase;

    protected User $pm;
    protected User $dev;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->pm = User::where('email', 'admin@project.local')->first();
        $this->dev = User::where('email', 'yessi@project.local')->first();
        $this->project = Project::where('key', 'PRK')->first();
    }

    public function test_whatsapp_service_formats_phone_numbers_correctly(): void
    {
        $service = new WhatsAppService();

        $this->assertEquals('6281234567890', $service->formatPhoneNumber('081234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('81234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('6281234567890'));
        $this->assertNull($service->formatPhoneNumber('123')); // Too short
        $this->assertNull($service->formatPhoneNumber(''));
        $this->assertNull($service->formatPhoneNumber(null));
    }

    public function test_user_model_has_whatsapp_helper_methods(): void
    {
        $this->assertTrue($this->dev->hasWhatsApp());
        $this->assertEquals('6281234567892', $this->dev->formattedPhone());

        $url = $this->dev->whatsappUrl('Halo Dunia');
        $this->assertStringContainsString('https://wa.me/6281234567892?text=Halo', $url);

        $noPhoneUser = User::create([
            'name' => 'Tanpa Phone',
            'email' => 'no-phone@test.local',
            'password' => bcrypt('password123'),
            'role' => 'developer',
            'phone' => null,
        ]);
        $this->assertFalse($noPhoneUser->hasWhatsApp());
        $this->assertNull($noPhoneUser->whatsappUrl('Halo'));
    }

    public function test_can_create_and_update_user_with_phone_number(): void
    {
        $this->actingAs($this->pm);

        $response = $this->post(route('users.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@test.local',
            'phone' => '085712345678',
            'role' => 'developer',
            'job_title' => 'QA Engineer',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'budi@test.local',
            'phone' => '085712345678',
        ]);

        $user = User::where('email', 'budi@test.local')->first();

        // Update phone
        $updateResponse = $this->put(route('users.update', $user), [
            'name' => 'Budi Santoso Updated',
            'email' => 'budi@test.local',
            'phone' => '089988776655',
            'role' => 'developer',
            'job_title' => 'QA Engineer',
            'is_active' => true,
        ]);

        $updateResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '089988776655',
        ]);
    }

    public function test_issue_show_json_includes_whatsapp_payload(): void
    {
        $this->actingAs($this->pm);

        $issue = Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'PRK-991',
            'title' => 'Implementasi Notifikasi WA',
            'type' => IssueType::TASK,
            'status' => IssueStatus::IN_PROGRESS,
            'priority' => IssuePriority::HIGH,
            'reporter_id' => $this->pm->id,
            'assignee_id' => $this->dev->id,
            'order_index' => 991,
            'due_date' => Carbon::tomorrow(),
        ]);

        $response = $this->getJson(route('projects.issues.show', [$this->project, $issue]));

        $response->assertOk();
        $response->assertJsonStructure([
            'issue',
            'type_badge',
            'status_badge',
            'priority_badge',
            'whatsapp' => [
                'has_phone',
                'phone',
                'assignee_name',
                'reminder_url',
                'overdue_url',
                'unresponsive_url',
            ],
        ]);

        $this->assertTrue($response->json('whatsapp.has_phone'));
        $this->assertStringContainsString('wa.me/6281234567892', $response->json('whatsapp.reminder_url'));
        $this->assertStringContainsString('wa.me/6281234567892', $response->json('whatsapp.overdue_url'));
        $this->assertStringContainsString('wa.me/6281234567892', $response->json('whatsapp.unresponsive_url'));
    }

    public function test_send_due_date_reminders_command_executes_successfully(): void
    {
        Mail::fake();

        // Create overdue issue
        Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'PRK-992',
            'title' => 'Tugas Telat Deadline',
            'type' => IssueType::BUG,
            'status' => IssueStatus::TO_DO,
            'priority' => IssuePriority::HIGH,
            'reporter_id' => $this->pm->id,
            'assignee_id' => $this->dev->id,
            'order_index' => 992,
            'due_date' => Carbon::yesterday(),
        ]);

        // Create upcoming issue
        Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'PRK-993',
            'title' => 'Tugas Mendekati Deadline',
            'type' => IssueType::STORY,
            'status' => IssueStatus::IN_PROGRESS,
            'priority' => IssuePriority::MEDIUM,
            'reporter_id' => $this->pm->id,
            'assignee_id' => $this->dev->id,
            'order_index' => 993,
            'due_date' => Carbon::today(),
        ]);

        $exitCode = Artisan::call('reminders:send-due-dates');

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('issue mendekati tenggat waktu', $output);
        $this->assertStringContainsString('issue terlambat (overdue)', $output);
    }

    public function test_can_update_whatsapp_settings_and_trigger_reminders_manually(): void
    {
        $this->actingAs($this->pm);

        $response = $this->post(route('app.settings.update'), [
            'app_name' => 'AgilePM Test',
            'organization_name' => 'Test Studio',
            'default_timezone' => 'Asia/Jakarta',
            'calendar_id' => 'team@calendar.google.com',
            'calendar_timezone' => 'Asia/Jakarta',
            'calendar_view_mode' => 'MONTH',
            'mail_mailer' => 'log',
            'whatsapp_gateway_enabled' => '1',
            'whatsapp_gateway_provider' => 'fonnte',
            'whatsapp_api_token' => 'dummy-token-123',
        ]);

        $response->assertRedirect(route('app.settings'));

        // Test manual trigger
        $triggerResponse = $this->post(route('app.settings.triggerReminders'));
        $triggerResponse->assertRedirect(route('app.settings'));
        $triggerResponse->assertSessionHas('success');
    }
}

<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\SprintStatus;
use App\Models\Component;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ReleaseVersion;
use App\Models\Sprint;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionAndDatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * 1. Test database transaction rollback upon failure.
     */
    public function test_database_transaction_rolls_back_on_exception(): void
    {
        $user = User::where('email', 'admin@project.local')->first();
        $initialProjectCount = Project::count();

        try {
            DB::transaction(function () use ($user) {
                Project::create([
                    'name' => 'Temporary Rollback Project',
                    'key' => 'ROLL',
                    'type' => 'scrum',
                    'owner_id' => $user->id,
                    'status' => 'active',
                ]);

                // Simulate an intentional unexpected error
                throw new \Exception('Simulasi kegagalan sistem dalam transaksi database');
            });
        } catch (\Exception $e) {
            $this->assertEquals('Simulasi kegagalan sistem dalam transaksi database', $e->getMessage());
        }

        // Verify that database remained intact and no partial data was committed
        $this->assertEquals($initialProjectCount, Project::count());
        $this->assertDatabaseMissing('projects', ['key' => 'ROLL']);
    }

    /**
     * 2. Test sprint complete transaction: incomplete issues rollover to backlog.
     */
    public function test_sprint_completion_moves_incomplete_issues_to_backlog_atomically(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // Create a dedicated sprint with 3 issues
        $sprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'Integrity Sprint 1',
            'status' => SprintStatus::ACTIVE,
            'start_date' => now(),
            'end_date' => now()->addWeeks(2),
        ]);

        $doneIssue = Issue::create([
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Issue Done',
            'type' => IssueType::TASK->value,
            'status' => IssueStatus::DONE->value,
            'priority' => IssuePriority::MEDIUM->value,
            'reporter_id' => $admin->id,
            'order_index' => 100,
        ]);

        $todoIssue = Issue::create([
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Issue To Do (Incomplete)',
            'type' => IssueType::TASK->value,
            'status' => IssueStatus::TO_DO->value,
            'priority' => IssuePriority::HIGH->value,
            'reporter_id' => $admin->id,
            'order_index' => 101,
        ]);

        $inProgressIssue = Issue::create([
            'project_id' => $project->id,
            'sprint_id' => $sprint->id,
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Issue In Progress (Incomplete)',
            'type' => IssueType::TASK->value,
            'status' => IssueStatus::IN_PROGRESS->value,
            'priority' => IssuePriority::HIGH->value,
            'reporter_id' => $admin->id,
            'order_index' => 102,
        ]);

        // Complete sprint and move incomplete to backlog
        $response = $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$sprint->id}/complete", [
            'incomplete_action' => 'backlog',
        ]);

        $response->assertRedirect("/projects/{$project->id}/backlog");

        // Verify atomic transaction results
        $this->assertEquals(SprintStatus::COMPLETED->value, $sprint->fresh()->status->value);
        $this->assertEquals($sprint->id, $doneIssue->fresh()->sprint_id, 'Done issue must remain linked to completed sprint for metrics.');
        $this->assertNull($todoIssue->fresh()->sprint_id, 'Incomplete To Do issue must be moved to backlog.');
        $this->assertNull($inProgressIssue->fresh()->sprint_id, 'Incomplete In Progress issue must be moved to backlog.');
    }

    /**
     * 3. Test sprint complete transaction: incomplete issues rollover to next sprint.
     */
    public function test_sprint_completion_moves_incomplete_issues_to_next_sprint_atomically(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        $currentSprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'Current Sprint',
            'status' => SprintStatus::ACTIVE,
            'start_date' => now(),
            'end_date' => now()->addWeeks(2),
        ]);

        $nextSprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'Next Sprint 2',
            'status' => SprintStatus::PLANNING,
        ]);

        $incompleteIssue = Issue::create([
            'project_id' => $project->id,
            'sprint_id' => $currentSprint->id,
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Incomplete Story',
            'type' => IssueType::STORY->value,
            'status' => IssueStatus::IN_PROGRESS->value,
            'priority' => IssuePriority::HIGH->value,
            'reporter_id' => $admin->id,
            'order_index' => 105,
        ]);

        // Complete sprint and move incomplete to next sprint
        $response = $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$currentSprint->id}/complete", [
            'incomplete_action' => (string) $nextSprint->id,
        ]);

        $response->assertRedirect("/projects/{$project->id}/backlog");
        $this->assertEquals(SprintStatus::COMPLETED->value, $currentSprint->fresh()->status->value);
        $this->assertEquals($nextSprint->id, $incompleteIssue->fresh()->sprint_id);
    }

    /**
     * 4. Test cascade deletion transaction across all related tables.
     */
    public function test_deleting_project_cascades_all_related_records_without_orphans(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        // 1. Create a project with full child hierarchy
        $project = Project::create([
            'name' => 'Cascade Test Project',
            'key' => 'CASC',
            'type' => 'scrum',
            'owner_id' => $admin->id,
            'status' => 'active',
        ]);

        $sprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'CASC Sprint 1',
            'status' => 'planning',
        ]);

        $comp = Component::create([
            'project_id' => $project->id,
            'name' => 'CASC Component',
        ]);

        $version = ReleaseVersion::create([
            'project_id' => $project->id,
            'name' => 'v1.0-casc',
        ]);

        $parentIssue = Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'CASC-1',
            'title' => 'Parent Issue',
            'type' => IssueType::STORY->value,
            'reporter_id' => $admin->id,
            'component_id' => $comp->id,
            'sprint_id' => $sprint->id,
            'version_id' => $version->id,
        ]);

        $subtask = Issue::create([
            'project_id' => $project->id,
            'parent_id' => $parentIssue->id,
            'issue_key' => 'CASC-2',
            'title' => 'Child Subtask',
            'type' => IssueType::SUBTASK->value,
            'reporter_id' => $admin->id,
        ]);

        $project->members()->attach($admin->id, ['role' => 'project_manager']);

        // Delete project
        $delResponse = $this->actingAs($admin)->delete("/projects/{$project->id}");
        $delResponse->assertRedirect('/dashboard');

        // Assert all related records have been safely removed without orphans
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
        $this->assertDatabaseMissing('components', ['id' => $comp->id]);
        $this->assertDatabaseMissing('release_versions', ['id' => $version->id]);
        $this->assertDatabaseMissing('issues', ['id' => $parentIssue->id]);
        $this->assertDatabaseMissing('issues', ['id' => $subtask->id]);
        $this->assertDatabaseMissing('project_user', ['project_id' => $project->id]);
    }

    /**
     * 5. Test subtask cascade deletion when parent issue is deleted.
     */
    public function test_deleting_parent_issue_cascades_subtasks(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        $parent = Issue::create([
            'project_id' => $project->id,
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Parent Issue for Delete Test',
            'type' => IssueType::STORY->value,
            'reporter_id' => $admin->id,
        ]);

        $subtask = Issue::create([
            'project_id' => $project->id,
            'parent_id' => $parent->id,
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Subtask for Delete Test',
            'type' => IssueType::SUBTASK->value,
            'reporter_id' => $admin->id,
        ]);

        // Delete parent issue
        $res = $this->actingAs($admin)->deleteJson("/projects/{$project->id}/issues/{$parent->id}");
        $res->assertStatus(200);

        $this->assertDatabaseMissing('issues', ['id' => $parent->id]);
        $this->assertDatabaseMissing('issues', ['id' => $subtask->id]);
    }

    /**
     * 6. Test calendar integration route.
     */
    public function test_calendar_route_displays_workspace_projects(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        $response = $this->actingAs($admin)->get('/kalender-proyek');
        $response->assertStatus(200);
        $response->assertSee('Kalender Proyek');
        $response->assertSee('PRK');
    }

    /**
     * 7. Test unauthenticated guest is forbidden from mutating data.
     */
    public function test_unauthenticated_user_cannot_mutate_data(): void
    {
        $project = Project::where('key', 'PRK')->first();

        $createProjectRes = $this->post('/projects', ['name' => 'Hacker Project']);
        $createProjectRes->assertRedirect('/login');

        $createUserRes = $this->post('/users', ['name' => 'Hacker User']);
        $createUserRes->assertRedirect('/login');

        $createIssueRes = $this->postJson("/projects/{$project->id}/issues", ['title' => 'Hacker Issue']);
        $createIssueRes->assertStatus(401);
    }

    /**
     * 8. Test Google Calendar settings can only be updated by Project Manager.
     */
    public function test_google_calendar_settings_authorization(): void
    {
        $pm = User::where('email', 'admin@project.local')->first();
        $developer = User::where('role', \App\Enums\UserRole::DEVELOPER->value)->first();

        // Developer should be forbidden (403)
        $devResponse = $this->actingAs($developer)->post('/kalender-proyek/settings', [
            'calendar_id' => 'test-team@group.calendar.google.com',
            'timezone' => 'Asia/Jakarta',
            'view_mode' => 'MONTH',
        ]);
        $devResponse->assertStatus(403);

        // PM should succeed
        $pmResponse = $this->actingAs($pm)->post('/kalender-proyek/settings', [
            'calendar_id' => 'agile-team@group.calendar.google.com',
            'timezone' => 'Asia/Jakarta',
            'view_mode' => 'MONTH',
        ]);
        $pmResponse->assertRedirect('/kalender-proyek');
        $pmResponse->assertSessionHas('success');

        // Check view reflects calendar id
        $calendarPage = $this->actingAs($pm)->get('/kalender-proyek');
        $calendarPage->assertSee('agile-team@group.calendar.google.com');
    }

    /**
     * 9. Test Application Settings (Google Calendar & SMTP) authorization.
     */
    public function test_application_settings_authorization(): void
    {
        $pm = User::where('email', 'admin@project.local')->first();
        $developer = User::where('role', \App\Enums\UserRole::DEVELOPER->value)->first();

        // Developer can view page in read-only
        $devView = $this->actingAs($developer)->get('/pengaturan-aplikasi');
        $devView->assertStatus(200);
        $devView->assertSee('Pengaturan Utama Aplikasi');

        // Developer cannot mutate application settings (403)
        $devUpdate = $this->actingAs($developer)->post('/pengaturan-aplikasi', [
            'app_name' => 'Hacked App',
            'default_timezone' => 'Asia/Jakarta',
            'calendar_timezone' => 'Asia/Jakarta',
            'calendar_view_mode' => 'MONTH',
            'mail_mailer' => 'smtp',
        ]);
        $devUpdate->assertStatus(403);

        // PM can update application settings
        $pmUpdate = $this->actingAs($pm)->post('/pengaturan-aplikasi', [
            'app_name' => 'AgilePM Pro',
            'organization_name' => 'Software Engineering Lab',
            'default_timezone' => 'Asia/Jakarta',
            'calendar_id' => 'lab-agile@group.calendar.google.com',
            'calendar_timezone' => 'Asia/Jakarta',
            'calendar_view_mode' => 'MONTH',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'admin@project.local',
            'mail_from_name' => 'AgilePM Admin',
        ]);
        $pmUpdate->assertRedirect('/pengaturan-aplikasi');
        $pmUpdate->assertSessionHas('success');

        // Check view reflects updated settings
        $pmView = $this->actingAs($pm)->get('/pengaturan-aplikasi');
        $pmView->assertSee('AgilePM Pro');
        $pmView->assertSee('lab-agile@group.calendar.google.com');
    }
}

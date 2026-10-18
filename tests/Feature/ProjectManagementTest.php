<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_renders_without_register_option(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('AgilePM Workspace');
        $response->assertSee('Registrasi Publik Ditiadakan');
        $response->assertDontSee('href="/register"', false);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::where('email', 'admin@project.local')->first();

        $response = $this->post('/login', [
            'email' => 'admin@project.local',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_can_access_dashboard_and_boards(): void
    {
        $user = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('PrakManpro Software Project');

        $boardResponse = $this->actingAs($user)->get("/projects/{$project->id}/board");
        $boardResponse->assertStatus(200);
        $boardResponse->assertSee('PRK Sprint 1');
        $boardResponse->assertSee('To Do');
        $boardResponse->assertSee('In Progress');
        $boardResponse->assertSee('Done');
    }

    public function test_project_manager_can_create_new_team_member(): void
    {
        $pm = User::where('email', 'admin@project.local')->first();

        $response = $this->actingAs($pm)->post('/users', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@project.local',
            'role' => UserRole::DEVELOPER->value,
            'job_title' => 'QA Specialist',
            'password' => 'secret12345',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@project.local',
            'name' => 'Budi Santoso',
            'job_title' => 'QA Specialist',
        ]);
    }
    public function test_crud_projects(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $dev = User::where('email', 'yessi@project.local')->first();

        // 1. Create Project
        $response = $this->actingAs($admin)->post('/projects', [
            'name' => 'E-Commerce Platform',
            'key' => 'ECOM',
            'description' => 'Platform toko online B2C',
            'type' => 'scrum',
        ]);
        $project = Project::where('key', 'ECOM')->first();
        $this->assertNotNull($project);
        $response->assertRedirect("/projects/{$project->id}/board");

        // 2. Add & Remove Member
        $this->actingAs($admin)->post("/projects/{$project->id}/members", [
            'user_id' => $dev->id,
            'role' => 'developer',
        ]);
        $this->assertTrue($project->members()->where('user_id', $dev->id)->exists());

        $this->actingAs($admin)->delete("/projects/{$project->id}/members/{$dev->id}");
        $this->assertFalse($project->fresh()->members()->where('user_id', $dev->id)->exists());

        // 3. Update Project
        $this->actingAs($admin)->put("/projects/{$project->id}", [
            'name' => 'E-Commerce Global',
            'description' => 'Platform global multi-bahasa',
            'type' => 'kanban',
            'status' => 'active',
        ]);
        $this->assertEquals('E-Commerce Global', $project->fresh()->name);
        $this->assertEquals('kanban', $project->fresh()->type);

        // 4. Delete Project
        $delResponse = $this->actingAs($admin)->delete("/projects/{$project->id}");
        $delResponse->assertRedirect('/projects');
        $this->assertDatabaseMissing('projects', ['key' => 'ECOM']);
    }

    public function test_crud_issues_and_subtasks(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // 1. Create Issue
        $createRes = $this->actingAs($admin)->postJson("/projects/{$project->id}/issues", [
            'title' => 'Integrasi Payment Gateway QRIS',
            'type' => 'story',
            'description' => 'Mendukung pembayaran scan QRIS',
            'priority' => 'high',
            'story_points' => 8,
        ]);
        $createRes->assertStatus(200);
        $issueId = $createRes->json('issue.id');
        $this->assertDatabaseHas('issues', [
            'id' => $issueId,
            'title' => 'Integrasi Payment Gateway QRIS',
            'story_points' => 8,
        ]);

        // 2. Read Issue Detail (JSON)
        $showRes = $this->actingAs($admin)->getJson("/projects/{$project->id}/issues/{$issueId}");
        $showRes->assertStatus(200);
        $showRes->assertJsonFragment(['title' => 'Integrasi Payment Gateway QRIS']);

        // 3. Update Issue
        $updateRes = $this->actingAs($admin)->putJson("/projects/{$project->id}/issues/{$issueId}", [
            'title' => 'Integrasi Payment Gateway QRIS & VA',
            'type' => 'story',
            'status' => 'in_progress',
            'priority' => 'highest',
            'story_points' => 13,
        ]);
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('issues', [
            'id' => $issueId,
            'title' => 'Integrasi Payment Gateway QRIS & VA',
            'status' => 'in_progress',
            'priority' => 'highest',
        ]);

        // 4. Create Subtask under this Issue
        $subtaskRes = $this->actingAs($admin)->postJson("/projects/{$project->id}/issues/{$issueId}/subtasks", [
            'title' => 'Setup Webhook Endpoint untuk Notifikasi Bayar',
        ]);
        $subtaskRes->assertStatus(200);
        $subtaskId = $subtaskRes->json('subtask.id');
        $this->assertDatabaseHas('issues', [
            'id' => $subtaskId,
            'parent_id' => $issueId,
            'type' => 'subtask',
        ]);

        // 5. Update Status via Drag & Drop / Quick Move endpoint
        $statusRes = $this->actingAs($admin)->postJson("/projects/{$project->id}/board/status", [
            'issue_id' => $issueId,
            'status' => 'done',
        ]);
        $statusRes->assertStatus(200);
        $this->assertDatabaseHas('issues', [
            'id' => $issueId,
            'status' => 'done',
        ]);

        // 6. Delete Issue
        $delRes = $this->actingAs($admin)->deleteJson("/projects/{$project->id}/issues/{$issueId}");
        $delRes->assertStatus(200);
        $this->assertDatabaseMissing('issues', ['id' => $issueId]);
    }

    public function test_crud_sprint_lifecycle(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // 1. Create Sprint
        $createRes = $this->actingAs($admin)->post("/projects/{$project->id}/sprints", [
            'name' => 'PRK Sprint 99',
            'goal' => 'Pengujian transaksi lifecycle sprint',
            'duration_weeks' => 2,
        ]);
        $createRes->assertRedirect("/projects/{$project->id}/backlog");
        $sprint = $project->sprints()->where('name', 'PRK Sprint 99')->first();
        $this->assertNotNull($sprint);
        $this->assertEquals('planning', $sprint->status->value);

        // 2. Start Sprint (first complete or delete active sprint so no conflict)
        $activeSprint = $project->activeSprint;
        if ($activeSprint) {
            $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$activeSprint->id}/complete", [
                'incomplete_action' => 'backlog',
            ]);
        }

        $startRes = $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$sprint->id}/start", [
            'name' => 'PRK Sprint 99 - Berjalan',
            'goal' => 'Mencapai target sprint',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
        ]);
        $startRes->assertRedirect("/projects/{$project->id}/board");
        $this->assertEquals('active', $sprint->fresh()->status->value);

        // 3. Complete Sprint
        $compRes = $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$sprint->id}/complete", [
            'incomplete_action' => 'backlog',
        ]);
        $compRes->assertRedirect("/projects/{$project->id}/backlog");
        $this->assertEquals('completed', $sprint->fresh()->status->value);

        // 4. Delete Sprint
        $delRes = $this->actingAs($admin)->delete("/projects/{$project->id}/sprints/{$sprint->id}");
        $delRes->assertRedirect("/projects/{$project->id}/backlog");
        $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
    }

    public function test_crud_components(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // 1. Create Component
        $res = $this->actingAs($admin)->post("/projects/{$project->id}/components", [
            'name' => 'Microservice Notifikasi',
            'description' => 'Layanan pengiriman email & WA notifikasi',
        ]);
        $res->assertStatus(302);
        $comp = $project->components()->where('name', 'Microservice Notifikasi')->first();
        $this->assertNotNull($comp);

        // 2. Update Component
        $updateRes = $this->actingAs($admin)->put("/projects/{$project->id}/components/{$comp->id}", [
            'name' => 'Microservice Notification & SMS',
            'description' => 'Update deskripsi layanan',
        ]);
        $updateRes->assertStatus(302);
        $this->assertEquals('Microservice Notification & SMS', $comp->fresh()->name);

        // 3. Delete Component
        $delRes = $this->actingAs($admin)->delete("/projects/{$project->id}/components/{$comp->id}");
        $delRes->assertStatus(302);
        $this->assertDatabaseMissing('components', ['id' => $comp->id]);
    }

    public function test_crud_releases(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // 1. Create Release Version
        $res = $this->actingAs($admin)->post("/projects/{$project->id}/releases", [
            'name' => 'v2.5.0-beta',
            'description' => 'Rilis beta fitur payment baru',
            'start_date' => now()->toDateString(),
            'release_date' => now()->addDays(7)->toDateString(),
        ]);
        $res->assertStatus(302);
        $version = $project->versions()->where('name', 'v2.5.0-beta')->first();
        $this->assertNotNull($version);

        // 2. Update Version
        $updateRes = $this->actingAs($admin)->put("/projects/{$project->id}/releases/{$version->id}", [
            'name' => 'v2.5.0-RC1',
            'description' => 'Kandidat rilis stabil',
            'status' => 'unreleased',
        ]);
        $updateRes->assertStatus(302);
        $this->assertEquals('v2.5.0-RC1', $version->fresh()->name);

        // 3. Trigger Release
        $releaseRes = $this->actingAs($admin)->post("/projects/{$project->id}/releases/{$version->id}/release", [
            'ignore_unresolved' => 1,
        ]);
        $releaseRes->assertStatus(302);
        $this->assertEquals('released', $version->fresh()->status->value);

        // 4. Delete Version
        $delRes = $this->actingAs($admin)->delete("/projects/{$project->id}/releases/{$version->id}");
        $delRes->assertStatus(302);
        $this->assertDatabaseMissing('release_versions', ['id' => $version->id]);
    }

    public function test_backlog_operations_and_bulk_actions(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // Create 2 test issues
        $i1 = $project->issues()->create([
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Bulk Test Issue 1',
            'type' => 'task',
            'status' => 'to_do',
            'priority' => 'low',
            'reporter_id' => $admin->id,
            'order_index' => 991,
        ]);

        $i2 = $project->issues()->create([
            'issue_key' => $project->generateNextIssueKey(),
            'title' => 'Bulk Test Issue 2',
            'type' => 'task',
            'status' => 'to_do',
            'priority' => 'low',
            'reporter_id' => $admin->id,
            'order_index' => 992,
        ]);

        // Bulk Change Status
        $bulkRes = $this->actingAs($admin)->post("/projects/{$project->id}/backlog/bulk", [
            'action' => 'change_status',
            'target_status' => 'in_progress',
            'issue_ids' => [$i1->id, $i2->id],
        ]);
        $bulkRes->assertStatus(302);
        $this->assertEquals('in_progress', $i1->fresh()->status->value);
        $this->assertEquals('in_progress', $i2->fresh()->status->value);

        // Bulk Delete
        $delBulkRes = $this->actingAs($admin)->post("/projects/{$project->id}/backlog/bulk", [
            'action' => 'delete',
            'issue_ids' => [$i1->id, $i2->id],
        ]);
        $delBulkRes->assertStatus(302);
        $this->assertDatabaseMissing('issues', ['id' => $i1->id]);
        $this->assertDatabaseMissing('issues', ['id' => $i2->id]);

        // CSV Export
        $csvRes = $this->actingAs($admin)->get("/projects/{$project->id}/backlog/export-csv");
        $csvRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $csvRes->headers->get('Content-Type'));
    }
}

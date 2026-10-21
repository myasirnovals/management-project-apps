<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\ProjectFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionAndFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected User $pm;
    protected User $scrumMaster;
    protected User $productOwner;
    protected User $developer;
    protected User $viewer;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->pm = User::where('role', UserRole::PROJECT_MANAGER->value)->first() ?? User::factory()->create([
            'role' => UserRole::PROJECT_MANAGER->value,
            'email' => 'pm@test.local',
        ]);

        $this->scrumMaster = User::where('role', UserRole::SCRUM_MASTER->value)->first() ?? User::factory()->create([
            'role' => UserRole::SCRUM_MASTER->value,
            'email' => 'sm@test.local',
        ]);

        $this->productOwner = User::where('role', UserRole::PRODUCT_OWNER->value)->first() ?? User::factory()->create([
            'role' => UserRole::PRODUCT_OWNER->value,
            'email' => 'po@test.local',
        ]);

        $this->developer = User::where('role', UserRole::DEVELOPER->value)->first() ?? User::factory()->create([
            'role' => UserRole::DEVELOPER->value,
            'email' => 'dev@test.local',
        ]);

        $this->viewer = User::where('role', UserRole::VIEWER->value)->first() ?? User::factory()->create([
            'role' => UserRole::VIEWER->value,
            'email' => 'viewer@test.local',
        ]);

        $this->project = Project::first() ?? Project::factory()->create([
            'key' => 'TEST',
            'name' => 'Testing Project',
        ]);

        // Ensure users are members of the project
        foreach ([$this->pm, $this->scrumMaster, $this->productOwner, $this->developer, $this->viewer] as $u) {
            if (!$this->project->members()->where('user_id', $u->id)->exists()) {
                $this->project->members()->attach($u->id);
            }
        }
    }

    /**
     * Test configuration and administration access restriction (PM only).
     */
    public function test_non_pm_roles_cannot_access_configurations_or_admin_routes(): void
    {
        $nonPmUsers = [$this->developer, $this->viewer, $this->scrumMaster, $this->productOwner];

        foreach ($nonPmUsers as $user) {
            // App settings
            $this->actingAs($user)->get('/pengaturan-aplikasi')->assertStatus(403);
            $this->actingAs($user)->post('/pengaturan-aplikasi', ['app_name' => 'Bad'])->assertStatus(403);

            // User management
            $this->actingAs($user)->get('/users')->assertStatus(403);
            $this->actingAs($user)->post('/users', ['name' => 'Bad', 'email' => 'bad@test.local'])->assertStatus(403);

            // Project creation / edit
            $this->actingAs($user)->get('/projects/create')->assertStatus(403);
            $this->actingAs($user)->get("/projects/{$this->project->id}/edit")->assertStatus(403);

            // Project member management
            $this->actingAs($user)->post("/projects/{$this->project->id}/members", ['user_id' => $user->id])->assertStatus(403);
            $this->actingAs($user)->delete("/projects/{$this->project->id}/members/{$user->id}")->assertStatus(403);

            // GitHub repository mutation
            $this->actingAs($user)->post("/projects/{$this->project->id}/github", ['github_repo' => 'test/repo'])->assertStatus(403);
        }

        // PM can access successfully
        $this->actingAs($this->pm)->get('/pengaturan-aplikasi')->assertStatus(200);
        $this->actingAs($this->pm)->get('/users')->assertStatus(200);
        $this->actingAs($this->pm)->get('/projects/create')->assertStatus(200);
        $this->actingAs($this->pm)->get("/projects/{$this->project->id}/edit")->assertStatus(200);
    }

    /**
     * Test viewer role cannot mutate issues or boards.
     */
    public function test_viewer_role_cannot_mutate_issues(): void
    {
        // Viewer cannot create issue
        $response = $this->actingAs($this->viewer)->post("/projects/{$this->project->id}/issues", [
            'title' => 'Viewer Issue Attempt',
            'type' => IssueType::TASK->value,
            'priority' => IssuePriority::MEDIUM->value,
        ]);
        $response->assertStatus(403);

        // Developer can create issue
        $devResponse = $this->actingAs($this->developer)->post("/projects/{$this->project->id}/issues", [
            'title' => 'Developer Valid Issue',
            'type' => IssueType::TASK->value,
            'priority' => IssuePriority::MEDIUM->value,
        ]);
        $devResponse->assertRedirect();
        $this->assertDatabaseHas('issues', [
            'project_id' => $this->project->id,
            'title' => 'Developer Valid Issue',
        ]);
    }

    /**
     * Test feedback system: any role can view and submit feedback.
     */
    public function test_users_can_submit_feedback_to_pm(): void
    {
        // Developer accesses feedback page
        $page = $this->actingAs($this->developer)->get("/projects/{$this->project->id}/feedback");
        $page->assertStatus(200);
        $page->assertSee('Kotak Saran');

        // Developer submits a suggestion
        $response = $this->actingAs($this->developer)->post("/projects/{$this->project->id}/feedback", [
            'title' => 'Tambah Dark Mode Otomatis',
            'category' => 'feature_request',
            'description' => 'Mohon tambahkan pengaturan tema dark mode otomatis sesuai sistem OS.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('project_feedbacks', [
            'project_id' => $this->project->id,
            'user_id' => $this->developer->id,
            'title' => 'Tambah Dark Mode Otomatis',
            'category' => 'feature_request',
            'status' => 'pending',
        ]);

        // Viewer can also submit feedback
        $viewerResponse = $this->actingAs($this->viewer)->post("/projects/{$this->project->id}/feedback", [
            'title' => 'Perbaikan Navigasi Mobile',
            'category' => 'improvement',
            'description' => 'Sidebar agak sempit di layar HP, tolong dicek ya PM.',
        ]);
        $viewerResponse->assertSessionHas('success');
    }

    /**
     * Test feedback moderation: only PM can respond or update feedback status.
     */
    public function test_only_pm_can_respond_to_feedback(): void
    {
        $feedback = ProjectFeedback::create([
            'project_id' => $this->project->id,
            'user_id' => $this->developer->id,
            'title' => 'Integrasi Slack Notification',
            'description' => 'Bisa integrasikan webhook ke Slack tim?',
            'category' => 'feature_request',
            'status' => 'pending',
        ]);

        // Developer attempts to reply/status update -> 403
        $devAttempt = $this->actingAs($this->developer)->patch("/projects/{$this->project->id}/feedback/{$feedback->id}", [
            'status' => 'under_review',
            'pm_response' => 'Saya sendiri yang approve.',
        ]);
        $devAttempt->assertStatus(403);

        // PM responds successfully
        $pmResponse = $this->actingAs($this->pm)->patch("/projects/{$this->project->id}/feedback/{$feedback->id}", [
            'status' => 'planned',
            'pm_response' => 'Ide sangat bagus! Akan kita agendakan di Sprint 3.',
        ]);
        $pmResponse->assertRedirect();
        $pmResponse->assertSessionHas('success');

        $feedback->refresh();
        $this->assertEquals('planned', $feedback->status);
        $this->assertEquals('Ide sangat bagus! Akan kita agendakan di Sprint 3.', $feedback->pm_response);
        $this->assertEquals($this->pm->id, $feedback->pm_id);
        $this->assertNotNull($feedback->responded_at);
    }

    /**
     * Test converting feedback to backlog issue by PM.
     */
    public function test_pm_can_convert_feedback_into_backlog_issue(): void
    {
        $feedback = ProjectFeedback::create([
            'project_id' => $this->project->id,
            'user_id' => $this->developer->id,
            'title' => 'Export Laporan ke PDF',
            'description' => 'Perlu fitur export sprint burndown ke file PDF.',
            'category' => 'feature_request',
            'status' => 'pending',
        ]);

        // Developer cannot convert -> 403
        $devConvert = $this->actingAs($this->developer)->post("/projects/{$this->project->id}/feedback/{$feedback->id}/convert", [
            'issue_type' => IssueType::STORY->value,
            'priority' => IssuePriority::HIGH->value,
        ]);
        $devConvert->assertStatus(403);

        // PM converts feedback to issue
        $pmConvert = $this->actingAs($this->pm)->post("/projects/{$this->project->id}/feedback/{$feedback->id}/convert", [
            'issue_type' => IssueType::STORY->value,
            'priority' => IssuePriority::HIGH->value,
            'story_points' => 5,
        ]);
        $pmConvert->assertRedirect();
        $pmConvert->assertSessionHas('success');

        $feedback->refresh();
        $this->assertEquals('planned', $feedback->status);
        $this->assertNotNull($feedback->converted_issue_id);

        $this->assertDatabaseHas('issues', [
            'id' => $feedback->converted_issue_id,
            'project_id' => $this->project->id,
            'title' => 'Export Laporan ke PDF',
            'type' => IssueType::STORY->value,
            'priority' => IssuePriority::HIGH->value,
            'story_points' => 5,
        ]);
    }

    /**
     * Test role-adaptive dashboard display for Project Manager.
     */
    public function test_dashboard_adapts_for_project_manager(): void
    {
        ProjectFeedback::create([
            'project_id' => $this->project->id,
            'user_id' => $this->developer->id,
            'title' => 'Fitur Baru Testing PM',
            'description' => 'Deskripsi testing feedback',
            'category' => 'feature_request',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->pm)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, ' . $this->pm->name);
        $response->assertSee('Role: Project Manager');
        $response->assertSee('Buat Proyek Baru');
        $response->assertSee('Tinjau Kotak Saran');
        $response->assertSee('Kotak Saran Tim Memerlukan Tindakan');
    }

    /**
     * Test role-adaptive dashboard display for Developer.
     */
    public function test_dashboard_adapts_for_developer(): void
    {
        $response = $this->actingAs($this->developer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, ' . $this->developer->name);
        $response->assertSee('Role: Developer');
        $response->assertSee('Tugas Saya di Sprint Aktif');
        $response->assertDontSee('Buat Proyek Baru');
    }

    /**
     * Test role-adaptive dashboard display for Viewer.
     */
    public function test_dashboard_adapts_for_viewer(): void
    {
        $response = $this->actingAs($this->viewer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, ' . $this->viewer->name);
        $response->assertSee('Role: Viewer');
        $response->assertDontSee('Buat Proyek Baru');
    }
}

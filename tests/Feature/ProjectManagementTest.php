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
}

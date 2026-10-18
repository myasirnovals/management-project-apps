<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenderAllViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_renders_for_guest(): void
    {
        $response = $this->get('/login');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_all_authenticated_pages_render_without_error(): void
    {
        $user = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        $routes = [
            '/dashboard',
            '/projects/create',
            "/projects/{$project->id}/edit",
            "/projects/{$project->id}/board",
            "/projects/{$project->id}/kanban",
            "/projects/{$project->id}/backlog",
            "/projects/{$project->id}/components",
            "/projects/{$project->id}/releases",
            "/projects/{$project->id}/reports",
            "/projects/{$project->id}/reports/burndown",
            "/projects/{$project->id}/reports/burnup",
            "/projects/{$project->id}/reports/velocity",
            "/projects/{$project->id}/reports/cfd",
            "/projects/{$project->id}/reports/epic",
            '/users',
            '/kalender-proyek',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $this->assertEquals(
                200, 
                $response->getStatusCode(), 
                "Route {$route} failed with status {$response->getStatusCode()}: " . substr($response->getContent(), 0, 300)
            );
        }
    }
}

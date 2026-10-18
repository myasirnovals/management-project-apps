<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_initials_generation(): void
    {
        $user1 = User::factory()->make(['name' => 'Devita Dwitama']);
        $this->assertEquals('DD', $user1->initials());

        $user2 = User::factory()->make(['name' => 'Developer']);
        $this->assertEquals('D', $user2->initials());

        $user3 = User::factory()->make(['name' => 'Muhamad Yasir Noval']);
        $this->assertEquals('MY', $user3->initials());

        $user4 = User::factory()->make(['name' => '']);
        $this->assertEquals('U', $user4->initials());
    }

    public function test_user_roles_and_project_manager_check(): void
    {
        $pm = User::factory()->make(['role' => UserRole::PROJECT_MANAGER]);
        $sm = User::factory()->make(['role' => UserRole::SCRUM_MASTER]);
        $dev = User::factory()->make(['role' => UserRole::DEVELOPER]);
        $viewer = User::factory()->make(['role' => UserRole::VIEWER]);

        $this->assertTrue($pm->isProjectManager());
        $this->assertTrue($sm->isProjectManager());
        $this->assertFalse($dev->isProjectManager());
        $this->assertFalse($viewer->isProjectManager());

        $this->assertEquals('Project Manager', UserRole::PROJECT_MANAGER->label());
        $this->assertEquals('Scrum Master', UserRole::SCRUM_MASTER->label());
        $this->assertEquals('Developer', UserRole::DEVELOPER->label());
    }
}

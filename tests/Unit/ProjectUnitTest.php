<?php

namespace Tests\Unit;

use App\Enums\IssueType;
use App\Models\Component;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ReleaseVersion;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_generates_first_issue_key_correctly(): void
    {
        $owner = User::factory()->create();
        $project = Project::create([
            'name' => 'Alpha Core',
            'key' => 'ALPHA',
            'type' => 'scrum',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);

        $nextKey = $project->generateNextIssueKey();
        $this->assertEquals('ALPHA-1', $nextKey);
    }

    public function test_project_generates_sequential_issue_keys(): void
    {
        $owner = User::factory()->create();
        $project = Project::create([
            'name' => 'Beta Pay',
            'key' => 'BETA',
            'type' => 'scrum',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);

        Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'BETA-1',
            'title' => 'Issue 1',
            'type' => IssueType::TASK->value,
            'reporter_id' => $owner->id,
            'order_index' => 1,
        ]);

        Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'BETA-2',
            'title' => 'Issue 2',
            'type' => IssueType::TASK->value,
            'reporter_id' => $owner->id,
            'order_index' => 2,
        ]);

        $nextKey = $project->generateNextIssueKey();
        $this->assertEquals('BETA-3', $nextKey);
    }

    public function test_project_relationships(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $project = Project::create([
            'name' => 'Gamma App',
            'key' => 'GAMMA',
            'type' => 'scrum',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);

        // Member
        $project->members()->attach($member->id, ['role' => 'developer']);
        $this->assertTrue($project->members->contains($member));

        // Sprint
        $sprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'Gamma Sprint 1',
            'status' => 'active',
        ]);
        $this->assertEquals($sprint->id, $project->activeSprint->id);

        // Component
        $comp = Component::create([
            'project_id' => $project->id,
            'name' => 'UI Kit',
        ]);
        $this->assertTrue($project->components->contains($comp));

        // Release Version
        $ver = ReleaseVersion::create([
            'project_id' => $project->id,
            'name' => 'v1.0.0',
        ]);
        $this->assertTrue($project->versions->contains($ver));

        // Issue & Epic
        $epic = Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'GAMMA-1',
            'title' => 'Core Architecture Epic',
            'type' => IssueType::EPIC->value,
            'reporter_id' => $owner->id,
        ]);
        $this->assertTrue($project->epics->contains($epic));
    }
}

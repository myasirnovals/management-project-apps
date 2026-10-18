<?php

namespace Tests\Unit;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_enums_have_valid_labels_and_values(): void
    {
        // IssueType
        $this->assertEquals('Story', IssueType::STORY->label());
        $this->assertEquals('Task', IssueType::TASK->label());
        $this->assertEquals('Bug', IssueType::BUG->label());
        $this->assertEquals('Epic', IssueType::EPIC->label());
        $this->assertEquals('Sub-task', IssueType::SUBTASK->label());

        // IssueStatus
        $this->assertEquals('To Do', IssueStatus::TO_DO->label());
        $this->assertEquals('In Progress', IssueStatus::IN_PROGRESS->label());
        $this->assertEquals('Done', IssueStatus::DONE->label());

        // IssuePriority
        $this->assertEquals('Highest', IssuePriority::HIGHEST->label());
        $this->assertEquals('Medium', IssuePriority::MEDIUM->label());
        $this->assertEquals('Lowest', IssuePriority::LOWEST->label());
    }

    public function test_issue_parent_subtask_hierarchy(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Hierarchy Test',
            'key' => 'HIER',
            'type' => 'scrum',
            'owner_id' => $user->id,
            'status' => 'active',
        ]);

        $parent = Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'HIER-1',
            'title' => 'Parent User Story',
            'type' => IssueType::STORY->value,
            'reporter_id' => $user->id,
            'status' => IssueStatus::TO_DO->value,
            'priority' => IssuePriority::HIGH->value,
        ]);

        $subtask = Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'HIER-2',
            'parent_id' => $parent->id,
            'title' => 'Subtask 1: Backend logic',
            'type' => IssueType::SUBTASK->value,
            'reporter_id' => $user->id,
            'status' => IssueStatus::TO_DO->value,
            'priority' => IssuePriority::MEDIUM->value,
        ]);

        $this->assertTrue($parent->subtasks->contains($subtask));
        $this->assertEquals($parent->id, $subtask->parent->id);
    }

    public function test_issue_relationships_with_reporter_and_assignee(): void
    {
        $reporter = User::factory()->create();
        $assignee = User::factory()->create();

        $project = Project::create([
            'name' => 'Assignee Test',
            'key' => 'ASG',
            'type' => 'scrum',
            'owner_id' => $reporter->id,
            'status' => 'active',
        ]);

        $issue = Issue::create([
            'project_id' => $project->id,
            'issue_key' => 'ASG-1',
            'title' => 'Issue With Assignment',
            'type' => IssueType::TASK->value,
            'reporter_id' => $reporter->id,
            'assignee_id' => $assignee->id,
            'status' => IssueStatus::IN_PROGRESS->value,
            'story_points' => 5,
        ]);

        $this->assertEquals($reporter->id, $issue->reporter->id);
        $this->assertEquals($assignee->id, $issue->assignee->id);
        $this->assertEquals(5, $issue->story_points);
        $this->assertEquals($project->id, $issue->project->id);
    }
}

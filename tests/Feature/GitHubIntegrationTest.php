<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\UserRole;
use App\Models\GithubPullRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => UserRole::PROJECT_MANAGER->value,
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'name' => 'Mobile Banking App',
            'key' => 'BANK',
            'code' => 'pjt-100001',
            'type' => 'scrum',
            'owner_id' => $this->user->id,
            'status' => 'active',
            'github_repo' => 'myorg/banking-app',
            'github_webhook_secret' => 'supersecret123',
            'github_auto_move_pr' => true,
            'github_auto_close_pr' => true,
        ]);

        $this->project->members()->attach($this->user->id, ['role' => UserRole::PROJECT_MANAGER->value]);
    }

    public function test_user_can_view_github_integration_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('projects.github.index', $this->project));

        $response->assertStatus(200);
        $response->assertSee('Integrasi GitHub');
        $response->assertSee('myorg/banking-app');
        $response->assertSee('supersecret123');
    }

    public function test_user_can_save_and_update_github_repository_configuration(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('projects.github.update', $this->project), [
                'github_repo' => 'https://github.com/myorg/awesome-payment-gateway/',
                'github_auto_move_pr' => '1',
                'github_auto_close_pr' => '1',
            ]);

        $response->assertRedirect(route('projects.github.index', $this->project));
        $this->project->refresh();

        $this->assertEquals('myorg/awesome-payment-gateway', $this->project->github_repo);
        $this->assertNotEmpty($this->project->github_webhook_secret);
    }

    public function test_user_can_regenerate_webhook_secret(): void
    {
        $oldSecret = $this->project->github_webhook_secret;

        $response = $this->actingAs($this->user)
            ->post(route('projects.github.regenerateSecret', $this->project));

        $response->assertRedirect(route('projects.github.index', $this->project));
        $this->project->refresh();

        $this->assertNotEquals($oldSecret, $this->project->github_webhook_secret);
        $this->assertNotEmpty($this->project->github_webhook_secret);
    }

    public function test_user_can_disconnect_github_repository(): void
    {
        $response = $this->actingAs($this->user)
            ->delete(route('projects.github.disconnect', $this->project));

        $response->assertRedirect(route('projects.github.index', $this->project));
        $this->project->refresh();

        $this->assertNull($this->project->github_repo);
        $this->assertNull($this->project->github_webhook_secret);
        $this->assertFalse($this->project->hasGithubIntegration());
    }

    public function test_github_webhook_ping_event_responds_with_success(): void
    {
        $payload = [
            'zen' => 'Keep it logically awesome.',
            'hook_id' => 12345678,
            'repository' => [
                'full_name' => 'myorg/banking-app',
            ],
        ];

        $rawPayload = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawPayload, $this->project->github_webhook_secret);

        $response = $this->withHeaders([
            'X-GitHub-Event' => 'ping',
            'X-Hub-Signature-256' => $signature,
        ])->postJson(route('api.github.webhook.project', $this->project), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'zen' => 'Keep it logically awesome.',
        ]);
    }

    public function test_github_webhook_verifies_signature_when_secret_is_set(): void
    {
        $payload = [
            'zen' => 'Testing signature failure',
            'repository' => ['full_name' => 'myorg/banking-app'],
        ];

        // Incorrect signature
        $response = $this->withHeaders([
            'X-GitHub-Event' => 'ping',
            'X-Hub-Signature-256' => 'sha256=invalidhashvalue12345',
        ])->postJson(route('api.github.webhook.project', $this->project), $payload);

        $response->assertStatus(403);
    }

    public function test_github_webhook_pull_request_opened_moves_issue_to_in_review(): void
    {
        $issue = Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'BANK-42',
            'title' => 'Implement Biometric Fingerprint Login',
            'type' => IssueType::STORY->value,
            'status' => IssueStatus::TO_DO->value,
            'priority' => IssuePriority::HIGH->value,
            'reporter_id' => $this->user->id,
        ]);

        $payload = [
            'action' => 'opened',
            'repository' => [
                'full_name' => 'myorg/banking-app',
                'name' => 'banking-app',
                'html_url' => 'https://github.com/myorg/banking-app',
            ],
            'pull_request' => [
                'number' => 88,
                'title' => '[BANK-42] Add fingerprint auth service',
                'html_url' => 'https://github.com/myorg/banking-app/pull/88',
                'state' => 'open',
                'merged' => false,
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
                'head' => ['ref' => 'feature/BANK-42-biometrics'],
                'base' => ['ref' => 'main'],
                'user' => [
                    'login' => 'johndoe',
                    'avatar_url' => 'https://github.com/images/error/octocat_happy.gif',
                ],
                'body' => 'Closes BANK-42 biometric auth implementation.',
            ],
        ];

        $rawPayload = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawPayload, $this->project->github_webhook_secret);

        $response = $this->withHeaders([
            'X-GitHub-Event' => 'pull_request',
            'X-Hub-Signature-256' => $signature,
        ])->postJson(route('api.github.webhook.project', $this->project), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'pr_number' => 88,
            'linked_issue' => 'BANK-42',
        ]);

        // Issue status must automatically transition to IN_REVIEW
        $issue->refresh();
        $this->assertEquals(IssueStatus::IN_REVIEW, $issue->status);

        // Pull request record must be persisted
        $this->assertDatabaseHas('github_pull_requests', [
            'project_id' => $this->project->id,
            'issue_id' => $issue->id,
            'pr_number' => 88,
            'state' => 'open',
            'user_username' => 'johndoe',
        ]);
    }

    public function test_github_webhook_pull_request_merged_moves_issue_to_done(): void
    {
        $issue = Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'BANK-99',
            'title' => 'Fix QR Code Scanner Timeout',
            'type' => IssueType::BUG->value,
            'status' => IssueStatus::IN_REVIEW->value,
            'priority' => IssuePriority::HIGHEST->value,
            'reporter_id' => $this->user->id,
        ]);

        $payload = [
            'action' => 'closed',
            'repository' => [
                'full_name' => 'myorg/banking-app',
                'name' => 'banking-app',
                'html_url' => 'https://github.com/myorg/banking-app',
            ],
            'pull_request' => [
                'number' => 99,
                'title' => 'BANK-99 Fix camera stream buffer',
                'html_url' => 'https://github.com/myorg/banking-app/pull/99',
                'state' => 'closed',
                'merged' => true,
                'merged_at' => now()->toIso8601String(),
                'created_at' => now()->subDay()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
                'head' => ['ref' => 'bugfix/qr-timeout'],
                'base' => ['ref' => 'main'],
                'user' => [
                    'login' => 'alice',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/1?v=4',
                ],
                'body' => 'Resolved issue BANK-99',
            ],
        ];

        $rawPayload = json_encode($payload);
        $signature = 'sha256=' . hash_hmac('sha256', $rawPayload, $this->project->github_webhook_secret);

        $response = $this->withHeaders([
            'X-GitHub-Event' => 'pull_request',
            'X-Hub-Signature-256' => $signature,
        ])->postJson(route('api.github.webhook.project', $this->project), $payload);

        $response->assertStatus(200);

        // Issue status must automatically transition to DONE
        $issue->refresh();
        $this->assertEquals(IssueStatus::DONE, $issue->status);

        // Pull request record must be marked merged
        $this->assertDatabaseHas('github_pull_requests', [
            'project_id' => $this->project->id,
            'issue_id' => $issue->id,
            'pr_number' => 99,
            'is_merged' => true,
            'state' => 'merged',
        ]);
    }

    public function test_isolation_webhook_for_project_a_does_not_affect_project_b(): void
    {
        // Project B has its own repo and key
        $projectB = Project::create([
            'name' => 'Website E-Commerce',
            'key' => 'SHOP',
            'code' => 'pjt-200002',
            'type' => 'kanban',
            'owner_id' => $this->user->id,
            'status' => 'active',
            'github_repo' => 'myorg/ecommerce-web',
            'github_webhook_secret' => 'secret_b_456',
        ]);

        $issueB = Issue::create([
            'project_id' => $projectB->id,
            'issue_key' => 'SHOP-10',
            'title' => 'Shopping Cart Checkout',
            'type' => IssueType::STORY->value,
            'status' => IssueStatus::TO_DO->value,
            'priority' => IssuePriority::MEDIUM->value,
            'reporter_id' => $this->user->id,
        ]);

        // Incoming webhook for Project A (BANK)
        $payloadA = [
            'action' => 'opened',
            'repository' => [
                'full_name' => 'myorg/banking-app',
                'name' => 'banking-app',
            ],
            'pull_request' => [
                'number' => 12,
                'title' => 'BANK-1: Some Bank Feature',
                'html_url' => 'https://github.com/myorg/banking-app/pull/12',
                'state' => 'open',
                'merged' => false,
                'head' => ['ref' => 'feature/bank-1'],
                'base' => ['ref' => 'main'],
                'user' => ['login' => 'bankdev'],
            ],
        ];

        $rawPayload = json_encode($payloadA);
        $signature = 'sha256=' . hash_hmac('sha256', $rawPayload, $this->project->github_webhook_secret);

        $response = $this->withHeaders([
            'X-GitHub-Event' => 'pull_request',
            'X-Hub-Signature-256' => $signature,
        ])->postJson(route('api.github.webhook.project', $this->project), $payloadA);

        $response->assertStatus(200);

        // Project B must remain completely untouched
        $issueB->refresh();
        $this->assertEquals(IssueStatus::TO_DO, $issueB->status);
        $this->assertEquals(0, $projectB->pullRequests()->count());
    }

    public function test_github_webhook_simulator_executes_successfully(): void
    {
        $issue = Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'BANK-55',
            'title' => 'Transfer Real-time API',
            'type' => IssueType::TASK->value,
            'status' => IssueStatus::TO_DO->value,
            'priority' => IssuePriority::MEDIUM->value,
            'reporter_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('projects.github.simulate', $this->project), [
                'action' => 'opened',
                'issue_id' => $issue->id,
                'pr_number' => 77,
            ]);

        $response->assertRedirect(route('projects.github.index', $this->project));
        $response->assertSessionHas('success');

        $issue->refresh();
        $this->assertEquals(IssueStatus::IN_REVIEW, $issue->status);

        $this->assertDatabaseHas('github_pull_requests', [
            'project_id' => $this->project->id,
            'issue_id' => $issue->id,
            'pr_number' => 77,
            'state' => 'open',
        ]);
    }

    public function test_issue_show_json_includes_pull_requests(): void
    {
        $issue = Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'BANK-70',
            'title' => 'Security Audit Logs',
            'type' => IssueType::TASK->value,
            'status' => IssueStatus::IN_PROGRESS->value,
            'priority' => IssuePriority::HIGH->value,
            'reporter_id' => $this->user->id,
        ]);

        GithubPullRequest::create([
            'project_id' => $this->project->id,
            'issue_id' => $issue->id,
            'pr_number' => 101,
            'title' => '[BANK-70] Audit trail logging',
            'html_url' => 'https://github.com/myorg/banking-app/pull/101',
            'state' => 'open',
            'is_merged' => false,
            'user_username' => 'auditor',
            'branch_head' => 'audit-patch',
            'branch_base' => 'main',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('projects.issues.show', [$this->project, $issue]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'issue',
            'pull_requests',
        ]);

        $json = $response->json();
        $this->assertCount(1, $json['pull_requests']);
        $this->assertEquals(101, $json['pull_requests'][0]['pr_number']);
        $this->assertEquals('Open', $json['pull_requests'][0]['status_label']);
    }
}

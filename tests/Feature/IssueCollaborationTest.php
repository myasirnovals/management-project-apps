<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Models\Issue;
use App\Models\IssueAttachment;
use App\Models\IssueComment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IssueCollaborationTest extends TestCase
{
    use RefreshDatabase;

    private User $pm;
    private User $dev1;
    private User $dev2;
    private Project $project;
    private Issue $issue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pm = User::factory()->create([
            'role' => 'project_manager',
            'name' => 'PM User',
            'email' => 'pm@example.com',
        ]);

        $this->dev1 = User::factory()->create([
            'role' => 'developer',
            'name' => 'Aji Developer',
            'email' => 'aji@example.com',
        ]);

        $this->dev2 = User::factory()->create([
            'role' => 'developer',
            'name' => 'Ammar Developer',
            'email' => 'ammar@example.com',
        ]);

        $this->project = Project::create([
            'name' => 'Core Platform',
            'key' => 'CORE',
            'code' => 'CORE',
            'type' => 'scrum',
            'owner_id' => $this->pm->id,
            'status' => 'active',
        ]);

        $this->project->members()->attach([$this->pm->id, $this->dev1->id, $this->dev2->id]);

        $this->issue = Issue::create([
            'project_id' => $this->project->id,
            'issue_key' => 'CORE-1',
            'title' => 'Analisa Bug Login',
            'description' => 'Ada kendala login session timeout.',
            'type' => IssueType::BUG,
            'status' => IssueStatus::IN_PROGRESS,
            'priority' => IssuePriority::HIGH,
            'reporter_id' => $this->pm->id,
            'assignee_id' => $this->dev1->id,
            'order_index' => 1,
        ]);
    }

    public function test_user_can_upload_attachment_to_issue(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('screenshot-error.png', 200, 'image/png');

        $response = $this->actingAs($this->dev1)
            ->postJson(route('projects.issues.attachments.store', [$this->project, $this->issue]), [
                'file' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'attachment' => [
                    'filename' => 'screenshot-error.png',
                    'is_image' => true,
                ],
            ]);

        $this->assertDatabaseHas('issue_attachments', [
            'issue_id' => $this->issue->id,
            'user_id' => $this->dev1->id,
            'filename' => 'screenshot-error.png',
        ]);

        $attachment = IssueAttachment::where('filename', 'screenshot-error.png')->first();
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertExists($attachment->file_path);
    }

    public function test_user_can_download_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('analisis_fitur.pdf', 300, 'application/pdf');

        $this->actingAs($this->dev1)
            ->postJson(route('projects.issues.attachments.store', [$this->project, $this->issue]), [
                'file' => $file,
            ]);

        $attachment = IssueAttachment::firstOrFail();

        $response = $this->actingAs($this->pm)
            ->get(route('projects.issues.attachments.download', [$this->project, $this->issue, $attachment]));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=analisis_fitur.pdf');
    }

    public function test_user_can_delete_own_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('temp_file.zip', 100);

        $this->actingAs($this->dev1)
            ->postJson(route('projects.issues.attachments.store', [$this->project, $this->issue]), [
                'file' => $file,
            ]);

        $attachment = IssueAttachment::firstOrFail();
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $storage->assertExists($attachment->file_path);

        $response = $this->actingAs($this->dev1)
            ->deleteJson(route('projects.issues.attachments.destroy', [$this->project, $this->issue, $attachment]));

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('issue_attachments', ['id' => $attachment->id]);
        $storage->assertMissing($attachment->file_path);
    }

    public function test_unauthorized_user_cannot_delete_other_user_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('aji_work.png', 100);

        $this->actingAs($this->dev1)
            ->postJson(route('projects.issues.attachments.store', [$this->project, $this->issue]), [
                'file' => $file,
            ]);

        $attachment = IssueAttachment::firstOrFail();

        // Dev2 tries to delete Dev1's attachment
        $response = $this->actingAs($this->dev2)
            ->deleteJson(route('projects.issues.attachments.destroy', [$this->project, $this->issue, $attachment]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('issue_attachments', ['id' => $attachment->id]);
    }

    public function test_project_manager_can_delete_any_attachment(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('aji_work.png', 100);

        $this->actingAs($this->dev1)
            ->postJson(route('projects.issues.attachments.store', [$this->project, $this->issue]), [
                'file' => $file,
            ]);

        $attachment = IssueAttachment::firstOrFail();

        // PM deletes Dev1's attachment
        $response = $this->actingAs($this->pm)
            ->deleteJson(route('projects.issues.attachments.destroy', [$this->project, $this->issue, $attachment]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('issue_attachments', ['id' => $attachment->id]);
    }

    public function test_user_can_add_comment_to_issue(): void
    {
        $response = $this->actingAs($this->dev1)
            ->postJson(route('projects.issues.comments.store', [$this->project, $this->issue]), [
                'content' => 'Sudah saya analisa. Masalah ada di token expire handling.',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'comment' => [
                    'content' => 'Sudah saya analisa. Masalah ada di token expire handling.',
                    'user_name' => 'Aji Developer',
                ],
            ]);

        $this->assertDatabaseHas('issue_comments', [
            'issue_id' => $this->issue->id,
            'user_id' => $this->dev1->id,
            'content' => 'Sudah saya analisa. Masalah ada di token expire handling.',
        ]);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $comment = IssueComment::create([
            'issue_id' => $this->issue->id,
            'user_id' => $this->dev1->id,
            'content' => 'Komentar sementara',
        ]);

        $response = $this->actingAs($this->dev1)
            ->deleteJson(route('projects.issues.comments.destroy', [$this->project, $this->issue, $comment]));

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('issue_comments', ['id' => $comment->id]);
    }

    public function test_unauthorized_user_cannot_delete_other_user_comment(): void
    {
        $comment = IssueComment::create([
            'issue_id' => $this->issue->id,
            'user_id' => $this->dev1->id,
            'content' => 'Komentar penting',
        ]);

        $response = $this->actingAs($this->dev2)
            ->deleteJson(route('projects.issues.comments.destroy', [$this->project, $this->issue, $comment]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('issue_comments', ['id' => $comment->id]);
    }

    public function test_issue_show_json_includes_attachments_and_comments(): void
    {
        IssueComment::create([
            'issue_id' => $this->issue->id,
            'user_id' => $this->dev1->id,
            'content' => 'Catatan pertama',
        ]);

        IssueAttachment::create([
            'issue_id' => $this->issue->id,
            'user_id' => $this->dev1->id,
            'filename' => 'screen.png',
            'file_path' => 'attachments/fake/screen.png',
            'file_type' => 'image/png',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->pm)
            ->getJson(route('projects.issues.show', [$this->project, $this->issue]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'issue',
                'attachments',
                'comments',
                'is_pm',
                'current_user_id',
            ]);

        $this->assertCount(1, $response->json('attachments'));
        $this->assertCount(1, $response->json('comments'));
    }
}

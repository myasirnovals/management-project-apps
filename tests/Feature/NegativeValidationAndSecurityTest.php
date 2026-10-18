<?php

namespace Tests\Feature;

use App\Enums\SprintStatus;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegativeValidationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * NEGATIF 1: Login gagal dengan password salah.
     */
    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@project.local',
            'password' => 'wrong-password-123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * NEGATIF 2: Login gagal dengan email yang tidak terdaftar.
     */
    public function test_login_fails_with_unregistered_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'unknown.hacker@evil.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * NEGATIF 3: Pembuatan proyek gagal jika key mengandung karakter terlarang (spasi/simbol/huruf kecil).
     */
    public function test_project_creation_fails_with_invalid_key_format(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        $response = $this->actingAs($admin)->post('/projects', [
            'name' => 'Invalid Key Project',
            'key' => 'proj key!', // Invalid: lowercase, space, symbol
            'type' => 'scrum',
        ]);

        $response->assertSessionHasErrors('key');
        $this->assertDatabaseMissing('projects', ['name' => 'Invalid Key Project']);
    }

    /**
     * NEGATIF 4: Pembuatan proyek gagal jika key duplikat dengan proyek yang sudah ada.
     */
    public function test_project_creation_fails_with_duplicate_key(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        // Key 'PRK' sudah ada dari Seeder
        $response = $this->actingAs($admin)->post('/projects', [
            'name' => 'Duplicate Project PRK',
            'key' => 'PRK',
            'type' => 'scrum',
        ]);

        $response->assertSessionHasErrors('key');
        $this->assertDatabaseMissing('projects', ['name' => 'Duplicate Project PRK']);
    }

    /**
     * NEGATIF 5: User tidak diizinkan menghapus akun miliknya sendiri.
     */
    public function test_user_cannot_delete_own_account(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * NEGATIF 6: Pendaftaran user gagal jika email sudah terdaftar.
     */
    public function test_user_creation_fails_with_duplicate_email(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Duplicate Email User',
            'email' => 'admin@project.local', // Already registered
            'role' => 'developer',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * NEGATIF 7: Pendaftaran user gagal jika password terlalu pendek (< 6 karakter).
     */
    public function test_user_creation_fails_with_short_password(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Short Password User',
            'email' => 'shortpass@project.local',
            'role' => 'developer',
            'password' => '123', // Too short
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'shortpass@project.local']);
    }

    /**
     * NEGATIF 8: Pembuatan issue gagal jika tipe atau story points tidak valid.
     */
    public function test_issue_creation_fails_with_invalid_attributes(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        $response = $this->actingAs($admin)->postJson("/projects/{$project->id}/issues", [
            'title' => 'Invalid Issue Test',
            'type' => 'invalid_type_xyz', // Nonexistent enum
            'story_points' => -10, // Invalid: negative
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['type', 'story_points']);
    }

    /**
     * NEGATIF 9: Tidak dapat memulai sprint baru jika ada sprint lain yang sedang aktif di proyek yang sama.
     */
    public function test_cannot_start_sprint_when_another_sprint_is_already_active(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // Pastikan ada 1 sprint aktif
        $this->assertNotNull($project->activeSprint);

        // Buat sprint kedua berstatus planning
        $secondSprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'Sprint Planning 2',
            'status' => SprintStatus::PLANNING,
        ]);

        // Coba start sprint kedua saat sprint pertama masih aktif
        $response = $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$secondSprint->id}/start", [
            'name' => 'Sprint Planning 2',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
        ]);

        // Harus gagal dan menampilkan pesan error
        $response->assertSessionHas('error');
        $this->assertEquals(SprintStatus::PLANNING->value, $secondSprint->fresh()->status->value);
    }

    /**
     * NEGATIF 10: Memulai sprint gagal jika tanggal selesai mendahului tanggal mulai.
     */
    public function test_start_sprint_fails_if_end_date_is_before_start_date(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        // Nonaktifkan sprint aktif agar tidak kena konflik sprint ganda
        $activeSprint = $project->activeSprint;
        if ($activeSprint) {
            $activeSprint->update(['status' => SprintStatus::COMPLETED]);
        }

        $sprint = Sprint::create([
            'project_id' => $project->id,
            'name' => 'Invalid Date Sprint',
            'status' => SprintStatus::PLANNING,
        ]);

        $response = $this->actingAs($admin)->post("/projects/{$project->id}/sprints/{$sprint->id}/start", [
            'name' => 'Invalid Date Sprint',
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-10', // Invalid: end_date before start_date
        ]);

        $response->assertSessionHasErrors('end_date');
        $this->assertEquals(SprintStatus::PLANNING->value, $sprint->fresh()->status->value);
    }

    /**
     * NEGATIF 11: Aksi massal (bulk action) gagal jika tidak ada issue yang dipilih.
     */
    public function test_bulk_action_fails_when_no_issues_selected(): void
    {
        $admin = User::where('email', 'admin@project.local')->first();
        $project = Project::where('key', 'PRK')->first();

        $response = $this->actingAs($admin)->post("/projects/{$project->id}/backlog/bulk", [
            'action' => 'change_status',
            'target_status' => 'done',
            'issue_ids' => [], // Empty array
        ]);

        $response->assertSessionHas('error', 'Tidak ada issue yang dipilih untuk aksi massal.');
    }
}

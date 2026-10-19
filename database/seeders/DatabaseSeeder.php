<?php

namespace Database\Seeders;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\IssueType;
use App\Enums\SprintStatus;
use App\Enums\UserRole;
use App\Enums\VersionStatus;
use App\Models\Component;
use App\Models\Issue;
use App\Models\Project;
use App\Models\ReleaseVersion;
use App\Models\Sprint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users (Strictly managed by Admin/PM, no public register)
        $pm = User::updateOrCreate(
            ['email' => 'admin@project.local'],
            [
                'name' => 'Devita Dwitama (Project Manager)',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'role' => UserRole::PROJECT_MANAGER,
                'job_title' => 'Project Manager & Scrum Master',
                'is_active' => true,
            ]
        );

        $po = User::updateOrCreate(
            ['email' => 'po@project.local'],
            [
                'name' => 'Satriyo Ananda (Product Owner)',
                'password' => Hash::make('password123'),
                'phone' => '081234567891',
                'role' => UserRole::PRODUCT_OWNER,
                'job_title' => 'Product Owner',
                'is_active' => true,
            ]
        );

        $dev1 = User::updateOrCreate(
            ['email' => 'yessi@project.local'],
            [
                'name' => 'Yessi Sofia',
                'password' => Hash::make('password123'),
                'phone' => '081234567892',
                'role' => UserRole::DEVELOPER,
                'job_title' => 'Frontend Developer',
                'is_active' => true,
            ]
        );

        $dev2 = User::updateOrCreate(
            ['email' => 'eprian@project.local'],
            [
                'name' => 'Eprian Junan',
                'password' => Hash::make('password123'),
                'phone' => '081234567893',
                'role' => UserRole::DEVELOPER,
                'job_title' => 'Backend Developer',
                'is_active' => true,
            ]
        );

        // 2. Create Initial Project from Module Handbook
        $project = Project::updateOrCreate(
            ['key' => 'PRK'],
            [
                'name' => 'PrakManpro Software Project',
                'description' => 'Proyek aplikasi Manajemen Proyek berbasis metodologi Agile Scrum sesuai modul praktikum.',
                'type' => 'scrum',
                'owner_id' => $pm->id,
                'status' => 'active',
                'code' => 'pjt-098342',
            ]
        );

        // Attach members to project
        $project->members()->syncWithoutDetaching([
            $pm->id => ['role' => UserRole::PROJECT_MANAGER->value],
            $po->id => ['role' => UserRole::PRODUCT_OWNER->value],
            $dev1->id => ['role' => UserRole::DEVELOPER->value],
            $dev2->id => ['role' => UserRole::DEVELOPER->value],
        ]);

        // 3. Create Components (Modul I.F)
        $compFrontend = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Front-end'],
            [
                'description' => 'Ensure the technical feasibility of UI/UX designs and user interactions',
                'lead_id' => $dev1->id,
            ]
        );

        $compBackend = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Back-end'],
            [
                'description' => 'Write secure code, APIs, and business logic applications',
                'lead_id' => $dev2->id,
            ]
        );

        $compDocs = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Documentation'],
            [
                'description' => 'Technical specifications and user manual documentation',
                'lead_id' => $pm->id,
            ]
        );

        // 4. Create Release Versions (Modul V)
        $version = ReleaseVersion::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'SistemInformasi Ver1.1'],
            [
                'description' => 'Update website to include authentication, chat bot, and bug fixes',
                'start_date' => Carbon::now()->subDays(10),
                'release_date' => Carbon::now()->addDays(14),
                'status' => VersionStatus::UNRELEASED,
            ]
        );

        // 5. Create Sprints (Modul III.B)
        $sprint1 = Sprint::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'PRK Sprint 1'],
            [
                'goal' => 'Menyelesaikan modul autentikasi user dan alur kerja aplikasi utama',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(9),
                'status' => SprintStatus::ACTIVE,
            ]
        );

        $sprint2 = Sprint::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'PRK Sprint 2'],
            [
                'goal' => 'Pengembangan fitur chat bot dan laporan analitik',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(24),
                'status' => SprintStatus::PLANNING,
            ]
        );

        // 6. Create Epics (Modul II.A)
        $epic1 = Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-1'],
            [
                'type' => IssueType::EPIC,
                'title' => 'Autentikasi User',
                'description' => 'Kumpulan inisiatif terkait alur registrasi internal, login pengguna, dan keamanan akun.',
                'status' => IssueStatus::IN_PROGRESS,
                'priority' => IssuePriority::HIGH,
                'reporter_id' => $po->id,
                'assignee_id' => $pm->id,
                'component_id' => $compBackend->id,
                'version_id' => $version->id,
                'order_index' => 1,
            ]
        );

        $epic2 = Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-2'],
            [
                'type' => IssueType::EPIC,
                'title' => 'Develop Website Viable Product',
                'description' => 'Website Designer: Website Looks Profesional and interactive.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::MEDIUM,
                'reporter_id' => $po->id,
                'assignee_id' => $dev1->id,
                'component_id' => $compFrontend->id,
                'order_index' => 2,
            ]
        );

        // 7. Create Stories & Tasks in Sprint 1 (Modul II.B & II.C)
        $story1 = Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-3'],
            [
                'parent_id' => $epic1->id,
                'type' => IssueType::STORY,
                'title' => 'User Ingin tampilan web yang profesional dengan tata letak responsif',
                'description' => 'Sebagai pengguna, saya ingin antarmuka yang bersih dan interaktif agar mudah mengakses fitur utama.',
                'status' => IssueStatus::IN_PROGRESS,
                'priority' => IssuePriority::HIGH,
                'story_points' => 5,
                'original_estimate_hours' => 16,
                'time_spent_hours' => 8,
                'reporter_id' => $po->id,
                'assignee_id' => $dev1->id,
                'component_id' => $compFrontend->id,
                'sprint_id' => $sprint1->id,
                'version_id' => $version->id,
                'order_index' => 3,
                'due_date' => Carbon::now()->addDays(4),
            ]
        );

        // Subtasks under Story1 (Modul II.C)
        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-4'],
            [
                'parent_id' => $story1->id,
                'type' => IssueType::SUBTASK,
                'title' => 'Task 1: Desain Halaman Login',
                'description' => 'Buat rancangan visual login page dengan tata letak modern dan ramah pengguna.',
                'status' => IssueStatus::DONE,
                'priority' => IssuePriority::MEDIUM,
                'story_points' => 2,
                'reporter_id' => $pm->id,
                'assignee_id' => $dev1->id,
                'component_id' => $compFrontend->id,
                'sprint_id' => $sprint1->id,
                'version_id' => $version->id,
                'order_index' => 4,
            ]
        );

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-5'],
            [
                'parent_id' => $story1->id,
                'type' => IssueType::SUBTASK,
                'title' => 'Task 2: Implementasi Halaman Login HTML/CSS/JS',
                'description' => 'Coding antarmuka login menggunakan Blade template dan styling Tailwind CSS.',
                'status' => IssueStatus::IN_PROGRESS,
                'priority' => IssuePriority::HIGH,
                'story_points' => 3,
                'reporter_id' => $pm->id,
                'assignee_id' => $dev1->id,
                'component_id' => $compFrontend->id,
                'sprint_id' => $sprint1->id,
                'version_id' => $version->id,
                'order_index' => 5,
            ]
        );

        // Bug in Sprint 1 (Modul II.D)
        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-6'],
            [
                'parent_id' => $story1->id,
                'type' => IssueType::BUG,
                'title' => 'Tombol simpan tidak bekerja pada browser Safari',
                'description' => 'Ketika menekan tombol simpan, event submit terblokir karena form validation bug.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::HIGHEST,
                'story_points' => 3,
                'reporter_id' => $pm->id,
                'assignee_id' => $dev2->id,
                'component_id' => $compFrontend->id,
                'sprint_id' => $sprint1->id,
                'version_id' => $version->id,
                'order_index' => 6,
            ]
        );

        // 8. Backlog Issues (Modul VI)
        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-7'],
            [
                'parent_id' => $epic1->id,
                'type' => IssueType::STORY,
                'title' => 'Alur Kerja Lupa Password & Reset Token',
                'description' => 'User memasukkan email yang terhubung dengan akun untuk menerima token pemulihan.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::MEDIUM,
                'story_points' => 5,
                'reporter_id' => $po->id,
                'assignee_id' => $dev2->id,
                'component_id' => $compBackend->id,
                'sprint_id' => null, // IN BACKLOG
                'order_index' => 7,
            ]
        );

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-8'],
            [
                'type' => IssueType::STORY,
                'title' => 'Create Chat Bot Feature untuk Bantuan Pengguna',
                'description' => 'Integrasi bot bantuan interaktif untuk menjawab pertanyaan tim secara real-time.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::LOW,
                'story_points' => 8,
                'reporter_id' => $po->id,
                'assignee_id' => null, // Unassigned
                'component_id' => $compBackend->id,
                'sprint_id' => null, // IN BACKLOG
                'version_id' => $version->id,
                'order_index' => 8,
            ]
        );

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-9'],
            [
                'type' => IssueType::BUG,
                'title' => 'Tabel Akun Tidak Muncul pada Resolusi Mobile',
                'description' => 'Overflow table terpotong saat layar di bawah 640px.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::LOW,
                'story_points' => 1,
                'reporter_id' => $dev1->id,
                'assignee_id' => null,
                'component_id' => $compFrontend->id,
                'sprint_id' => null, // IN BACKLOG
                'order_index' => 9,
            ]
        );
    }
}

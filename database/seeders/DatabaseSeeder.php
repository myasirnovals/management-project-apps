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
    public function run(): void
    {
        $pm = User::updateOrCreate(
            ['email' => 'admin@project.local'],
            [
                'name' => 'Devita Dwitama (Project Manager)',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'role' => UserRole::PROJECT_MANAGER,
                'job_title' => 'Project Manager',
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

        $project = Project::updateOrCreate(
            ['key' => 'PRK'],
            [
                'name' => 'Portal Kolaborasi Proyek Internal',
                'description' => 'Pengembangan aplikasi web kolaborasi tugas, pengelolaan sprint, dan pelacakan rilis tim rekayasa.',
                'type' => 'scrum',
                'owner_id' => $pm->id,
                'status' => 'active',
                'code' => 'pjt-098342',
            ]
        );

        $project->members()->syncWithoutDetaching([
            $pm->id => ['role' => UserRole::PROJECT_MANAGER->value],
            $po->id => ['role' => UserRole::PRODUCT_OWNER->value],
            $dev1->id => ['role' => UserRole::DEVELOPER->value],
            $dev2->id => ['role' => UserRole::DEVELOPER->value],
        ]);

        $compFrontend = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Front-end'],
            [
                'description' => 'Antarmuka pengguna web, komponen responsif, dan interaksi klien.',
                'lead_id' => $dev1->id,
            ]
        );

        $compBackend = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Back-end'],
            [
                'description' => 'Layanan autentikasi, API internal, dan aturan bisnis aplikasi.',
                'lead_id' => $dev2->id,
            ]
        );

        $compDocs = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Documentation'],
            [
                'description' => 'Spesifikasi teknis arsitektur dan panduan operasional pengguna.',
                'lead_id' => $pm->id,
            ]
        );

        $version = ReleaseVersion::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'v1.1.0'],
            [
                'description' => 'Pembaruan sistem autentikasi, pusat bantuan pengguna, dan perbaikan stabilitas.',
                'start_date' => Carbon::now()->subDays(10),
                'release_date' => Carbon::now()->addDays(14),
                'status' => VersionStatus::UNRELEASED,
            ]
        );

        $sprint1 = Sprint::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'PRK Sprint 1'],
            [
                'goal' => 'Menyelesaikan alur autentikasi pengguna dan tata letak kerja utama.',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(9),
                'status' => SprintStatus::ACTIVE,
            ]
        );

        Sprint::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'PRK Sprint 2'],
            [
                'goal' => 'Pengembangan pusat bantuan pengguna dan laporan progres.',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(24),
                'status' => SprintStatus::PLANNING,
            ]
        );

        $epic1 = Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-1'],
            [
                'type' => IssueType::EPIC,
                'title' => 'Autentikasi & Keamanan Akun',
                'description' => 'Inisiatif terkait login pengguna, manajemen sesi, dan perlindungan akun internal.',
                'status' => IssueStatus::IN_PROGRESS,
                'priority' => IssuePriority::HIGH,
                'reporter_id' => $po->id,
                'assignee_id' => $pm->id,
                'component_id' => $compBackend->id,
                'version_id' => $version->id,
                'order_index' => 1,
            ]
        );

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-2'],
            [
                'type' => IssueType::EPIC,
                'title' => 'Antarmuka Ruang Kerja Utama',
                'description' => 'Penyusunan tata letak papan tugas dan navigasi proyek yang responsif.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::MEDIUM,
                'reporter_id' => $po->id,
                'assignee_id' => $dev1->id,
                'component_id' => $compFrontend->id,
                'order_index' => 2,
            ]
        );

        $story1 = Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-3'],
            [
                'parent_id' => $epic1->id,
                'type' => IssueType::STORY,
                'title' => 'Tata letak halaman kerja yang bersih dan responsif di perangkat seluler',
                'description' => 'Sebagai anggota tim, saya memerlukan tampilan papan kerja yang rapi agar mudah memperbarui status tiket.',
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

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-4'],
            [
                'parent_id' => $story1->id,
                'type' => IssueType::SUBTASK,
                'title' => 'Rancangan tata letak halaman masuk pengguna',
                'description' => 'Menyusun struktur form login yang jelas beserta validasi pesan kesalahan.',
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
                'title' => 'Implementasi komponen halaman masuk pada template Blade',
                'description' => 'Menerapkan komponen antarmuka login dengan gaya Tailwind CSS.',
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

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-6'],
            [
                'parent_id' => $story1->id,
                'type' => IssueType::BUG,
                'title' => 'Tombol simpan tidak merespons pada peramban Safari',
                'description' => 'Ketika menekan tombol simpan, pengiriman formulir tertahan oleh validasi sisi klien.',
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

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-7'],
            [
                'parent_id' => $epic1->id,
                'type' => IssueType::STORY,
                'title' => 'Alur pemulihan kata sandi melalui tautan email',
                'description' => 'Pengguna dapat meminta token pengaturan ulang kata sandi melalui alamat email terdaftar.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::MEDIUM,
                'story_points' => 5,
                'reporter_id' => $po->id,
                'assignee_id' => $dev2->id,
                'component_id' => $compBackend->id,
                'sprint_id' => null,
                'order_index' => 7,
            ]
        );

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-8'],
            [
                'type' => IssueType::STORY,
                'title' => 'Fitur pencarian cepat dokumentasi bantuan pengguna',
                'description' => 'Menyediakan pencarian artikel panduan langsung dari halaman kerja tim.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::LOW,
                'story_points' => 8,
                'reporter_id' => $po->id,
                'assignee_id' => null,
                'component_id' => $compBackend->id,
                'sprint_id' => null,
                'version_id' => $version->id,
                'order_index' => 8,
            ]
        );

        Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => 'PRK-9'],
            [
                'type' => IssueType::BUG,
                'title' => 'Tabel daftar anggota terpotong pada layar ponsel',
                'description' => 'Kontainer tabel melebar melampaui batas layar di bawah resolusi 640px.',
                'status' => IssueStatus::TO_DO,
                'priority' => IssuePriority::LOW,
                'story_points' => 1,
                'reporter_id' => $dev1->id,
                'assignee_id' => null,
                'component_id' => $compFrontend->id,
                'sprint_id' => null,
                'order_index' => 9,
            ]
        );
    }
}

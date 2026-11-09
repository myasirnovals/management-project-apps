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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        [$pm, $po, $ui, $lead] = $this->seedTeam();

        $project = Project::updateOrCreate(
            ['key' => 'PRK'],
            [
                'name' => 'Sistem Informasi Manajemen Toko',
                'description' => 'Pengembangan perangkat lunak Sistem Informasi Manajemen Toko: kasir, stok barang, dan laporan penjualan.',
                'type' => 'scrum',
                'owner_id' => $pm->id,
                'status' => 'active',
                'code' => 'pjt-098342',
            ]
        );

        $project->members()->syncWithoutDetaching([
            $pm->id => ['role' => UserRole::PROJECT_MANAGER->value],
            $po->id => ['role' => UserRole::PRODUCT_OWNER->value],
            $ui->id => ['role' => UserRole::DEVELOPER->value],
            $lead->id => ['role' => UserRole::DEVELOPER->value],
        ]);

        $frontend = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Front-end'],
            ['description' => 'Antarmuka kasir dan dashboard, mengikuti rancangan UI/UX.', 'lead_id' => $ui->id]
        );
        $backend = Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Back-end'],
            ['description' => 'API, logika bisnis, dan basis data.', 'lead_id' => $lead->id]
        );
        Component::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Analisis & Dokumentasi'],
            ['description' => 'Kebutuhan bisnis, alur proses, dan dokumen teknis.', 'lead_id' => $po->id]
        );

        $version = ReleaseVersion::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'v0.1 Kasir dan Stok'],
            [
                'description' => 'Rilis awal: login, transaksi kasir, dan pencatatan stok.',
                'start_date' => Carbon::now()->subDays(7),
                'release_date' => Carbon::now()->addDays(21),
                'status' => VersionStatus::UNRELEASED,
            ]
        );

        $sprint = Sprint::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'PRK Sprint 1'],
            [
                'goal' => 'Alur login dan transaksi kasir dasar berjalan end to end.',
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(11),
                'status' => SprintStatus::ACTIVE,
            ]
        );
        Sprint::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'PRK Sprint 2'],
            [
                'goal' => 'Manajemen stok barang dan laporan penjualan harian.',
                'start_date' => Carbon::now()->addDays(12),
                'end_date' => Carbon::now()->addDays(26),
                'status' => SprintStatus::PLANNING,
            ]
        );

        $epicKasir = $this->issue($project, 'PRK-1', [
            'type' => IssueType::EPIC,
            'title' => 'Transaksi kasir',
            'description' => 'Semua yang dibutuhkan kasir untuk mencatat penjualan dari awal sampai struk.',
            'status' => IssueStatus::IN_PROGRESS,
            'priority' => IssuePriority::HIGH,
            'reporter_id' => $po->id,
            'assignee_id' => $po->id,
            'component_id' => $backend->id,
            'version_id' => $version->id,
        ]);

        $epicStok = $this->issue($project, 'PRK-2', [
            'type' => IssueType::EPIC,
            'title' => 'Manajemen stok barang',
            'description' => 'Pencatatan barang masuk, barang keluar, dan batas stok minimum.',
            'status' => IssueStatus::TO_DO,
            'priority' => IssuePriority::MEDIUM,
            'reporter_id' => $po->id,
            'assignee_id' => $lead->id,
            'component_id' => $backend->id,
        ]);

        $story = $this->issue($project, 'PRK-3', [
            'parent_id' => $epicKasir->id,
            'type' => IssueType::STORY,
            'title' => 'Kasir dapat menambah barang ke keranjang lewat pencarian nama atau kode',
            'description' => 'Hasil pencarian muncul saat mengetik, dan barang yang stoknya habis diberi tanda.',
            'status' => IssueStatus::IN_PROGRESS,
            'priority' => IssuePriority::HIGH,
            'story_points' => 5,
            'original_estimate_hours' => 16,
            'time_spent_hours' => 6,
            'reporter_id' => $po->id,
            'assignee_id' => $ui->id,
            'component_id' => $frontend->id,
            'sprint_id' => $sprint->id,
            'version_id' => $version->id,
            'due_date' => Carbon::now()->addDays(5),
        ]);

        $this->issue($project, 'PRK-4', [
            'parent_id' => $story->id,
            'type' => IssueType::SUBTASK,
            'title' => 'Rancang layar kasir di Figma',
            'status' => IssueStatus::DONE,
            'priority' => IssuePriority::MEDIUM,
            'story_points' => 2,
            'reporter_id' => $pm->id,
            'assignee_id' => $ui->id,
            'component_id' => $frontend->id,
            'sprint_id' => $sprint->id,
            'version_id' => $version->id,
        ]);

        $this->issue($project, 'PRK-5', [
            'parent_id' => $story->id,
            'type' => IssueType::SUBTASK,
            'title' => 'Endpoint pencarian barang',
            'status' => IssueStatus::IN_PROGRESS,
            'priority' => IssuePriority::HIGH,
            'story_points' => 3,
            'reporter_id' => $pm->id,
            'assignee_id' => $lead->id,
            'component_id' => $backend->id,
            'sprint_id' => $sprint->id,
            'version_id' => $version->id,
        ]);

        $this->issue($project, 'PRK-6', [
            'type' => IssueType::BUG,
            'title' => 'Total belanja salah saat diskon dan pajak dipakai bersamaan',
            'description' => 'Diskon dihitung setelah pajak. Seharusnya sebelum pajak.',
            'status' => IssueStatus::TO_DO,
            'priority' => IssuePriority::HIGHEST,
            'story_points' => 2,
            'reporter_id' => $lead->id,
            'assignee_id' => $lead->id,
            'component_id' => $backend->id,
            'sprint_id' => $sprint->id,
            'version_id' => $version->id,
        ]);

        $this->issue($project, 'PRK-7', [
            'parent_id' => $epicStok->id,
            'type' => IssueType::STORY,
            'title' => 'Pemilik toko mendapat tanda saat stok barang di bawah batas minimum',
            'status' => IssueStatus::TO_DO,
            'priority' => IssuePriority::MEDIUM,
            'story_points' => 5,
            'reporter_id' => $po->id,
            'component_id' => $backend->id,
        ]);

        $this->issue($project, 'PRK-8', [
            'type' => IssueType::STORY,
            'title' => 'Laporan penjualan harian dapat diunduh sebagai CSV',
            'status' => IssueStatus::TO_DO,
            'priority' => IssuePriority::LOW,
            'story_points' => 3,
            'reporter_id' => $po->id,
            'component_id' => $backend->id,
            'version_id' => $version->id,
        ]);
    }

    /**
     * Tim inti proyek. Kata sandi awal hanya diberikan saat akun dibuat, jadi
     * menjalankan seeder ulang tidak menimpa kata sandi yang sudah diganti.
     *
     * @return array<int, User>
     */
    private function seedTeam(): array
    {
        $initialPassword = env('SEED_USER_PASSWORD', 'password123');

        $team = [
            [
                'email' => 'myasirnoval23@if.unjani.ac.id',
                'name' => 'Muhamad Yasir Noval',
                'phone' => '088220693823',
                'role' => UserRole::PROJECT_MANAGER,
                'job_title' => 'Project Manager',
            ],
            [
                'email' => 'ammarbagas23@if.unjani.ac.id',
                'name' => 'Ammar Bagas Fathurrahman Wantoro',
                'phone' => '082119763858',
                'role' => UserRole::PRODUCT_OWNER,
                'job_title' => 'System Analyst',
            ],
            [
                'email' => 'ajikartiko23@if.unjani.ac.id',
                'name' => 'Aji Kartiko Hartanto',
                'phone' => '088971734039',
                'role' => UserRole::DEVELOPER,
                'job_title' => 'UI/UX Designer',
            ],
            [
                'email' => 'myusron23@if.unjani.ac.id',
                'name' => 'Muhamad Yusron Noval',
                'phone' => '083866573447',
                'role' => UserRole::DEVELOPER,
                'job_title' => 'Lead Developer, QA / Test Engineer',
            ],
        ];

        return array_map(function (array $attributes) use ($initialPassword): User {
            $user = User::firstOrNew(['email' => $attributes['email']]);
            $user->fill($attributes + ['is_active' => true]);

            if (! $user->exists) {
                $user->password = $initialPassword;
            }

            $user->save();

            return $user;
        }, $team);
    }

    private function issue(Project $project, string $key, array $attributes): Issue
    {
        static $order = 0;

        return Issue::updateOrCreate(
            ['project_id' => $project->id, 'issue_key' => $key],
            $attributes + ['order_index' => ++$order]
        );
    }
}

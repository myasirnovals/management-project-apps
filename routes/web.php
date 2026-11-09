<?php

use App\Http\Controllers\AppSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BacklogController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GitHubIntegrationController;
use App\Http\Controllers\GitHubWebhookController;
use App\Http\Controllers\IssueAttachmentController;
use App\Http\Controllers\IssueCommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\UserController;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Auth::check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

// Akun hanya dibuat oleh Project Manager, jadi tidak ada rute registrasi.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

    Route::get('/lupa-kata-sandi', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/lupa-kata-sandi', [PasswordResetController::class, 'store'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/kata-sandi-baru/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/kata-sandi-baru', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kalender-proyek', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/pengaturan-aplikasi', [AppSettingsController::class, 'index'])->name('app.settings');

    // Hanya Project Manager (admin aplikasi).
    Route::middleware('can:administer')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

        Route::post('/pengaturan-aplikasi', [AppSettingsController::class, 'update'])->name('app.settings.update');
        Route::post('/pengaturan-aplikasi/trigger-reminders', [AppSettingsController::class, 'triggerReminders'])->name('app.settings.triggerReminders');
        Route::post('/kalender-proyek/settings', [CalendarController::class, 'updateSettings'])->name('calendar.settings.update');
    });

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

    Route::prefix('projects/{project}')->name('projects.')->scopeBindings()->middleware('project.member')->group(function () {
        Route::get('/', [ProjectController::class, 'show'])->name('show');

        // Papan dan laporan bisa dibuka semua anggota proyek.
        Route::get('/board', [BoardController::class, 'index'])->name('board');
        Route::get('/kanban', [BoardController::class, 'kanban'])->name('kanban');
        Route::post('/board/status', [BoardController::class, 'updateStatus'])->name('board.status');
        Route::get('/backlog', [BacklogController::class, 'index'])->name('backlog');
        Route::get('/timeline-calendar', [CalendarController::class, 'projectTimeline'])->name('timeline');
        Route::get('/components', [ComponentController::class, 'index'])->name('components.index');
        Route::get('/releases', [ReleaseController::class, 'index'])->name('releases.index');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/burndown', [ReportController::class, 'burndown'])->name('reports.burndown');
        Route::get('/reports/burnup', [ReportController::class, 'burnup'])->name('reports.burnup');
        Route::get('/reports/velocity', [ReportController::class, 'velocity'])->name('reports.velocity');
        Route::get('/reports/cfd', [ReportController::class, 'cumulativeFlow'])->name('reports.cfd');
        Route::get('/reports/epic', [ReportController::class, 'epic'])->name('reports.epic');

        // Issue: semua anggota boleh membuat dan memperbarui. Aturan lebih rinci ada di IssueController.
        Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
        Route::get('/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
        Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
        Route::post('/issues/{issue}/subtasks', [IssueController::class, 'storeSubtask'])->name('issues.subtasks.store');

        Route::post('/issues/{issue}/attachments', [IssueAttachmentController::class, 'store'])->name('issues.attachments.store');
        Route::get('/issues/{issue}/attachments/{attachment}/download', [IssueAttachmentController::class, 'download'])->name('issues.attachments.download');
        Route::delete('/issues/{issue}/attachments/{attachment}', [IssueAttachmentController::class, 'destroy'])->name('issues.attachments.destroy');
        Route::post('/issues/{issue}/comments', [IssueCommentController::class, 'store'])->name('issues.comments.store');
        Route::delete('/issues/{issue}/comments/{comment}', [IssueCommentController::class, 'destroy'])->name('issues.comments.destroy');

        // Product Owner dan Project Manager: backlog, komponen, rilis.
        Route::middleware('can:manage-backlog')->group(function () {
            Route::post('/backlog/move', [BacklogController::class, 'moveToSprint'])->name('backlog.move');
            Route::post('/backlog/clean-up', [BacklogController::class, 'cleanUp'])->name('backlog.cleanUp');
            Route::post('/backlog/bulk', [BacklogController::class, 'bulkAction'])->name('backlog.bulk');
            Route::get('/backlog/export-csv', [BacklogController::class, 'exportCsv'])->name('backlog.exportCsv');

            Route::delete('/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');

            Route::post('/components', [ComponentController::class, 'store'])->name('components.store');
            Route::put('/components/{component}', [ComponentController::class, 'update'])->name('components.update');
            Route::delete('/components/{component}', [ComponentController::class, 'destroy'])->name('components.destroy');

            Route::post('/releases', [ReleaseController::class, 'store'])->name('releases.store');
            Route::put('/releases/{version}', [ReleaseController::class, 'update'])->name('releases.update');
            Route::post('/releases/{version}/release', [ReleaseController::class, 'release'])->name('releases.release');
            Route::delete('/releases/{version}', [ReleaseController::class, 'destroy'])->name('releases.destroy');
        });

        // Hanya Project Manager: pengaturan proyek, sprint, anggota proyek, GitHub.
        Route::middleware('can:administer')->group(function () {
            Route::get('/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/', [ProjectController::class, 'update'])->name('update');
            Route::delete('/', [ProjectController::class, 'destroy'])->name('destroy');
            Route::post('/members', [ProjectController::class, 'addMember'])->name('members.add');
            Route::delete('/members/{user}', [ProjectController::class, 'removeMember'])->withoutScopedBindings()->name('members.remove');

            Route::post('/sprints', [SprintController::class, 'store'])->name('sprints.store');
            Route::post('/sprints/{sprint}/start', [SprintController::class, 'start'])->name('sprints.start');
            Route::post('/sprints/{sprint}/complete', [SprintController::class, 'complete'])->name('sprints.complete');
            Route::delete('/sprints/{sprint}', [SprintController::class, 'destroy'])->name('sprints.destroy');

            Route::get('/github', [GitHubIntegrationController::class, 'index'])->name('github.index');
            Route::post('/github', [GitHubIntegrationController::class, 'update'])->name('github.update');
            Route::post('/github/regenerate-secret', [GitHubIntegrationController::class, 'regenerateSecret'])->name('github.regenerateSecret');
            Route::delete('/github', [GitHubIntegrationController::class, 'disconnect'])->name('github.disconnect');
            Route::post('/github/simulate', [GitHubIntegrationController::class, 'simulate'])->name('github.simulate');
        });
    });

    // Jalan pintas ke pengaturan proyek pertama yang bisa diakses.
    Route::get('/pengaturan', function () {
        $project = Project::visibleTo(Auth::user())->first();

        return $project
            ? redirect()->route('projects.edit', $project)
            : redirect()->route('dashboard')->with('info', 'Belum ada proyek yang bisa Anda akses.');
    })->middleware('can:administer')->name('pengaturan');
});

// Penerima webhook GitHub. Dilindungi tanda tangan, bukan sesi login.
Route::post('/api/projects/{project}/github/webhook', [GitHubWebhookController::class, 'handleProject'])
    ->name('api.github.webhook.project');
Route::post('/api/github/webhook', [GitHubWebhookController::class, 'handleGlobal'])
    ->name('api.github.webhook.global');

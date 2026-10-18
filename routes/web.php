<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BacklogController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if logged in, or login if guest
Route::get('/', function () {
    return \Illuminate\Support\Facades\Auth::check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
});

// Authentication Routes (Strictly Login & Logout, NO Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User & Team Management (Only Admin/Project Manager invites members)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // Project Management
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.add');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.remove');

    // Project Calendar (/kalender-proyek)
    Route::get('/kalender-proyek', [CalendarController::class, 'index'])->name('calendar.index');

    // Project Settings Shortcut (/pengaturan)
    Route::get('/pengaturan', function () {
        $project = \App\Models\Project::first();
        if ($project) {
            return redirect()->route('projects.edit', $project);
        }
        return redirect()->route('dashboard')
            ->with('info', 'Belum ada proyek yang dibuat. Silakan buat proyek terlebih dahulu.');
    })->name('pengaturan');

    // Nested Project Routes
    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        // Boards (Modul III & IV)
        Route::get('/board', [BoardController::class, 'index'])->name('board');
        Route::get('/kanban', [BoardController::class, 'kanban'])->name('kanban');
        Route::post('/board/status', [BoardController::class, 'updateStatus'])->name('board.status');

        // Backlog (Modul VI)
        Route::get('/backlog', [BacklogController::class, 'index'])->name('backlog');
        Route::post('/backlog/move', [BacklogController::class, 'moveToSprint'])->name('backlog.move');
        Route::post('/backlog/clean-up', [BacklogController::class, 'cleanUp'])->name('backlog.cleanUp');
        Route::post('/backlog/bulk', [BacklogController::class, 'bulkAction'])->name('backlog.bulk');
        Route::get('/backlog/export-csv', [BacklogController::class, 'exportCsv'])->name('backlog.exportCsv');

        // Sprints (Modul III.B)
        Route::post('/sprints', [SprintController::class, 'store'])->name('sprints.store');
        Route::post('/sprints/{sprint}/start', [SprintController::class, 'start'])->name('sprints.start');
        Route::post('/sprints/{sprint}/complete', [SprintController::class, 'complete'])->name('sprints.complete');
        Route::delete('/sprints/{sprint}', [SprintController::class, 'destroy'])->name('sprints.destroy');

        // Issues (Modul II & IV.G)
        Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
        Route::get('/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
        Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
        Route::delete('/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
        Route::post('/issues/{issue}/subtasks', [IssueController::class, 'storeSubtask'])->name('issues.subtasks.store');

        // Components (Modul I.F)
        Route::get('/components', [ComponentController::class, 'index'])->name('components.index');
        Route::post('/components', [ComponentController::class, 'store'])->name('components.store');
        Route::put('/components/{component}', [ComponentController::class, 'update'])->name('components.update');
        Route::delete('/components/{component}', [ComponentController::class, 'destroy'])->name('components.destroy');

        // Releases (Modul V)
        Route::get('/releases', [ReleaseController::class, 'index'])->name('releases.index');
        Route::post('/releases', [ReleaseController::class, 'store'])->name('releases.store');
        Route::put('/releases/{version}', [ReleaseController::class, 'update'])->name('releases.update');
        Route::post('/releases/{version}/release', [ReleaseController::class, 'release'])->name('releases.release');
        Route::delete('/releases/{version}', [ReleaseController::class, 'destroy'])->name('releases.destroy');

        // Reports (Modul VII)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/burndown', [ReportController::class, 'burndown'])->name('reports.burndown');
        Route::get('/reports/burnup', [ReportController::class, 'burnup'])->name('reports.burnup');
        Route::get('/reports/velocity', [ReportController::class, 'velocity'])->name('reports.velocity');
        Route::get('/reports/cfd', [ReportController::class, 'cumulativeFlow'])->name('reports.cfd');
        Route::get('/reports/epic', [ReportController::class, 'epic'])->name('reports.epic');
    });
});

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add additional columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('developer')->after('email'); // project_manager, scrum_master, product_owner, developer, viewer
            $table->string('job_title')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('job_title');
            $table->boolean('is_active')->default(true)->after('avatar');
        });

        // Projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key', 10)->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('scrum'); // scrum, kanban
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();
        });

        // Project members pivot table
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('developer'); // project_manager, scrum_master, product_owner, developer, viewer
            $table->timestamps();
            $table->unique(['project_id', 'user_id']);
        });

        // Components (Modul I.F)
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('lead_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Sprints (Modul III.B)
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('goal')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('planning'); // planning, active, completed
            $table->timestamps();
        });

        // Release Versions (Modul V)
        Schema::create('release_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('release_date')->nullable();
            $table->string('status')->default('unreleased'); // unreleased, released, archived
            $table->timestamps();
        });

        // Issues (Modul II, III, IV, VI)
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('issue_key', 20)->index(); // e.g. PRK-1
            $table->foreignId('parent_id')->nullable()->constrained('issues')->cascadeOnDelete();
            $table->string('type')->default('task'); // epic, story, task, bug, subtask
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('to_do'); // to_do, in_progress, in_review, done
            $table->string('priority')->default('medium'); // highest, high, medium, low, lowest
            $table->unsignedInteger('story_points')->nullable();
            $table->decimal('original_estimate_hours', 8, 2)->nullable();
            $table->decimal('time_spent_hours', 8, 2)->nullable();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('component_id')->nullable()->constrained('components')->nullOnDelete();
            $table->foreignId('sprint_id')->nullable()->constrained('sprints')->nullOnDelete();
            $table->foreignId('version_id')->nullable()->constrained('release_versions')->nullOnDelete();
            $table->integer('order_index')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
        Schema::dropIfExists('release_versions');
        Schema::dropIfExists('sprints');
        Schema::dropIfExists('components');
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('projects');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'job_title', 'avatar', 'is_active']);
        });
    }
};

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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('github_repo')->nullable()->after('status');
            $table->string('github_webhook_secret')->nullable()->after('github_repo');
            $table->boolean('github_auto_move_pr')->default(true)->after('github_webhook_secret');
            $table->boolean('github_auto_close_pr')->default(true)->after('github_auto_move_pr');
        });

        Schema::create('github_pull_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained('issues')->nullOnDelete();
            $table->unsignedInteger('pr_number');
            $table->string('title');
            $table->string('html_url');
            $table->string('state')->default('open'); // open, closed, merged
            $table->string('user_username')->nullable();
            $table->string('user_avatar')->nullable();
            $table->string('branch_head')->nullable();
            $table->string('branch_base')->nullable();
            $table->boolean('is_merged')->default(false);
            $table->timestamp('merged_at')->nullable();
            $table->timestamp('pr_created_at')->nullable();
            $table->timestamp('pr_updated_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'pr_number']);
            $table->index('issue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_pull_requests');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'github_repo',
                'github_webhook_secret',
                'github_auto_move_pr',
                'github_auto_close_pr',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('id');
        });

        // Populate existing projects with unique pjt-XXXXXX code
        $projects = DB::table('projects')->get();
        $usedCodes = [];

        foreach ($projects as $project) {
            do {
                $code = 'pjt-' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($code, $usedCodes, true));

            $usedCodes[] = $code;
            DB::table('projects')->where('id', $project->id)->update(['code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};

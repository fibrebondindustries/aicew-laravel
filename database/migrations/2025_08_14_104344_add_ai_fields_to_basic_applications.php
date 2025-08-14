<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::table('basic_applications', function (Blueprint $table) {
//             $table->unsignedTinyInteger('ai_score')->nullable()->after('expected_salary');
//             $table->text('ai_summary')->nullable()->after('ai_score');
//             $table->string('job_role')->nullable()->after('job_id');
//             $table->unique('candidate_id');
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::table('basic_applications', function (Blueprint $table) {
//              $table->dropUnique(['candidate_id']);
//             $table->dropColumn(['ai_score', 'ai_summary', 'job_role']);

//         });
//     }
// };

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add columns if missing
        Schema::table('basic_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('basic_applications', 'ai_score')) {
                // use TINYINT for score 0–100; change if you need decimals
                $table->unsignedTinyInteger('ai_score')
                      ->nullable()
                      ->after('expected_salary'); // remove ->after() if that column may not exist
            }

            if (!Schema::hasColumn('basic_applications', 'ai_summary')) {
                $table->text('ai_summary')
                      ->nullable()
                      ->after('ai_score');
            }
        });

        // 2) Add unique index on candidate_id if not already present
        $indexName = 'basic_applications_candidate_id_unique';
        $exists = DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', 'basic_applications')
            ->where('index_name', $indexName)
            ->exists();

        if (!$exists) {
            Schema::table('basic_applications', function (Blueprint $table) use ($indexName) {
                $table->unique('candidate_id', $indexName);
            });
        }
    }

    public function down(): void
    {
        // Drop unique index if it exists
        $indexName = 'basic_applications_candidate_id_unique';
        $exists = DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', 'basic_applications')
            ->where('index_name', $indexName)
            ->exists();

        if ($exists) {
            Schema::table('basic_applications', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }

        // Drop only the columns this migration added
        Schema::table('basic_applications', function (Blueprint $table) {
            if (Schema::hasColumn('basic_applications', 'ai_summary')) {
                $table->dropColumn('ai_summary');
            }
            if (Schema::hasColumn('basic_applications', 'ai_score')) {
                $table->dropColumn('ai_score');
            }
        });
    }
};

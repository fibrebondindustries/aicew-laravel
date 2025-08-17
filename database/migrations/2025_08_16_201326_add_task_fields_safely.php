<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ----- basic_applications: code_zip, task_score, task_summary -----
        Schema::table('basic_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('basic_applications', 'code_zip')) {
                // path in storage (public) where candidate ZIP is stored
                $table->string('code_zip')->nullable()->after('resume_path');
            }

            if (! Schema::hasColumn('basic_applications', 'task_score')) {
                // AI or manual evaluation score (0–100 or decimals)
                $table->decimal('task_score', 5, 2)->nullable()->after('ai_summary');
            }

            if (! Schema::hasColumn('basic_applications', 'task_summary')) {
                // brief AI feedback or reviewer notes
                $table->longText('task_summary')->nullable()->after('task_score');
            }
        });

        // ----- resume_prompts: task_mode, task_prompt (optional) -----
        if (Schema::hasTable('resume_prompts')) {
            Schema::table('resume_prompts', function (Blueprint $table) {
                if (! Schema::hasColumn('resume_prompts', 'task_mode')) {
                    // 'ai' or 'manual'; default to manual for safety
                    $table->enum('task_mode', ['ai', 'manual'])->default('manual')->after('title');
                }

                if (! Schema::hasColumn('resume_prompts', 'task_prompt')) {
                    // prompt used when task_mode = 'ai'
                    $table->longText('task_prompt')->nullable()->after('task_mode');
                }
            });
        }
    }

    public function down(): void
    {
        // Rollback only the columns that exist

        if (Schema::hasTable('basic_applications')) {
            Schema::table('basic_applications', function (Blueprint $table) {
                if (Schema::hasColumn('basic_applications', 'task_summary')) {
                    $table->dropColumn('task_summary');
                }
                if (Schema::hasColumn('basic_applications', 'task_score')) {
                    $table->dropColumn('task_score');
                }
                if (Schema::hasColumn('basic_applications', 'code_zip')) {
                    $table->dropColumn('code_zip');
                }
            });
        }

        if (Schema::hasTable('resume_prompts')) {
            Schema::table('resume_prompts', function (Blueprint $table) {
                if (Schema::hasColumn('resume_prompts', 'task_prompt')) {
                    $table->dropColumn('task_prompt');
                }
                if (Schema::hasColumn('resume_prompts', 'task_mode')) {
                    $table->dropColumn('task_mode');
                }
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resume_prompts', function (Blueprint $table) {
            if (!Schema::hasColumn('resume_prompts', 'task_mode')) {
                $table->string('task_mode', 20)->default('manual')->after('title');
            }

            if (!Schema::hasColumn('resume_prompts', 'task_prompt')) {
                $table->longText('task_prompt')->nullable()->after('task_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resume_prompts', function (Blueprint $table) {
            if (Schema::hasColumn('resume_prompts', 'task_prompt')) {
                $table->dropColumn('task_prompt');
            }
            if (Schema::hasColumn('resume_prompts', 'task_mode')) {
                $table->dropColumn('task_mode');
            }
        });
    }
};

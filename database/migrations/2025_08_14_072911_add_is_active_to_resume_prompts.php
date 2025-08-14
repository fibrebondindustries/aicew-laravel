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
        Schema::table('resume_prompts', function (Blueprint $table) {
             if (! Schema::hasColumn('resume_prompts', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('prompt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_prompts', function (Blueprint $table) {
           if (Schema::hasColumn('resume_prompts', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};

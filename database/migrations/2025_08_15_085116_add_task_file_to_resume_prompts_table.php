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
            $table->string('task_file_path')->nullable()->after('prompt');     // storage path (relative to /storage)
            $table->string('task_file_name')->nullable()->after('task_file_path');
            $table->string('task_file_mime')->nullable()->after('task_file_name');
            $table->unsignedBigInteger('task_file_size')->nullable()->after('task_file_mime'); // bytes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_prompts', function (Blueprint $table) {
            $table->dropColumn(['task_file_path','task_file_name','task_file_mime','task_file_size']);
        });
    }
};

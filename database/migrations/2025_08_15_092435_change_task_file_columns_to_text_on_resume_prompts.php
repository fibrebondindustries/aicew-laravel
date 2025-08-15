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
            // switch to TEXT so we can store JSON arrays (multiple files)
             $table->text('task_file_path')->nullable()->change();
            $table->text('task_file_name')->nullable()->change();
            $table->text('task_file_mime')->nullable()->change();
            $table->text('task_file_size')->nullable()->change(); // from unsignedBigInteger -> TEXT
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_prompts', function (Blueprint $table) {
            $table->string('task_file_path', 255)->nullable()->change();
            $table->string('task_file_name', 255)->nullable()->change();
            $table->string('task_file_mime', 255)->nullable()->change();
            $table->unsignedBigInteger('task_file_size')->nullable()->change();
        });
    }
};

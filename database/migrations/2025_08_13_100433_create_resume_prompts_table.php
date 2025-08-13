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
       Schema::create('resume_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique(); // JOB1, JOB2 etc.
            $table->string('title');
            $table->text('prompt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_prompts');
    }
};

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
         Schema::create('basic_applications', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_id')->unique();       // e.g., FBI101
            $table->string('full_name');
            $table->string('email');
            $table->string('mobile', 20);
            $table->string('gender', 20)->nullable();
            $table->string('location')->nullable();
            $table->decimal('years_of_experience', 4, 1)->nullable();
            $table->decimal('current_salary', 12, 2)->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->string('notice_period')->nullable();     // "30 days", "Immediate", etc.
            $table->string('portfolio_link')->nullable();
            $table->string('resume_path')->nullable();       // storage path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_applications');
    }
};

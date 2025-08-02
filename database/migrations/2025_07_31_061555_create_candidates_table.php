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
            Schema::create('candidates', function (Blueprint $table) {
                $table->id();
                $table->string('candidate_id')->unique();  // FBI101 etc.
                $table->unsignedBigInteger('job_id');
                $table->string('name');
                $table->string('job_role');               // from form
                $table->string('experience');             // from form
                $table->string('resume');                 // file path
                $table->string('email');
                $table->string('phone');
                $table->decimal('score', 5, 2)->nullable();   // from AICEW API
                $table->text('summary')->nullable();          // from AICEW API
                $table->timestamps();
            });
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

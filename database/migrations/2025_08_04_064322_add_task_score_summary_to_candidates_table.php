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
        Schema::table('candidates', function (Blueprint $table) {
          $table->float('task_score')->nullable()->after('task_id');      // Add after task_id
          $table->text('task_summary')->nullable()->after('task_score');  // Add after task_score
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('task_score');
            $table->dropColumn('task_summary');
        });
    }
};

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
        Schema::table('basic_applications', function (Blueprint $table) {
            $table->boolean('mail_sent')->default(false)->after('ai_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_applications', function (Blueprint $table) {
            $table->dropColumn('mail_sent');
        });
    }
};

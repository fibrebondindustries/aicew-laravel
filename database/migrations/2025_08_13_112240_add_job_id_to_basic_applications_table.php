<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('basic_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('basic_applications', 'job_id')) {
                $table->string('job_id', 50)
                    ->nullable()
                    ->index()
                    ->after('candidate_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('basic_applications', function (Blueprint $table) {
            if (Schema::hasColumn('basic_applications', 'job_id')) {
                $table->dropColumn('job_id');
            }
        });
    }
};

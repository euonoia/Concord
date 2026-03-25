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
        Schema::table('available_jobs_hr4', function (Blueprint $table) {
            $table->string('specialization_name')->nullable()->after('department');
            $table->unsignedBigInteger('position_id')->nullable()->after('specialization_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_jobs_hr4', function (Blueprint $table) {
            $table->dropColumn(['specialization_name', 'position_id']);
        });
    }
};

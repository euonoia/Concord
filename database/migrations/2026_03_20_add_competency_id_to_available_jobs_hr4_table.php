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
            if (!Schema::hasColumn('available_jobs_hr4', 'competency_id')) {
                $table->unsignedBigInteger('competency_id')->nullable()->after('position_id');
                // Foreign key will be added only if competencies_hr2 table exists
                try {
                    if (Schema::hasTable('competencies_hr2')) {
                        $table->foreign('competency_id')->references('id')->on('competencies_hr2')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // Table doesn't exist yet, skip foreign key
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('available_jobs_hr4', function (Blueprint $table) {
            if (Schema::hasColumn('available_jobs_hr4', 'competency_id')) {
                try {
                    $table->dropForeign(['competency_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, skip
                }
                $table->dropColumn('competency_id');
            }
        });
    }
};

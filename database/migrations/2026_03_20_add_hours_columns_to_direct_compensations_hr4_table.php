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
        Schema::table('direct_compensations_hr4', function (Blueprint $table) {
            // Add hours tracking columns
            if (!Schema::hasColumn('direct_compensations_hr4', 'worked_hours')) {
                $table->decimal('worked_hours', 8, 2)->default(0)->after('overtime_pay')->comment('Total hours worked for month');
            }
            
            if (!Schema::hasColumn('direct_compensations_hr4', 'overtime_hours')) {
                $table->decimal('overtime_hours', 8, 2)->default(0)->after('worked_hours')->comment('Overtime hours for month');
            }
            
            if (!Schema::hasColumn('direct_compensations_hr4', 'night_diff_hours')) {
                $table->decimal('night_diff_hours', 8, 2)->default(0)->after('overtime_hours')->comment('Night differential hours for month');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direct_compensations_hr4', function (Blueprint $table) {
            $table->dropColumn(['worked_hours', 'overtime_hours', 'night_diff_hours']);
        });
    }
};

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
        Schema::table('attendance_logs_hr3', function (Blueprint $table) {
            // Add hours tracking columns if they don't exist
            if (!Schema::hasColumn('attendance_logs_hr3', 'worked_hours')) {
                $table->decimal('worked_hours', 8, 2)->default(0)->after('clock_out')->comment('Total hours worked');
            }
            
            if (!Schema::hasColumn('attendance_logs_hr3', 'overtime_hours')) {
                $table->decimal('overtime_hours', 8, 2)->default(0)->after('worked_hours')->comment('Overtime hours (beyond 8 hours)');
            }
            
            if (!Schema::hasColumn('attendance_logs_hr3', 'night_diff_hours')) {
                $table->decimal('night_diff_hours', 8, 2)->default(0)->after('overtime_hours')->comment('Night shift hours (10 PM - 6 AM)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs_hr3', function (Blueprint $table) {
            $table->dropColumn(['worked_hours', 'overtime_hours', 'night_diff_hours']);
        });
    }
};

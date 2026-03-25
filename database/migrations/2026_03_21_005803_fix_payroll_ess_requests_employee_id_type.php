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
        Schema::table('payroll_ess_requests_hr4', function (Blueprint $table) {
            // Change employee_id from unsignedBigInteger to string to match HR2 format
            // HR2 uses string format like 'GEN-0001', 'HR-0001', etc.
            $table->string('employee_id', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_ess_requests_hr4', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->change();
        });
    }
};

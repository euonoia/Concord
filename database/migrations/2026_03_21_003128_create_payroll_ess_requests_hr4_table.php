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
        Schema::create('payroll_ess_requests_hr4', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('Employee ID from employees table');
            $table->string('request_type')->default('payroll')->comment('Type of request: payroll, bonus, deduction, etc');
            $table->text('details')->nullable()->comment('Details of the request');
            $table->string('status')->default('pending')->comment('Status: pending, approved, rejected');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('User ID who approved the request');
            $table->text('approval_notes')->nullable()->comment('Notes from approver');
            $table->date('requested_date')->nullable()->comment('Date of request');
            $table->dateTime('approved_date')->nullable()->comment('Date of approval/rejection');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('employee_id');
            $table->index('status');
            $table->index('requested_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_ess_requests_hr4');
    }
};

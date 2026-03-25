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
        Schema::create('payroll_request_hr2', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_id')->comment('Employee ID from HR2');
            $table->text('details')->nullable()->comment('Details of the payroll request');
            $table->decimal('salary', 12, 2)->nullable()->comment('Gross Salary requested');
            $table->decimal('net_pay', 12, 2)->nullable()->comment('Net Pay for the employee');
            $table->string('status')->default('pending')->comment('Status: pending, approved, rejected');
            $table->string('request_type')->default('payroll')->comment('Type of request: payroll, bonus, deduction, etc');
            $table->timestamps();
            
            // Indexes
            $table->index('employee_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_request_hr2');
    }
};

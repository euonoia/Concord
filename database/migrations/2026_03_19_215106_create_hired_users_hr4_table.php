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
        Schema::create('hired_users_hr4', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hr4_job_id')->nullable();
            $table->string('employee_id');
            $table->string('full_name');
            $table->timestamp('hired_at')->useCurrent();
            $table->timestamps();

            // Note: Since hr4_job_id comes from available_jobs_hr4 which might not have a formal relationship in Laravel yet, 
            // we'll keep it as a loose link or add a foreign key if appropriate.
            // For now, keeping it as an unsignedBigInteger is safest for cross-module compatibility.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hired_users_hr4');
    }

};

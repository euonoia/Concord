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
        Schema::create('onboarding_assessments_hr1', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('job_posting_id')->nullable();
            $table->string('application_id', 50)->nullable();
            $table->string('first_name', 150);
            $table->string('last_name', 150);
            $table->string('email', 100);
            $table->string('phone', 20)->nullable();
            $table->string('department_id', 50)->nullable();
            $table->integer('position_id')->nullable();
            $table->string('specialization', 100)->nullable();
            $table->enum('post_grad_status', ['intern', 'residency', 'fellowship']);
            $table->enum('application_status', ['pending', 'under_review', 'interview', 'accepted', 'rejected', 'onboarding'])->default('pending');
            $table->string('resume_path', 512)->nullable();
            $table->dateTime('applied_at')->useCurrent();
            
            // HR2 assessment fields
            $table->enum('assessment_status', ['pending', 'scheduled', 'passed', 'failed'])->default('pending');
            $table->dateTime('interview_date')->nullable();
            $table->string('interviewer', 150)->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('applicant_id', 'fk_onboarding_hr1_applicant')
                ->references('id')
                ->on('applicants_hr1')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_assessments_hr1');
    }
};

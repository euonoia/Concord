<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications_hr1', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_hr1')->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained('job_postings_hr1')->onDelete('cascade');
            $table->enum('status', ['Applicant', 'Candidate', 'Probation', 'Regular', 'Rejected'])->default('Applicant');
            $table->date('applied_date');
            $table->dateTime('interview_date')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_description')->nullable();
            $table->text('documents')->nullable(); // JSON array of file paths
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications_hr1');
    }
};


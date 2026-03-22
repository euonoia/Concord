<?php

namespace App\Models\admin\Hr\hr1;

use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr1\Applicant; // Note: I should check if this exists

class OnboardingAssessment extends Model
{
    protected $table = 'onboarding_assessments_hr1';

    protected $fillable = [
        'applicant_id',
        'job_posting_id',
        'application_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'department_id',
        'position_id',
        'specialization',
        'post_grad_status',
        'application_status',
        'resume_path',
        'applied_at',
        'assessment_status',
        'interview_date',
        'interviewer',
        'remarks',
        'is_validated',
        'validated_by'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'interview_date' => 'datetime',
    ];

    // Relationship removed as Applicant model not found. Use DB facade if needed.
}

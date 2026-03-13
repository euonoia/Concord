<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr1\ApplicantHr1;
use App\Models\Employee; // Ensure this path to your Employee model is correct

class InterviewScheduleHr3 extends Model
{
    protected $table = 'interview_schedule_hr3';

    protected $fillable = [
        'applicant_id', 
        'schedule_date', 
        'schedule_time', 
        'location', 
        'notes', 
        'validated_by'
    ];

    public function validator()
    {
     
        return $this->belongsTo(Employee::class, 'validated_by', 'employee_id');
    }

    public function applicant()
    {
        return $this->belongsTo(ApplicantHr1::class, 'applicant_id');
    }
}
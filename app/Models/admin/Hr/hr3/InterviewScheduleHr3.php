<?php
namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewScheduleHr3 extends Model
{
    use HasFactory;

    protected $table = 'interview_schedule_hr3';

    protected $fillable = [
        'applicant_id',
        'schedule_date',
        'schedule_time',
        'location',
        'notes',
        'validated_by'
    ];

    public function applicant()
    {
        return $this->belongsTo(
            \App\Models\admin\Hr\hr1\ApplicantHr1::class,
            'applicant_id'
        );
    }
}
<?php

namespace App\Models\hr1;

use App\Models\hr1\User;
use App\Models\hr1\JobPosting_hr1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application_hr1 extends Model
{
    use HasFactory;

    protected $table = 'applications_hr1';

    protected $fillable = [
        'user_id',
        'job_posting_id',
        'status',
        'applied_date',
        'interview_date',
        'interview_location',
        'interview_description',
        'documents',
    ];

    protected $casts = [
        'applied_date' => 'date',
        'interview_date' => 'datetime',
        'documents' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPosting_hr1()
    {
        return $this->belongsTo(JobPosting_hr1::class, 'job_posting_id');
    }

    public function getJobTitleAttribute()
    {
        return $this->jobPosting_hr1->title ?? '';
    }

    public function getDepartmentAttribute()
    {
        return $this->jobPosting_hr1->department ?? '';
    }
}


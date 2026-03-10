<?php

namespace App\Models\hr1;

use App\Models\hr1\Application_hr1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting_hr1 extends Model
{
    use HasFactory;

    protected $table = 'job_postings_hr1';

    protected $fillable = [
        'title',
        'department',
        'location',
        'type',
        'status',
        'posted_date',
        'description',
    ];

    protected $casts = [
        'posted_date' => 'date',
    ];

    public function applications_hr1()
    {
        return $this->hasMany(Application_hr1::class, 'job_posting_id');
    }
}


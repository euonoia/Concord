<?php

namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AvailableJob extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Connect to TiDB cloud
    protected $table = 'available_jobs_hr4';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'department',
        'description',
        'requirements',
        'salary_range',
        'status',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];

    // Relationship to user who posted
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
<?php

namespace App\Models\admin\Hr\hr1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantHr1 extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'applicants_hr1';

    // Primary key
    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
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
    ];

    // Automatically manage created_at & updated_at
    public $timestamps = true;

    // Optional: cast dates
    protected $dates = [
        'applied_at',
        'created_at',
        'updated_at',
    ];
}
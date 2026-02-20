<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\LearningModule;

class CourseEnroll extends Model
{
    use HasFactory;

    protected $table = 'course_enrolls_hr2';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'course_id',
        'status',
    ];

    // Relationship back to course
    public function course()
    {
        return $this->belongsTo(LearningModule::class, 'course_id', 'id');
    }
}

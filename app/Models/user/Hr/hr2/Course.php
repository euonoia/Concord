<?php

namespace App\Models\Hr2;

use Illuminate\Database\Eloquent\Model;
use App\Models\hr2\CourseEnroll;

class Course extends Model
{
    protected $table = 'courses_hr2';

    protected $primaryKey = 'course_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    // Relationship to course enrollments
    public function enrolls()
    {
        return $this->hasMany(CourseEnroll::class, 'course_id', 'course_id');
    }

    // Use course_id for route model binding
    public function getRouteKeyName(): string
    {
        return 'course_id';
    }
}

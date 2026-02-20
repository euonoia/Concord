<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\CourseEnroll;

class LearningModule extends Model
{
    use HasFactory;

    protected $table = 'learning_modules_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'competency_id',
        'learning_type',
        'duration',
    ];

    // Relationship to enrolls
    public function enrolls()
    {
        return $this->hasMany(CourseEnroll::class, 'course_id', 'id');
    }
}

<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    use HasFactory;

    protected $table = 'learning_modules_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'module_code',
        'module_name',
        'dept_code',
        'specialization_name',
        'module_type',
        'duration_hours',
        'is_mandatory',
    ];

    // Relationship to employee enrollments
    public function enrolls()
    {
        return $this->hasMany(CourseEnroll::class, 'module_id', 'id');
    }
}
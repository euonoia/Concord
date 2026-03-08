<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnroll extends Model
{
    use HasFactory;

    protected $table = 'employee_learning_assignments_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'module_id',
        'assigned_date',
        'due_date',
        'status',
        'progress_percentage',
        'completed_at'
    ];

    public function module()
    {
        return $this->belongsTo(LearningModule::class, 'module_id', 'id');
    }
}
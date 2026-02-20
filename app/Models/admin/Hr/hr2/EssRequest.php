<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\EssRequestArchive;
use App\Models\Employee; 

class EssRequest extends Model
{
    use HasFactory;

    protected $table = 'ess_request_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'type',
        'details',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Archive request
   public function archive()
    {
    
        EssRequestArchive::create([
            'ess_id' => $this->id,
            'employee_id' => $this->employee_id,
            'type' => $this->type,
            'details' => $this->details,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'archived_at' => now(),
        ]);

        $this->delete();
    }
}

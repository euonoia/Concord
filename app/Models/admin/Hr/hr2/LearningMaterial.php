<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    protected $table = 'learning_materials_hr2';

    protected $fillable = [
        'module_code',
        'title',
        'url',
        'file_path',
        'type'
    ];

    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function module()
    {
        return $this->belongsTo(
            LearningModule::class,
            'module_code',     // foreign key in this table
            'module_code'      // referenced key in learning_modules_hr2
        );
    }
}
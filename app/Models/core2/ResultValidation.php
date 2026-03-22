<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class ResultValidation extends Model
{
    protected $table = 'result_validation_core2';

    protected $fillable = [
        'result_id',
        'sample_id',
        'test_result',
        'validated_by',
    ];
}

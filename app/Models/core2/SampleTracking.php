<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class SampleTracking extends Model
{
    protected $table = 'sample_tracking_core2';

    protected $fillable = [
        'sample_id',
        'test_order_id',
        'status',
        'lab_id',
    ];
}

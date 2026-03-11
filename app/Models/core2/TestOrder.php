<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class TestOrder extends Model
{
    protected $table = 'test_orders_core2';

    protected $fillable = [
        'order_id',
        'patient_id',
        'test_id',
        'date_ordered',
    ];
}

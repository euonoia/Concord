<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    use HasFactory;

    protected $table = 'meal_logs_core2';

    protected $fillable = [
        'diet_order_id',
        'encounter_id',
        'meal_type',
        'delivery_status',
        'delivered_at',
        'delivered_by',
    ];
}

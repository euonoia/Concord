<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class HouseKeepingStatus extends Model
{
    protected $table = 'house_keeping_status_core2';

    protected $fillable = [
        'house_keeping_id',
        'room_id',
        'bed_id',
        'status',
        'last_cleaned_date',
    ];
}

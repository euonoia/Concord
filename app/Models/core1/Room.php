<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\Core1\RoomFactory> */
    use HasFactory;

    protected $table = 'rooms_core1';

    protected $fillable = [
        'ward_id',
        'room_number',
        'room_type',
        'status',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}

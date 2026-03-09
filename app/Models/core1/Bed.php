<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    /** @use HasFactory<\Database\Factories\Core1\BedFactory> */
    use HasFactory;

    protected $table = 'beds_core1';

    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}

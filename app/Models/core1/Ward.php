<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    /** @use HasFactory<\Database\Factories\Core1\WardFactory> */
    use HasFactory;

    protected $table = 'wards_core1';

    protected $fillable = [
        'name',
        'description',
        'capacity',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}

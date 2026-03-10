<?php

namespace App\Models\hr1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recognition_hr1 extends Model
{
    use HasFactory;

    protected $table = 'recognitions_hr1';

    protected $fillable = [
        'from',
        'to',
        'reason',
        'award_type',
        'date',
        'congratulations',
        'boosts',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}


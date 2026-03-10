<?php

namespace App\Models\hr1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardCategory_hr1 extends Model
{
    use HasFactory;

    protected $table = 'award_categories_hr1';

    protected $fillable = [
        'name',
        'icon',
    ];
}


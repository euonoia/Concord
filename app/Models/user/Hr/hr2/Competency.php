<?php

namespace App\Models\user\Hr\Hr2;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $table = 'competencies_hr2'; // rename to your table
    protected $primaryKey = 'id'; // default primary key
    protected $guarded = []; // allow mass assignment
}

<?php

namespace App\Models\Hr2;

use Illuminate\Database\Eloquent\Model;

class EssRequest extends Model
{
    protected $table = 'ess_request_hr2';
    protected $primaryKey = 'ess_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];
}


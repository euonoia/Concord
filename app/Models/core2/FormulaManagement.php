<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class FormulaManagement extends Model
{
    protected $table = 'formula_management_core2';

    protected $fillable = [
        'formula_id',
        'formula_name',
        'ingredients_list',
        'drug_id',
    ];
}

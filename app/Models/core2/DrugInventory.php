<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class DrugInventory extends Model
{
    protected $table = 'drug_inventory_core2';

    protected $fillable = [
        'drug_num',
        'drug_name',
        'quantity'
    
        
    ];
}

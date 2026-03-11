<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class PackageDefinitionPricing extends Model
{
    protected $table = 'package_definition_pricing_core2';

    protected $fillable = [
        'package_id',
        'package_name',
        'price',
        'includes_services',
    ];
}

<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageDefinitionPricing extends Model
{
    use SoftDeletes;

    protected $table = 'package_definition_pricing_core2';

    protected $fillable = [
        'package_identifier',
        'package_description',
        'price_list_node',
        'included_services_state',
        'excluded_services_state',
        'status',
    ];

    protected $casts = [
        'included_services_state' => 'array',
        'excluded_services_state' => 'array',
        'price_list_node' => 'decimal:2',
        'created_at' => 'datetime',
    ];
}
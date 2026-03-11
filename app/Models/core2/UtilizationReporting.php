<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class UtilizationReporting extends Model
{
    protected $table = 'utilization_reporting_core2';

    protected $fillable = [
        'report_id',
        'module_name',
        'usage_metrics',
        'reporting_period',
    ];
}

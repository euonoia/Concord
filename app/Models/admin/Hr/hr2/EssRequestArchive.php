<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EssRequestArchive extends Model
{
    use HasFactory;

    protected $table = 'ess_request_archive_hr2';
    public $timestamps = true;

    protected $fillable = [
        'ess_id',
        'employee_id',
        'type',
        'details',
        'status',
        'archived_at',
    ];
}


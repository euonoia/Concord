<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    /** @use HasFactory<\Database\Factories\Core1\AdmissionFactory> */
    use HasFactory;

    protected $table = 'admissions_core1';

    protected $fillable = [
        'encounter_id',
        'bed_id',
        'admission_date',
        'discharge_date',
        'status',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }
}

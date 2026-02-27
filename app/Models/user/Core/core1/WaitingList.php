<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    protected $table = 'waiting_lists_core1';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'preferred_date',
        'preferred_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}

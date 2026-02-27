<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills_core1';

    protected $fillable = [
        'bill_number',
        'patient_id',
        'bill_date',
        'due_date',
        'items',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

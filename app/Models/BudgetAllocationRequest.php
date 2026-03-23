<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetAllocationRequest extends Model
{
    use HasFactory;

    protected $table = 'budget_allocation_requests';

    protected $fillable = [
        'user_id',
        'month',
        'total_compensation',
        'status',
        'note',
    ];

    protected $casts = [
        'total_compensation' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models\hr1;

use App\Models\hr1\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTask_hr1 extends Model
{
    use HasFactory;

    protected $table = 'onboarding_tasks_hr1';

    protected $fillable = [
        'title',
        'department',
        'category',
        'completed',
        'assigned_to',
        'user_id',
        'required_for_phase',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


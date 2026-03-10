<?php

namespace App\Models\hr1;

use App\Models\hr1\Application_hr1;
use App\Models\hr1\LearningModule_hr1;
use App\Models\hr1\OnboardingTask_hr1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users_hr1';

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'role',
        'position',
        'status',
        'applied_date',
        'score',
        'skills',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'applied_date' => 'date',
        'password' => 'hashed',
    ];

    public function applications_hr1()
    {
        return $this->hasMany(Application_hr1::class);
    }

    public function onboardingTasks_hr1()
    {
        return $this->hasMany(OnboardingTask_hr1::class);
    }

    public function learningModules_hr1()
    {
        return $this->belongsToMany(LearningModule_hr1::class, 'user_learning_modules_hr1', 'user_id', 'learning_module_id')
            ->withPivot('completed')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }
}


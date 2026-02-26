<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users_core1';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'department',
        'specialization',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isHeadNurse(): bool
    {
        return $this->role === 'head_nurse';
    }

    public function isNurse(): bool
    {
        return in_array($this->role, ['nurse', 'head_nurse']);
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isBilling(): bool
    {
        return $this->role === 'billing';
    }
}

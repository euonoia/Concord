<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Employee;
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $keyType = 'int';
    public $incrementing = true; 

    protected $fillable = [
        'uuid',
        'username',
        'email',
        'password',
        'user_type',
        'role_slug',
        'is_active',
        'last_login_at',
        'last_login_ip'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to the Employee Profile
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }

    /**
     * Role Helper Methods
     */
    public function isAdmin(): bool
    {
        return in_array($this->role_slug, ['admin', 'sys_super_admin', 'core_admin']);
    }

    public function isDoctor(): bool
    {
        return $this->role_slug === 'doctor';
    }

    public function isHeadNurse(): bool
    {
        return $this->role_slug === 'head_nurse';
    }

    public function isNurse(): bool
    {
        return in_array($this->role_slug, ['nurse', 'head_nurse']);
    }

    public function isPatient(): bool
    {
        return in_array($this->role_slug, ['patient', 'patient_guardian']);
    }

    public function isReceptionist(): bool
    {
        return $this->role_slug === 'receptionist';
    }

    public function isBilling(): bool
    {
        return $this->role_slug === 'billing';
    }

    /**
     * Virtual attribute for 'role' mapping to 'role_slug'
     * for backward compatibility in controllers.
     */
    public function getRoleAttribute()
    {
        return $this->role_slug;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
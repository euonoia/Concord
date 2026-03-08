<?php

namespace App\Policies;

use App\Models\User;
use App\Models\user\Core\core1\Patient;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role_slug, ['admin', 'receptionist', 'doctor', 'nurse', 'head_nurse']);
    }

    public function view(User $user, Patient $patient): bool
    {
        return in_array($user->role_slug, ['admin', 'receptionist', 'doctor', 'nurse', 'head_nurse']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role_slug, ['admin', 'receptionist']);
    }

    public function update(User $user, Patient $patient): bool
    {
        return in_array($user->role_slug, ['admin', 'receptionist']);
    }

    public function completeRegistration(User $user, Patient $patient): bool
    {
        return in_array($user->role_slug, ['admin', 'receptionist']);
    }

    public function merge(User $user): bool
    {
        return in_array($user->role_slug, ['admin', 'receptionist']);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->role_slug === 'admin';
    }
}

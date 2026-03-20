<?php

namespace App\Policies;

use App\Models\ClassRegistration;
use App\Models\User;

class ClassRegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'accountant', 'secretary', 'employee', 'parent_student']);
    }

    public function view(User $user, ClassRegistration $registration): bool
    {
        if ($user->hasRole('parent_student')) {
            return $registration->user_id === $user->id;
        }

        return $user->hasAnyRole(['admin', 'accountant', 'secretary', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('parent_student') && $user->verified;
    }

    public function update(User $user, ClassRegistration $registration): bool
    {
        return false;
    }

    public function delete(User $user, ClassRegistration $registration): bool
    {
        return $user->hasRole('admin');
    }

    public function updateStatus(User $user, ClassRegistration $registration): bool
    {
        return $user->hasAnyRole(['admin', 'secretary']);
    }
}
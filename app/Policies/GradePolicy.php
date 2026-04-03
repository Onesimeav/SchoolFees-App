<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'accountant', 'secretary', 'employee']);
    }

    public function view(User $user, Grade $grade): bool
    {
        return $user->hasAnyRole(['admin', 'accountant', 'secretary', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'secretary']);
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->hasAnyRole(['admin', 'secretary']);
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->hasRole('admin');
    }
}
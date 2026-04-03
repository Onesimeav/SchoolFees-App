<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if user can view any users.
     * Admin, Secretary, Employee can view user list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'secretary', 'employee']);
    }

    /**
     * Determine if user can view a specific user.
     * Admin, Secretary can view any user.
     * Others can only view their own profile.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->hasAnyRole(['admin', 'secretary'])) {
            return true;
        }

        return $user->id === $model->id; // Can view own profile
    }

    /**
     * Determine if user can create users.
     * Only Admin and Secretary can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'secretary']);
    }

    /**
     * Determine if user can update a user.
     * Admin, Secretary can update any user.
     * Others can only update their own profile.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->hasAnyRole(['admin', 'secretary'])) {
            return true;
        }

        return $user->id === $model->id; // Can update own profile
    }

    /**
     * Determine if user can delete a user.
     * Only Admin can delete users.
     * Cannot delete self.
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->hasRole('admin')) {
            return false;
        }

        return $user->id !== $model->id; // Cannot delete self
    }

    /**
     * Determine if user can restore a deleted user.
     * Only Admin can restore users.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if user can permanently delete a user.
     * Only Admin can force delete users.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}

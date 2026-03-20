<?php

namespace App\Policies;

use App\Models\Fee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FeePolicy
{
    /**
     * Determine if user can view any fees.
     * All authenticated users can view fees.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can see fee list
    }

    /**
     * Determine if user can view a specific fee.
     * All authenticated users can view fees.
     */
    public function view(User $user, Fee $fee): bool
    {
        return true; // All authenticated users can view fee details
    }

    /**
     * Determine if user can create fees.
     * Only Admin and Accountant can create fees.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'accountant', 'secretary']);
    }

    /**
     * Determine if user can update fees.
     * Admin, Accountant, and Secretary can update fees.
     */
    public function update(User $user, Fee $fee): bool
    {
        return $user->hasAnyRole(['admin', 'accountant', 'secretary']);
    }

    /**
     * Determine if user can delete fees.
     * Admin and Secretary can delete fees.
     */
    public function delete(User $user, Fee $fee): bool
    {
        return $user->hasAnyRole(['admin', 'secretary']);
    }

    /**
     * Determine if user can restore deleted fees.
     * Admin and Secretary can restore fees.
     */
    public function restore(User $user, Fee $fee): bool
    {
        return $user->hasAnyRole(['admin', 'secretary']);
    }

    /**
     * Determine if user can permanently delete fees.
     * Only Admin can force delete fees.
     */
    public function forceDelete(User $user, Fee $fee): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if user can approve fees.
     * Custom permission for fee approval workflow.
     * Only Admin can approve fees.
     */
    public function approve(User $user, Fee $fee): bool
    {
        return $user->hasRole('admin');
    }
}

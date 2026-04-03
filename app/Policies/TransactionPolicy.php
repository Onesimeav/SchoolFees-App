<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine if user can view any transactions.
     * All authenticated users can view transactions.
     * Note: Query scoping is handled in Filament resources.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users, but scoped by role in resources
    }

    /**
     * Determine if user can view a specific transaction.
     * Admin/Accountant/Secretary can view all transactions.
     * Students can only view their own transactions.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Admin, Accountant, Secretary can view all
        if ($user->hasAnyRole(['admin', 'accountant', 'secretary'])) {
            return true;
        }

        // Students can only view their own transactions
        return $transaction->user_id === $user->id;
    }

    /**
     * Determine if user can create transactions.
     * Only Admin and Accountant can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'accountant']);
    }

    /**
     * Determine if user can update transactions.
     * Only Admin and Accountant can update transactions.
     * Can only update pending transactions.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        if (!$user->hasAnyRole(['admin', 'accountant'])) {
            return false;
        }

        // Can only update pending transactions
        return $transaction->status === 'pending';
    }

    /**
     * Determine if user can delete transactions.
     * Only Admin can delete transactions.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if user can process (complete) transactions.
     * Custom permission - Admin and Accountant can process.
     * Transaction must be pending.
     */
    public function process(User $user, Transaction $transaction): bool
    {
        if (!$user->hasAnyRole(['admin', 'accountant'])) {
            return false;
        }

        // Can only process pending transactions
        return $transaction->status === 'pending';
    }

    /**
     * Determine if user can refund transactions.
     * Custom permission - Only Admin can refund.
     * Transaction must be completed.
     */
    public function refund(User $user, Transaction $transaction): bool
    {
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Can only refund completed transactions
        return $transaction->status === 'completed';
    }

    /**
     * Determine if user can restore deleted transactions.
     * Only Admin can restore transactions.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if user can permanently delete transactions.
     * Only Admin can force delete transactions.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->hasRole('admin');
    }
}

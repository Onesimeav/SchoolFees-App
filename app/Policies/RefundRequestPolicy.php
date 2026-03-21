<?php

namespace App\Policies;

use App\Models\RefundRequest;
use App\Models\User;

class RefundRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RefundRequest $refundRequest): bool
    {
        if ($user->hasAnyRole(['admin', 'accountant', 'secretary', 'employee'])) {
            return true;
        }

        return $user->hasRole('parent_student') && $refundRequest->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('parent_student') && $user->verified;
    }

    public function update(User $user, RefundRequest $refundRequest): bool
    {
        return $user->hasAnyRole(['admin', 'accountant']);
    }

    public function delete(User $user, RefundRequest $refundRequest): bool
    {
        return $user->hasRole('admin');
    }

    public function accept(User $user, RefundRequest $refundRequest): bool
    {
        return $user->hasAnyRole(['admin', 'accountant'])
            && $refundRequest->status === 'pending';
    }

    public function refuse(User $user, RefundRequest $refundRequest): bool
    {
        return $user->hasAnyRole(['admin', 'accountant'])
            && $refundRequest->status === 'pending';
    }
}
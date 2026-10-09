<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /*
    |--------------------------------------------------------------------------
    | View User Accounts
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->role === 'system_admin';
    }


    /*
    |--------------------------------------------------------------------------
    | View Individual Account
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        User $targetUser
    ): bool {
        return $user->role === 'system_admin';
    }


    /*
    |--------------------------------------------------------------------------
    | Approve Account
    |--------------------------------------------------------------------------
    */

    public function approve(
        User $user,
        User $targetUser
    ): bool {
        if ($user->role !== 'system_admin') {
            return false;
        }

        if ($targetUser->id === $user->id) {
            return false;
        }

        return $targetUser->account_status === 'pending';
    }


    /*
    |--------------------------------------------------------------------------
    | Reject Account
    |--------------------------------------------------------------------------
    */

    public function reject(
        User $user,
        User $targetUser
    ): bool {
        if ($user->role !== 'system_admin') {
            return false;
        }

        if ($targetUser->id === $user->id) {
            return false;
        }

        return $targetUser->account_status === 'pending';
    }


    /*
    |--------------------------------------------------------------------------
    | Suspend Account
    |--------------------------------------------------------------------------
    */

    public function suspend(
        User $user,
        User $targetUser
    ): bool {
        if ($user->role !== 'system_admin') {
            return false;
        }

        if ($targetUser->id === $user->id) {
            return false;
        }

        return
            $targetUser->account_status === 'active' &&
            $targetUser->is_active;
    }


    /*
    |--------------------------------------------------------------------------
    | Reactivate Account
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        User $user,
        User $targetUser
    ): bool {
        if ($user->role !== 'system_admin') {
            return false;
        }

        if ($targetUser->id === $user->id) {
            return false;
        }

        return
            $targetUser->account_status === 'suspended' ||
            !$targetUser->is_active;
    }
}
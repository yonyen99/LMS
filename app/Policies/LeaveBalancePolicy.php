<?php

namespace App\Policies;

use App\Models\User;

class LeaveBalancePolicy
{
    /**
     * Determine if the user can export leave balances
     */
    public function export(User $currentUser, User $targetUser): bool
    {
        // Admins can export any employee
        if ($currentUser->hasRole('Admin')) {
            return true;
        }

        // Managers can only export their own department's employees (non-managers)
        if ($currentUser->hasRole('Manager')) {
            return $currentUser->department_id === $targetUser->department_id 
                   && !$targetUser->hasRole('Manager') 
                   && !$targetUser->hasRole('Admin');
        }

        return false;
    }
}
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
        // Admins can export anyone
        if ($currentUser->hasRole('Admin')) {
            return true;
        }

        // Users can always export their own leave balance
        if ($currentUser->id === $targetUser->id) {
            return true;
        }

        // Managers can export employees in their department, but not Admins/Managers
        if ($currentUser->hasRole('Manager')) {
            return $currentUser->department_id === $targetUser->department_id
                && !$targetUser->hasRole('Admin')
                && !$targetUser->hasRole('Manager');
        }

        // Everyone else: denied
        return false;
    }
}

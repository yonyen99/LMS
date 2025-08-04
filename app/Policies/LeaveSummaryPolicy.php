<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveSummary;

class LeaveSummaryPolicy
{
    public function create(User $user)
    {
        return in_array($user->role, ['Admin', 'HR']);
    }

    public function update(User $user, LeaveSummary $leaveSummary)
    {
        return in_array($user->role, ['Admin', 'HR']);
    }

    public function delete(User $user, LeaveSummary $leaveSummary)
    {
        return in_array($user->role, ['Admin', 'HR']);
    }

    public function view(User $user, LeaveSummary $leaveSummary)
    {
        return true;
    }
}
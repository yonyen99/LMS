<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;

class LeaveRequestPolicy
{
    public function update(User $user, LeaveRequest $leaveRequest)
    {
        return $user->id === $leaveRequest->user_id;
    }

    public function delete(User $user, LeaveRequest $leaveRequest)
    {
        return $user->id === $leaveRequest->user_id;
    }

    public function cancel(User $user, LeaveRequest $leaveRequest)
    {
        return $user->id === $leaveRequest->user_id &&
            !in_array(strtolower($leaveRequest->status), ['accepted', 'rejected', 'canceled']);
    }

    public function updateStatus(User $user, LeaveRequest $leaveRequest)
    {
        return in_array($user->role, ['Super Admin', 'HR', 'Manager', 'Department Head', 'Team Lead']);
    }
}

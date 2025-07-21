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

    // Add this cancel method
    public function cancel(User $user, LeaveRequest $leaveRequest)
    {
        // For example: user can cancel if they own the request AND status is not accepted or rejected
        return $user->id === $leaveRequest->user_id
            && !in_array(strtolower($leaveRequest->status), ['accepted', 'rejected', 'canceled']);
    }
}

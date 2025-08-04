<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Log;

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
        return in_array($user->role, ['HR', 'Manager']);
    }

    public function export(User $user)
    {
        Log::info('Checking export permission for user: ' . $user->id . ' with roles: ' . implode(',', $user->roles->pluck('name')->toArray()));
        return $user->hasAnyRole(['Admin', 'Manager', 'HR', 'Employee']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestActionController extends Controller
{

    /**
     * Authorize the action based on the request signature or user role.
     * This method checks if the request has a valid signature or if the user has the appropriate role.
     * If neither condition is met, it aborts with a 403 Unauthorized response.
     *
     * @param Request $request
     */

    
    protected function authorizeAction(Request $request)
    {
        // Allow access if the request has a valid signature
        if ($request->hasValidSignature()) {
            return;
        }

        $user = auth()->user();

        if (!$user || !($user->hasRole('Admin') || $user->hasRole('Manager')) || $user->hasRole('HR')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Approve a leave request.
     * This method allows a user with the appropriate role to approve a leave request.
     * It updates the status of the leave request to 'Accepted' and saves the changes.
     * * @param Request $request
     */

    public function accept(Request $request, $id)
    {
        $this->authorizeAction($request);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'Accepted';
        $leaveRequest->last_changed_at = now();
        $leaveRequest->save();

        return view('emails.leave_action_response', [
            'message' => '✅ Leave request has been accepted.',
            'status' => 'Accepted',
        ]);
    }

    /**
     * Reject a leave request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View
     */

    public function reject(Request $request, $id)
    {
        $this->authorizeAction($request);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'Rejected';
        $leaveRequest->last_changed_at = now();
        $leaveRequest->save();

        return view('emails.leave_action_response', [
            'message' => '❌ Leave request has been rejected.',
            'status' => 'Rejected',
        ]);
    }

    /**
     * Cancel a leave request.
     * This method allows a user to cancel their leave request.
     * It updates the status of the leave request to 'Canceled' and saves the changes.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View
     */

    public function cancel(Request $request, $id)
    {
        $this->authorizeAction($request);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'Canceled';
        $leaveRequest->last_changed_at = now();
        $leaveRequest->save();

        return view('emails.leave_action_response', [
            'message' => '✅ Leave request has been canceled.',
            'status' => 'Canceled',
        ]);
    }
}

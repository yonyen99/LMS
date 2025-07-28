<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestActionController extends Controller
{
    protected function authorizeAction(Request $request)
    {
        // Allow access if the request has a valid signature
        if ($request->hasValidSignature()) {
            return;
        }

        $user = auth()->user();

        if (!$user || !($user->hasRole('Super Admin') || $user->hasRole('Manager'))) {
            abort(403, 'Unauthorized action.');
        }
    }

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
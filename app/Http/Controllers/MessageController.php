<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $query = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'Requested');

        // If the user is a manager, exclude their own requests
        if (auth()->user()->hasRole('Manager')) {
            $query->where('user_id', '!=', auth()->id());
        }

        $leaveRequests = $query->latest()->get();

        return view('messages.index', compact('leaveRequests'));
    }

    public function markAsRead($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (is_null($leaveRequest->read_at)) {
            $leaveRequest->read_at = now();
            $leaveRequest->save();
        }

        return response()->json(['success' => true]);
    }

}

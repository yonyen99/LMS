<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     * This method retrieves leave requests based on the user's role and status.
     * It filters out requests that are not relevant to the user and sorts them by the latest.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $query = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'Requested');

        if (auth()->user()->hasRole('Manager')) {
            $query->where('user_id', '!=', auth()->id());
        }

        $leaveRequests = $query->latest()->get();

        return view('messages.index', compact('leaveRequests'));
    }

    /**
     * Mark a leave request as read.
     * This method updates the read_at timestamp for a leave request to indicate it has been read.
     * It returns a JSON response indicating success.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;


class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;

        $query = LeaveRequest::query();


        // Filter by status if provided
        if ($request->filled('status_request')) {
            if (is_array($request->status_request)) {
                $query->whereIn('status', $request->status_request);
            } else {
                $query->where('status', $request->status_request);
            }
        } else {
            // Exclude canceled/cancellation by default
            $query->whereNotIn('status', ['Canceled', 'Cancellation']);
        }

        // Optional: filter by leave_type_id or leave type name if needed
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Order by creation date
        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get leave types for filter dropdown (assuming LeaveType model with id and name)
        $leaveTypes = LeaveType::all();

        $statusRequestOptions = ['Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'];
        $statusColors = [
            'Accepted'     => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested'    => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'     => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Canceled'     => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];

        return view('notifications.index', compact('unreadCount', 'leaveRequests', 'leaveTypes', 'statusRequestOptions', 'statusColors'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Accepted,Rejected,Cancellation,Canceled',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = $request->status;
        $leaveRequest->last_changed_at = now();
        $leaveRequest->save();

        return redirect()->back()->with('success', "Leave request #{$id} status updated to {$request->status}.");
    }

}

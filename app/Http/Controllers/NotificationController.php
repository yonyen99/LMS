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

        // Allowed statuses to show in the notification view
        $statusRequestOptions = ['Accepted', 'Requested', 'Rejected'];
        $statusColors = [
            'Accepted'     => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested'    => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'     => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];

        // Base query: only show allowed statuses
        $query = LeaveRequest::with(['user', 'leaveType'])
            ->whereIn('status', $statusRequestOptions);

        // Filter by status_request (from dropdown)
        if ($request->filled('status_request')) {
            $query->where('status', $request->status_request);
        }

        // Optional: filter by leave_type_id if used
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Sort newest first
        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // All leave types for filter dropdown
        $leaveTypes = LeaveType::all();

        return view('notifications.index', compact(
            'unreadCount',
            'leaveRequests',
            'leaveTypes',
            'statusRequestOptions',
            'statusColors'
        ));
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

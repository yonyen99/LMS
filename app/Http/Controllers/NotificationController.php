<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveSummary;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;

        $statusRequestOptions = ['Accepted', 'Requested', 'Rejected'];
        $statusColors = [
            'Accepted'  => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'  => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];

        $query = LeaveRequest::with(['user', 'leaveType'])
            ->whereIn('status', $statusRequestOptions);

        // Limit to Manager's department if applicable
        if (auth()->user()->hasRole('Manager')) {
            $departmentId = auth()->user()->department_id;

            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        // Filters
        if ($request->filled('status_request')) {
            $query->where('status', $request->status_request);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10);
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
            'status' => 'required|in:Accepted,Rejected,Canceled,Cancellation',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);
        $oldStatus = $leaveRequest->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($leaveRequest, $newStatus, $oldStatus) {
            $summary = LeaveSummary::where('department_id', $leaveRequest->user->department_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->lockForUpdate()
                ->first();

            if ($summary && in_array($newStatus, ['Rejected', 'Canceled'])) {
                if ($oldStatus === 'Requested') {
                    $summary->requested -= $leaveRequest->duration; 
                    $summary->entitled += $leaveRequest->duration;
                } elseif ($oldStatus === 'Accepted') {
                    $summary->taken -= $leaveRequest->duration;
                    $summary->entitled += $leaveRequest->duration;
                }

                $summary->available_actual = $summary->entitled - $summary->taken;
                $summary->save();
            }


            $leaveRequest->update([
                'status' => $newStatus,
                'last_changed_at' => now(),
            ]);
        });

        return redirect()->route('notifications.index')->with('success', "Leave request updated to {$newStatus}.");
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        DB::transaction(function () use ($leaveRequest) {
            $summary = LeaveSummary::where('department_id', $leaveRequest->user->department_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->lockForUpdate()
                ->first();

            if ($summary) {
                $summary->taken += $leaveRequest->duration;
                $summary->requested -= $leaveRequest->duration;
                $summary->save();
            }

            $leaveRequest->update([
                'status' => 'Accepted',
                'last_changed_at' => now(),
            ]);
        });

        return redirect()->route('notifications.index')->with('success', 'Leave approved.');
    }
}

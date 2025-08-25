<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveSummary;
use App\Models\LeaveStatusChange;
use Illuminate\Support\Facades\DB;
use App\Mail\LeaveRequestAcceptedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class NotificationController extends Controller
{

    /**
     * Display a listing of the notifications.
     * This method retrieves leave requests and displays them in a paginated view.
     * It applies various filters based on user input and permissions.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */


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

        // Role-based filtering
        if (auth()->user()->hasRole('Manager')) {
            // Manager can only see requests from their department employees (not other managers)
            $departmentId = auth()->user()->department_id;

            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                    ->whereHas('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'Employee'); // Only show employees, not managers
                    });
            });
        } elseif (auth()->user()->hasRole('Admin')) {
            // Admin sees both managers and employees
            $query->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($roleQuery) {
                    $roleQuery->whereIn('name', ['Employee', 'Manager']);
                });
            });

            // Add role filter for admin if requested
            if ($request->filled('role')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->whereHas('roles', function ($roleQuery) use ($request) {
                        $roleQuery->where('name', $request->role);
                    });
                });
            }
        } else {
            // For other roles (like Employee), only show their own requests
            $query->where('user_id', auth()->id());
        }

        // === Filters ===
        if ($request->filled('status_request') && in_array($request->status_request, $statusRequestOptions)) {
            $query->where('status', $request->status_request);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('statuses')) {
            $statuses = array_map('strtolower', $request->statuses);
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('show_request') && $request->show_request === 'mine') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('type')) {
            $query->whereHas('leaveType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%")
                    ->orWhere('start_time', 'like', "%{$search}%")
                    ->orWhere('end_time', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");

                $q->orWhereHas('leaveType', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Sorting
        $sortOrder = $request->input('sort_order', 'new');
        if ($sortOrder === 'new') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'asc');
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

    /**
     * Update the status of a leave request.
     * This method allows a user to update the status of a leave request.
     * It validates the request and updates the leave request status accordingly.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Accepted,Rejected,Canceled,Cancellation',
        ]);

        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);
        $actingUser = auth()->user();
        $requestUser = $leaveRequest->user;

        // ❌ Managers cannot modify their own requests
        if ($actingUser->id === $requestUser->id && $actingUser->hasRole('Manager')) {
            return redirect()->route('notifications.index')->with('error', 'Managers cannot update their own leave requests.');
        }

        // ❌ Only Admin, HR, or Super Admin can modify Manager requests
        if ($requestUser->hasRole('Manager') && !$actingUser->hasAnyRole(['Admin', 'HR'])) {
            return redirect()->route('notifications.index')->with('error', 'You are not authorized to update a Manager’s leave request.');
        }

        $oldStatus = $leaveRequest->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($leaveRequest, $newStatus, $oldStatus, $actingUser) {
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
                } elseif ($oldStatus === 'Planned') {
                    $summary->requested -= $leaveRequest->duration;
                    $summary->entitled += $leaveRequest->duration;
                }

                $summary->available_actual = $summary->entitled - $summary->taken;
                $summary->save();
            }

            $leaveRequest->update([
                'status' => $newStatus,
                'last_changed_at' => now(),
            ]);

            LeaveStatusChange::create([
                'leave_request_id' => $leaveRequest->id,
                'user_id' => $actingUser->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
            ]);
        });

        // ✅ Send email if Accepted → send to ALL users in ALL departments
        if ($newStatus === 'Accepted') {
            $allUsers = \App\Models\User::pluck('email')->toArray();

            if (!empty($allUsers)) {
                Mail::to($allUsers)->send(new LeaveRequestAcceptedMail($leaveRequest));
            }
        }

        return redirect()->route('notifications.index')->with('success', "Leave request updated to {$newStatus}.");
    }


    /**
     * Approve function
     * 
    
     */

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

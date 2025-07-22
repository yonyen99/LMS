<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = LeaveRequest::with('leaveType')
        ->where('user_id', auth()->id());

        if ($request->filled('statuses')) {
            // Normalize to lowercase if needed
            $statuses = array_map('strtolower', $request->statuses);
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('show_request') && $request->show_request == 'mine') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('type')) {
            // Assuming leaveType->name matches type values
            $query->whereHas('leaveType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        $statusRequestOptions = [
            'Planned',
            'Accepted',
            'Requested',
            'Rejected',
            'Cancellation',
            'Canceled',
        ];
        if ($request->filled('status_request') && in_array($request->status_request, $statusRequestOptions)) {
            $query->where('status', $request->status_request);
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

                // Optional: Join with leave_types and users
                $q->orWhereHas('leaveType', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }
        $sortOrder = $request->input('sort_order', 'new');

       if ($sortOrder === 'new') {
            $query->orderBy('id', 'desc');  // newest = highest ID first
        } else {
            $query->orderBy('id', 'asc');   // oldest = lowest ID first
        }

        $statusColors = [
            'Planned'      => ['text' => '#ffffff', 'bg' => '#A59F9F'],
            'Accepted'     => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested'    => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'     => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Canceled'     => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];
        
        $leaveTypes = LeaveType::orderBy('name')->pluck('name'); 
        $leaveRequests = $query->paginate(10);

        return view('leaveRequest.index', compact('leaveRequests', 'statusColors', 'leaveTypes', 'statusRequestOptions'));

                
    }


    public function create()
    {
        $leaveTypes = LeaveType::all();
        return view('leaveRequest.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'start_time' => 'required|in:morning,afternoon,full',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|in:morning,afternoon,full',
            'duration' => 'required|numeric|min:0.5',
            'reason' => 'nullable|string',
            'status' => 'required|in:planned,requested',
        ]);

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'duration' => $request->duration,
            'reason' => $request->reason,
            'status' => $request->status,
            'requested_at' => now(),
            'last_changed_at' => now(),
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        return view('leaveRequest.show', compact('leaveRequest'));
    }

    // public function edit(LeaveRequest $leaveRequest)
    // {
    //     $this->authorize('update', $leaveRequest); // Optional policy check
    //     $leaveTypes = LeaveType::all();
    //     return view('leaveRequest.edit', compact('leaveRequest', 'leaveTypes'));
    // }

    // public function update(Request $request, LeaveRequest $leaveRequest)
    // {
    //     $this->authorize('update', $leaveRequest);

    //     $request->validate([
    //         'leave_type_id' => 'required|exists:leave_types,id',
    //         'start_date' => 'required|date',
    //         'start_time' => 'required|in:morning,afternoon,full',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //         'end_time' => 'required|in:morning,afternoon,full',
    //         'duration' => 'required|numeric|min:0.5',
    //         'reason' => 'nullable|string',
    //         'status' => 'required|in:requested,accepted,rejected,canceled',
    //     ]);

    //     $leaveRequest->update([
    //         'leave_type_id' => $request->leave_type_id,
    //         'start_date' => $request->start_date,
    //         'start_time' => $request->start_time,
    //         'end_date' => $request->end_date,
    //         'end_time' => $request->end_time,
    //         'duration' => $request->duration,
    //         'reason' => $request->reason,
    //         'status' => $request->status,
    //         'last_changed_at' => now(),
    //     ]);

    //     return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully.');
    // }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->authorize('delete', $leaveRequest);
        $leaveRequest->delete();
        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted.');
    }


   /**
     * Cancel the specified leave request.
     */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('cancel-request', $leaveRequest);
        $leaveRequest->update([
            'status' => 'Canceled',
            'last_changed_at' => now(),
        ]);
        return redirect()->route('leave-requests.index')->with('success', 'Leave request canceled successfully.');
    }

    /**
     * Display the calendar view of leave requests.
     */
    public function calendar()
    {
        $leaveRequests = LeaveRequest::with('leaveType')->where('user_id', Auth::id())->get();
        $leaveTypes = LeaveType::all();
        return view('leaveRequest.calendar', compact('leaveRequests', 'leaveTypes'));
    }
}
        
<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = LeaveRequest::with(['leaveType', 'user'])->latest();

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

        if ($request->filled('status_request')) {
            // Map 'Pending' => 'requested', 'Approved' => 'accepted', etc
            $statusMap = ['Pending' => 'requested', 'Approved' => 'accepted', 'Rejected' => 'rejected'];
            if (isset($statusMap[$request->status_request])) {
                $query->where('status', $statusMap[$request->status_request]);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('reason', 'like', "%{$search}%");
            // Add more searchable fields if needed
        }

        $statusColors = [
            'Planned'      => ['text' => '#ffffff', 'bg' => '#A59F9F'],
            'Accepted'     => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested'    => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'     => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Canceled'     => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];
        $leaveRequests = $query->paginate(10);

        return view('home', compact('leaveRequests', 'statusColors'));
    }

}

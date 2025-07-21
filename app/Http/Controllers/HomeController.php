<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;

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
        $query = LeaveRequest::with(['leaveType', 'user']);

        // Filters...
        if ($request->filled('statuses')) {
            $query->whereIn('status', $request->statuses);
        }

        if ($request->filled('show_request') && $request->show_request === 'mine') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('type')) {
            $query->whereHas('leaveType', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        $statusRequestOptions = [
            'Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled',
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
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('leaveType', fn($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn($sub) => $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        // Sorting
        $sortOrder = $request->input('sort_order', 'new');
        if ($sortOrder === 'new') {
            $query->orderBy('id', 'desc');  // newest = highest ID first
        } else {
            $query->orderBy('id', 'asc');   // oldest = lowest ID first
        }

        // Badge colors
        $statusColors = [
            'Planned'      => ['text' => '#ffffff', 'bg' => '#A59F9F'],
            'Accepted'     => ['text' => '#ffffff', 'bg' => '#447F44'],
            'Requested'    => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
            'Rejected'     => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
            'Canceled'     => ['text' => '#ffffff', 'bg' => '#F80300'],
        ];

        // Leave types for dropdown
        $leaveTypes = LeaveType::orderBy('name')->pluck('name');

        // Pagination size control
        $perPage = $request->input('per_page', 10);
        $leaveRequests = $query->paginate($perPage);

        return view('home', compact(
            'leaveRequests',
            'statusColors',
            'leaveTypes',
            'statusRequestOptions'
        ));
    }



}

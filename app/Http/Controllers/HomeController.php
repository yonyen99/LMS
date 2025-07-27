<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

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
        // Fetch leave requests for the authenticated user
        $query = LeaveRequest::with('leaveType')
            ->where('user_id', auth()->id());

        // Filters
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

        $requests = 0;

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole(['Super Admin', 'Admin', 'HR'])) {
                // Admins see all requested leave requests
                $requests = LeaveRequest::where('status', 'Requested')->count();

            } elseif ($user->hasRole('Manager')) {
                // Manager sees requests from others in the same department (not their own)
                $requests = LeaveRequest::where('status', 'Requested')
                    ->whereHas('user', function ($q) use ($user) {
                        $q->where('department_id', $user->department_id)
                        ->where('id', '!=', $user->id); // exclude own requests
                    })
                    ->count();
            }
        }


        // Sorting
        $sortOrder = $request->input('sort_order', 'new');
        if ($sortOrder === 'new') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'asc');
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

        // Fetch counts for dashboard statistics
        $totalManagers = User::role('Manager')->count();
        $totalEmployees = User::role('Employee')->count();
        $totalDepartments = Department::count();
        $totalLeaves = LeaveRequest::count();
        $totalRequests = LeaveRequest::where('status', 'Requested')->count();
        $totalApproved = LeaveRequest::where('status', 'Accepted')->count();

        // Fetch department user counts with manager and employee names
        $departmentData = Department::with([
            'users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.department_id')
                    ->with('roles:name');
            }
        ])
            ->withCount([
                'users',
                'users as manager_count' => function ($query) {
                    $query->role('Manager');
                },
                'users as employee_count' => function ($query) {
                    $query->role('Employee');
                }
            ])
            ->get()
            ->map(function ($department) {
                $managers = $department->users->filter(function ($user) {
                    return $user->hasRole('Manager');
                })->pluck('name')->toArray();

                $employees = $department->users->filter(function ($user) {
                    return $user->hasRole('Employee');
                })->pluck('name')->toArray();

                return [
                    'name' => $department->name,
                    'user_count' => $department->users_count,
                    'manager_count' => $department->manager_count,
                    'employee_count' => $department->employee_count,
                    'manager_names' => $managers,
                    'employee_names' => $employees,
                ];
            })
            ->filter(function ($department) {
                return $department['user_count'] > 0; // Only include departments with users
            })
            ->values()
            ->toArray();

        // Pagination size control
        $perPage = $request->input('per_page', 10);
        $leaveRequests = $query->paginate($perPage);

        return view('home', compact(
            'leaveRequests',
            'statusColors',
            'leaveTypes',
            'statusRequestOptions',
            'totalRequests',
            'totalManagers',
            'totalEmployees',
            'totalDepartments',
            'totalLeaves',
            'requests',
            'totalApproved',
            'departmentData'
        ));
    }
}
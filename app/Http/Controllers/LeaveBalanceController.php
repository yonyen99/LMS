<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveSummary;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $userId = $user->id;

        // Get entitled days per leave type
        $deptEntitlements = collect();
        if ($user->hasRole('Admin')) {
            // Admins see leave summaries for all departments or a specific department
            $deptEntitlements = LeaveSummary::with('leaveType')
                ->when($request->department_id, function ($query) use ($request) {
                    $query->where('department_id', $request->department_id);
                })
                ->get()
                ->keyBy('leave_type_id');
        } else {
            // Non-Admins (Managers, Employees) see their department's summaries
            if ($departmentId) {
                $deptEntitlements = LeaveSummary::with('leaveType')
                    ->where('department_id', $departmentId)
                    ->get()
                    ->keyBy('leave_type_id');
            }
        }

        // Fallback if no entitlements (e.g., no department or no records)
        if ($deptEntitlements->isEmpty() && $user->hasRole('Admin')) {
            // Fetch all leave types as a fallback for Admins
            $deptEntitlements = LeaveType::all()->mapWithKeys(function ($leaveType) {
                return [$leaveType->id => (object)[
                    'leaveType' => $leaveType,
                    'department_id' => null,
                ]];
            });
        }

        // Get total taken (Accepted) for this user
        $taken = LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_taken'))
            ->where('user_id', $userId)
            ->where('status', 'Accepted')
            ->groupBy('leave_type_id')
            ->pluck('total_taken', 'leave_type_id');

        // Get total requested (Requested) for this user
        $requested = LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_requested'))
            ->where('user_id', $userId)
            ->where('status', 'Requested')
            ->groupBy('leave_type_id')
            ->pluck('total_requested', 'leave_type_id');

        // Get total planned (Planned) for this user
        $planned = LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_planned'))
            ->where('user_id', $userId)
            ->where('status', 'Planned')
            ->groupBy('leave_type_id')
            ->pluck('total_planned', 'leave_type_id');

        // Build summaries for the authenticated user
        $summaries = $deptEntitlements->map(function ($entitlement, $leaveTypeId) use ($taken, $requested, $planned, $user) {
            $baseEntitled = $entitlement->leaveType->typical_annual_requests ?? 0;

            // If user is a Manager, add 2 extra entitled days
            if ($user->hasRole('Manager')) {
                $baseEntitled += 2;
            }

            $takenDays = $taken[$leaveTypeId] ?? 0;
            $requestedDays = $requested[$leaveTypeId] ?? 0;
            $plannedDays = $planned[$leaveTypeId] ?? 0;
            $availableActual = max($baseEntitled - $takenDays, 0);
            $availableSimulated = max($baseEntitled - ($takenDays + $requestedDays), 0);

            return (object)[
                'leaveType' => $entitlement->leaveType,
                'entitled' => $baseEntitled,
                'taken' => $takenDays,
                'requested' => $requestedDays,
                'available_actual' => $availableActual,
                'available_simulated' => $availableSimulated,
                'planned' => $plannedDays,
            ];
        });

        // Fetch employee request data based on role
        $employeeRequests = collect();
        $departments = collect();
        $selectedDepartment = $request->department_id;

        if ($user->hasRole('Admin')) {
            // Admins can see all employees and filter by department
            $query = User::query()->with('department')
                ->whereHas('leaveRequests')
                ->when($selectedDepartment, function ($q) use ($selectedDepartment) {
                    $q->where('department_id', $selectedDepartment);
                });

            $employeeRequests = $query->get()->map(function ($employee) {
                $requestedCount = LeaveRequest::where('user_id', $employee->id)->count();
                $leaveTypeCount = LeaveType::count();
                $nonRequestedCount = $leaveTypeCount - LeaveRequest::where('user_id', $employee->id)
                    ->distinct('leave_type_id')->count();

                return (object)[
                    'name' => $employee->name,
                    'department' => $employee->department->name ?? 'N/A',
                    'requested_count' => $requestedCount,
                    'non_requested_count' => $nonRequestedCount,
                ];
            });

            // Fetch all departments for the filter dropdown
            $departments = Department::all();
        } elseif ($user->hasRole('Manager')) {
            // Managers see only employees in their department
            $employeeRequests = User::where('department_id', $departmentId)
                ->whereHas('leaveRequests')
                ->with('department')
                ->get()
                ->map(function ($employee) {
                    $requestedCount = LeaveRequest::where('user_id', $employee->id)->count();
                    $leaveTypeCount = LeaveType::count();
                    $nonRequestedCount = $leaveTypeCount - LeaveRequest::where('user_id', $employee->id)
                        ->distinct('leave_type_id')->count();

                    return (object)[
                        'name' => $employee->name,
                        'department' => $employee->department->name ?? 'N/A',
                        'requested_count' => $requestedCount,
                        'non_requested_count' => $nonRequestedCount,
                    ];
                });
        }

        return view('leave_types.leave_balance', compact('summaries', 'employeeRequests', 'departments', 'selectedDepartment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

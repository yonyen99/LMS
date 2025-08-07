<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveSummary;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveBalanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $userId = $user->id;

        Log::info('LeaveBalanceController: Request Data', ['department_id' => $request->department_id, 'user_id' => $userId]);

        // Get entitled days per leave type
        $deptEntitlements = collect();
        if ($user->hasRole('Admin')) {
            $deptEntitlements = LeaveSummary::with('leaveType')
                ->when($request->department_id, function ($query) use ($request) {
                    $query->where('department_id', $request->department_id);
                })
                ->get()
                ->keyBy('leave_type_id');
        } else {
            if ($departmentId) {
                $deptEntitlements = LeaveSummary::with('leaveType')
                    ->where('department_id', $departmentId)
                    ->get()
                    ->keyBy('leave_type_id');
            }
        }

        // Fallback for Admins if no entitlements
        if ($deptEntitlements->isEmpty() && $user->hasRole('Admin')) {
            $deptEntitlements = LeaveType::all()->mapWithKeys(function ($leaveType) {
                return [$leaveType->id => (object)[
                    'leaveType' => $leaveType,
                    'department_id' => null,
                    'entitled' => $leaveType->typical_annual_requests ?? 0,
                ]];
            });
        }

        Log::info('LeaveBalanceController: Entitlements', $deptEntitlements->toArray());

        // Get total taken, requested, and planned for this user
        $taken = LeaveRequest::select('leave_type_id', DB::raw('COALESCE(SUM(duration), 0) as total_taken'))
            ->where('user_id', $userId)
            ->where('status', 'Accepted')
            ->groupBy('leave_type_id')
            ->pluck('total_taken', 'leave_type_id');

        $requested = LeaveRequest::select('leave_type_id', DB::raw('COALESCE(SUM(duration), 0) as total_requested'))
            ->where('user_id', $userId)
            ->whereIn('status', ['Requested', 'Planned', 'Accepted'])
            ->groupBy('leave_type_id')
            ->pluck('total_requested', 'leave_type_id');

        $planned = LeaveRequest::select('leave_type_id', DB::raw('COALESCE(SUM(duration), 0) as total_planned'))
            ->where('user_id', $userId)
            ->where('status', 'Planned')
            ->groupBy('leave_type_id')
            ->pluck('total_planned', 'leave_type_id');

        Log::info('LeaveBalanceController: Taken', $taken->toArray());
        Log::info('LeaveBalanceController: Requested', $requested->toArray());
        Log::info('LeaveBalanceController: Planned', $planned->toArray());

        // Build summaries for the authenticated user
        $summaries = $deptEntitlements->map(function ($entitlement, $leaveTypeId) use ($taken, $requested, $planned, $user) {
            $baseEntitled = $entitlement->entitled ?? $entitlement->leaveType->typical_annual_requests ?? 0;

            if ($user->hasRole('Manager')) {
                $baseEntitled += 2;
            }
            $baseEntitled = min($baseEntitled, 40); // Ensure max 40 days

            $takenDays = (float)($taken[$leaveTypeId] ?? 0);
            $requestedDays = (float)($requested[$leaveTypeId] ?? 0);
            $plannedDays = (float)($planned[$leaveTypeId] ?? 0);

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

        Log::info('LeaveBalanceController: Summaries', $summaries->toArray());

        // Fetch employee request data based on role
        $employeeRequests = collect();
        $departments = collect();
        $selectedDepartment = $request->department_id;

        if ($user->hasRole('Admin') || $user->hasRole('Manager')) {
            $query = User::query()->with('department')
                ->whereHas('leaveRequests')
                ->when($user->hasRole('Admin') && $selectedDepartment, function ($q) use ($selectedDepartment) {
                    $q->where('department_id', $selectedDepartment);
                })
                ->when($user->hasRole('Manager'), function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });

            $employeeRequests = $query->get()->map(function ($employee) {
                $leaveSummaries = LeaveSummary::where('department_id', $employee->department_id)
                    ->with('leaveType')
                    ->get();

                $totalEntitled = 0;
                $totalRequested = 0;
                $leaveTypes = $leaveSummaries->isEmpty() ? LeaveType::all() : $leaveSummaries;

                foreach ($leaveTypes as $leaveType) {
                    $baseEntitled = $leaveSummaries->isEmpty()
                        ? ($leaveType->typical_annual_requests ?? 0)
                        : ($leaveType->entitled ?? $leaveType->leaveType->typical_annual_requests ?? 0);

                    if ($employee->hasRole('Manager')) {
                        $baseEntitled += 2;
                    }

                    $totalEntitled += $baseEntitled;
                    $totalRequested += (float)LeaveRequest::where('user_id', $employee->id)
                        ->where('leave_type_id', $leaveType->leave_type_id ?? $leaveType->id)
                        ->whereIn('status', ['Requested', 'Planned', 'Accepted'])
                        ->sum('duration');
                }

                // Cap total entitled at 40 days per user
                $totalEntitled = min($totalEntitled, 40);
                $requestedCount = LeaveRequest::where('user_id', $employee->id)->count();

                // Debug log for this user
                Log::info('Employee Calculation', [
                    'user_id' => $employee->id,
                    'total_entitled' => $totalEntitled,
                    'total_requested' => $totalRequested,
                    'days_can_request' => max($totalEntitled - $totalRequested, 0),
                ]);

                return (object)[
                    'name' => $employee->name,
                    'department' => $employee->department->name ?? 'N/A',
                    'requested_count' => $requestedCount,
                    'total_entitled' => $totalEntitled,
                    'total_requested' => $totalRequested,
                ];
            });

            if ($user->hasRole('Admin')) {
                $departments = Department::all();
            }
        }

        Log::info('LeaveBalanceController: Employee Requests', $employeeRequests->toArray());

        return view('leave_types.leave_balance', compact('summaries', 'employeeRequests', 'departments', 'selectedDepartment'));
    }
}
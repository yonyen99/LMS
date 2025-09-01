<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\LeaveSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveBalanceExport;
use Carbon\Carbon;

class LeaveBalanceController extends Controller
{
    /**
     * Display the leave balance for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $userId = $user->id;

        // Get all leave types with their default entitlements
        $leaveTypes = LeaveType::all()->keyBy('id');

        // Get leave usage for current user grouped by leave type
        $usage = LeaveRequest::where('user_id', $userId)
            ->select('leave_type_id')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Accepted" THEN duration ELSE 0 END), 0) as taken')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Requested" THEN duration ELSE 0 END), 0) as requested')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Planned" THEN duration ELSE 0 END), 0) as planned')
            ->groupBy('leave_type_id')
            ->get()
            ->keyBy('leave_type_id');

        // Build leave summaries for ALL leave types
        $summaries = $leaveTypes->map(function ($leaveType) use ($usage, $user) {
            // Try to get department-specific entitlement first
            $deptEntitlement = LeaveSummary::where('department_id', $user->department_id)
                ->where('leave_type_id', $leaveType->id)
                ->orderBy('report_date', 'desc')
                ->first();
                
            $entitled = $deptEntitlement->entitled ?? $leaveType->typical_annual_requests;
            $taken = (float)($usage[$leaveType->id]->taken ?? 0);
            $requested = (float)($usage[$leaveType->id]->requested ?? 0);
            $planned = (float)($usage[$leaveType->id]->planned ?? 0);

            return (object)[
                'leaveType' => $leaveType,
                'entitled' => $entitled,
                'taken' => $taken,
                'requested' => $requested,
                'planned' => $planned,
                'available_actual' => max($entitled - $taken, 0),
                'available_simulated' => max($entitled - ($taken + $requested), 0),
            ];
        });

        // Calculate totals
        $totals = [
            'entitled' => $summaries->sum('entitled'),
            'taken' => $summaries->sum('taken'),
            'available' => $summaries->sum('available_actual'),
            'requested' => $summaries->sum('requested')
        ];

        // Calculate monthly leave usage for current month
        $currentMonthUsage = LeaveRequest::where('user_id', $userId)
            ->whereIn('status', ['Accepted', 'Requested', 'Planned'])
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->sum('duration');
        
        $monthlyLimit = 1.5;
        $monthlyRemaining = max($monthlyLimit - $currentMonthUsage, 0);
        $monthlyUsagePercentage = $monthlyLimit > 0 ? min(($currentMonthUsage / $monthlyLimit) * 100, 100) : 0;

        // Department overview for managers/admins
        $departmentOverview = collect();
        $departments = collect();

        if ($user->hasRole('Admin') || $user->hasRole('Manager')) {
            $query = User::with(['department', 'leaveRequests', 'roles']);

            // If Manager → filter only employees in their department
            if ($user->hasRole('Manager')) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'Employee');
                })->where('department_id', $departmentId);
            }

            $departmentOverview = $query->get()->map(function ($employee) use ($leaveTypes) {
                $used = $employee->leaveRequests()
                    ->where('status', 'Accepted')
                    ->sum('duration');

                // Calculate monthly usage for each employee
                $monthlyUsed = $employee->leaveRequests()
                    ->whereIn('status', ['Accepted', 'Requested', 'Planned'])
                    ->whereMonth('start_date', now()->month)
                    ->whereYear('start_date', now()->year)
                    ->sum('duration');

                // Calculate entitled days for this employee
                $entitled = 0;
                foreach ($leaveTypes as $leaveType) {
                    $deptEntitlement = LeaveSummary::where('department_id', $employee->department_id)
                        ->where('leave_type_id', $leaveType->id)
                        ->orderBy('report_date', 'desc')
                        ->first();
                        
                    $entitled += $deptEntitlement->entitled ?? $leaveType->typical_annual_requests;
                }

                return (object) [
                    'name'         => $employee->name,
                    'department'   => $employee->department->name ?? 'N/A',
                    'entitled'     => $entitled,
                    'used'         => $used,
                    'available'    => max($entitled - $used, 0),
                    'utilization'  => $entitled > 0 ? ($used / $entitled) * 100 : 0,
                    'id'           => $employee->id,
                    'monthly_used' => $monthlyUsed,
                    'monthly_limit' => 1.5
                ];
            });

            // Admin can see all departments
            if ($user->hasRole('Admin')) {
                $departments = Department::all();
            }
        }

        return view('leave_types.leave_balance', compact(
            'summaries',
            'departmentOverview',
            'departments',
            'user',
            'totals',
            'currentMonthUsage',
            'monthlyLimit',
            'monthlyRemaining',
            'monthlyUsagePercentage'
        ));
    }

    /**
     * Show leave balance details for a specific user.
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();

        // Manager restriction: can only view employees in their department
        if ($currentUser->hasRole('Manager')) {
            if ($user->department_id != $currentUser->department_id || !$user->hasRole('Employee')) {
                abort(403, 'Unauthorized action.');
            }
        }

        // Get all leave types with their default entitlements
        $leaveTypes = LeaveType::all()->keyBy('id');

        // Get leave usage for the specified user grouped by leave type
        $usage = LeaveRequest::where('user_id', $user->id)
            ->select('leave_type_id')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Accepted" THEN duration ELSE 0 END), 0) as taken')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Requested" THEN duration ELSE 0 END), 0) as requested')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Planned" THEN duration ELSE 0 END), 0) as planned')
            ->groupBy('leave_type_id')
            ->get()
            ->keyBy('leave_type_id');

        // Calculate monthly leave usage for current month
        $currentMonthUsage = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['Accepted', 'Requested', 'Planned'])
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->sum('duration');
        
        $monthlyLimit = 1.5;
        $monthlyRemaining = max($monthlyLimit - $currentMonthUsage, 0);

        // Build summaries for ALL leave types
        $summaries = $leaveTypes->map(function ($leaveType) use ($usage, $user) {
            // Try to get department-specific entitlement first
            $deptEntitlement = LeaveSummary::where('department_id', $user->department_id)
                ->where('leave_type_id', $leaveType->id)
                ->orderBy('report_date', 'desc')
                ->first();
                
            $entitled = $deptEntitlement->entitled ?? $leaveType->typical_annual_requests;
            $taken = (float)($usage[$leaveType->id]->taken ?? 0);
            $requested = (float)($usage[$leaveType->id]->requested ?? 0);
            $planned = (float)($usage[$leaveType->id]->planned ?? 0);

            return (object)[
                'leaveType' => $leaveType,
                'entitled' => $entitled,
                'taken' => $taken,
                'requested' => $requested,
                'planned' => $planned,
                'available_actual' => max($entitled - $taken, 0),
            ];
        });

        return view('leave_types.leave_balance_detail', [
            'user' => $user,
            'summaries' => $summaries,
            'currentMonthUsage' => $currentMonthUsage,
            'monthlyLimit' => $monthlyLimit,
            'monthlyRemaining' => $monthlyRemaining
        ]);
    }

    // ... rest of your controller methods (exportPDF, exportExcel, getLeaveUsage) ...
}
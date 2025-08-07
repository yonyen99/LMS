<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveBalanceExport;

class LeaveBalanceController extends Controller
{

    /**
     * Display the leave balance for the authenticated user.
     * This method retrieves the leave balance information for the current user,
     * including their entitlements, usage, and available leave.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */


    public function index(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $userId = $user->id;

        // Get all leave types with their default entitlements
        $leaveTypes = LeaveType::all()->keyBy('id');
        // $leaveTypes = LeaveType::all()->keyBy('id');

        // Get leave usage for current user grouped by leave type
        $usage = LeaveRequest::where('user_id', $userId)
            ->select('leave_type_id')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Accepted" THEN duration ELSE 0 END), 0) as taken')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Requested" THEN duration ELSE 0 END), 0) as requested')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Planned" THEN duration ELSE 0 END), 0) as planned')
            ->groupBy('leave_type_id')
            ->get()
            ->keyBy('leave_type_id');

        // Build leave summaries
        $summaries = $leaveTypes->map(function ($leaveType) use ($usage) {
            $entitled = (float)$leaveType->typical_annual_requests;
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

        // Department overview for managers/admins
        $departmentOverview = collect();
        $departments = collect();

        if ($user->hasRole('Admin') || $user->hasRole('Manager')) {
            $query = User::with(['department', 'leaveRequests', 'roles'])
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'Employee'); // Only employees
                })
                ->when($user->hasRole('Manager'), function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });

            $departmentOverview = $query->get()->map(function ($employee) use ($leaveTypes) {
                $used = $employee->leaveRequests()
                    ->where('status', 'Accepted')
                    ->sum('duration');

                $entitled = $leaveTypes->sum('typical_annual_requests');

                return (object)[
                    'name' => $employee->name,
                    'department' => $employee->department->name ?? 'N/A',
                    'entitled' => $entitled,
                    'used' => $used,
                    'available' => max($entitled - $used, 0),
                    'utilization' => $entitled > 0 ? ($used / $entitled) * 100 : 0,
                    'id' => $employee->id // Make sure ID is included
                ];
            });

            if ($user->hasRole('Admin')) {
                $departments = Department::all();
            }
        }

        return view('leave_types.leave_balance', compact(
            'summaries',
            'departmentOverview',
            'departments',
            'user',
            'totals'
        ));
    }

    /**
     * 
     * Show leave balance details for a specific user.
     * This method is used to display the leave balance details for a specific user.
     * It checks if the user is authorized to view the details and retrieves the leave balance information
     * for the specified user.
     * 
     */

    public function show(User $user) // Using route model binding
    {
        $currentUser = Auth::user();

        // Authorization check for managers
        if ($currentUser->hasRole('Manager') && $user->department_id != $currentUser->department_id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the user is an employee
        if (!$user->hasRole('Employee')) {
            abort(403, 'Only employee records can be viewed');
        }

        $leaveTypes = LeaveType::all();
        $summaries = $leaveTypes->map(function ($type) use ($user) {
            $usage = $this->getLeaveUsage($user->id, $type->id);
            return (object)[
                'leaveType' => $type,
                'entitled' => $type->typical_annual_requests,
                'taken' => $usage->taken,
                'requested' => $usage->requested,
                'planned' => $usage->planned,
                'available_actual' => max($type->typical_annual_requests - $usage->taken, 0),
            ];
        });

        return view('leave_types.leave_balance_detail', [
            'user' => $user,
            'summaries' => $summaries,
        ]);
    }

    /**
     * Get leave usage for a specific user and leave type.
     * This method retrieves the leave usage for a specific user and leave type,
     * including the total taken, requested, and planned durations.
     *
     * @param int $userId
     * @param int $leaveTypeId
     * @return \Illuminate\Database\Eloquent\Collection
     */

    protected function getLeaveUsage($userId, $leaveTypeId)
    {
        return LeaveRequest::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Accepted" THEN duration ELSE 0 END), 0) as taken')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Requested" THEN duration ELSE 0 END), 0) as requested')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = "Planned" THEN duration ELSE 0 END), 0) as planned')
            ->first();
    }

    /**
     * 
     * Export leave balance details to PDF.
     * This method exports the leave balance details of a specific user to a PDF file.
     * It checks if the user is authorized to export the details and generates a PDF file
     * containing the leave balance information.
     */


    public function exportPDF(User $user)
    {
        $currentUser = Auth::user();

        // Authorization is handled by middleware, but keeping this as backup
        if (!$currentUser->can('export', $user)) {
            abort(403, 'Unauthorized action.');
        }

        $leaveTypes = LeaveType::all();
        $summaries = $leaveTypes->map(function ($type) use ($user) {
            $usage = $this->getLeaveUsage($user->id, $type->id);
            return [
                'leaveType' => $type,
                'entitled' => $type->typical_annual_requests,
                'taken' => $usage->taken,
                'requested' => $usage->requested,
                'planned' => $usage->planned,
                'available_actual' => max($type->typical_annual_requests - $usage->taken, 0),
            ];
        });

        $pdf = PDF::loadView('leave_types.leave_balance_pdf', [
            'user' => $user,
            'summaries' => $summaries,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'portrait');

        $filename = "leave_balance_{$user->employee_id}_{$user->name}.pdf";

        return $pdf->download($filename);
    }


    /**
     * 
     * Export leave balance details to Excel.
     * This method exports the leave balance details of a specific user to an Excel file.
     * It checks if the user is authorized to export the details and generates an Excel file
     * containing the leave balance information.
     */

    public function exportExcel(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser->can('export', $user)) {
            abort(403, 'Unauthorized action.');
        }

        $leaveTypes = LeaveType::all();
        $summaries = $leaveTypes->map(function ($type) use ($user) {
            $usage = $this->getLeaveUsage($user->id, $type->id);
            return [
                'leaveType' => $type,
                'entitled' => $type->typical_annual_requests,
                'taken' => $usage->taken,
                'requested' => $usage->requested,
                'planned' => $usage->planned,
                'available_actual' => max($type->typical_annual_requests - $usage->taken, 0),
            ];
        });

        $filename = "leave_balance_{$user->employee_id}_{$user->name}.xlsx";

        return Excel::download(new LeaveBalanceExport($user, $summaries), $filename);
    }
}

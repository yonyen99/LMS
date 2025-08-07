<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveBalanceController extends Controller
{
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
            $query = User::with(['department', 'leaveRequests'])
                ->where('id', '!=', $user->id)
                ->when($user->hasRole('Manager'), function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereDoesntHave('roles', function ($q) {
                          $q->whereIn('name', ['Admin', 'Manager']);
                      });
                })
                ->when($user->hasRole('Admin'), function ($q) {
                    $q->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'Admin');
                    });
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
}
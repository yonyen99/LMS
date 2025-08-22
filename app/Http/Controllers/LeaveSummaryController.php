<?php

namespace App\Http\Controllers;

use App\Models\LeaveSummary;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Department;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     * This method retrieves the latest leave summaries grouped by department and report date,
     * aggregating leave types into a single field.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $subquery = DB::table('leave_summaries as ls')
            ->selectRaw('MAX(id) as id')
            ->groupBy('department_id', 'report_date');

        $summaries = DB::table('leave_summaries')
            ->joinSub($subquery, 'latest', 'leave_summaries.id', '=', 'latest.id')
            ->join('departments', 'leave_summaries.department_id', '=', 'departments.id')
            ->join('leave_types', 'leave_summaries.leave_type_id', '=', 'leave_types.id')
            ->select(
                'leave_summaries.id',
                'leave_summaries.department_id',
                'departments.name as department_name',
                DB::raw('GROUP_CONCAT(leave_types.name) as leave_types'),
                DB::raw('GROUP_CONCAT(leave_types.id) as leave_type_ids'),
                'leave_summaries.report_date',
                DB::raw('SUM(leave_summaries.entitled) as entitled')
            )
            ->groupBy('leave_summaries.department_id', 'leave_summaries.report_date', 'leave_summaries.id', 'departments.name')
            ->orderByDesc('leave_summaries.id')
            ->paginate(10);

        $departments = Department::all();
        $leaveTypes = LeaveType::all();

        return view('leave_summaries.index', compact('summaries', 'departments', 'leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     * This method retrieves all departments to populate the form for creating a new leave summary.
     * It checks if the user has permission to create a leave summary before returning the view.
     */
    public function create()
    {
        $this->authorize('create', LeaveSummary::class);

        $departments = Department::all();

        return view('leave_summaries.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     * This method creates leave summaries for all users in the specified department
     * for all leave types based on the report date provided in the request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'report_date'   => 'required|date',
        ]);

        $leaveTypes = LeaveType::all();
        $annualEntitlement = 18; // Total days per year
        $monthlyAccrual = $annualEntitlement / 12; // 1.5 days per month
        $reportDate = \Carbon\Carbon::parse($request->report_date);
        $users = User::where('department_id', $request->department_id)->get();

        foreach ($leaveTypes as $leaveType) {
            foreach ($users as $user) {
                // Get or create leave summary for this month and leave type
                $leaveSummary = LeaveSummary::firstOrNew([
                    'user_id'        => $user->id,
                    'leave_type_id'  => $leaveType->id,
                    'department_id'  => $request->department_id,
                    'report_date'    => $reportDate->format('Y-m-d'),
                ]);

                // Calculate current remaining days (available_actual)
                $currentRemaining = $leaveSummary->available_actual ?? 0;

                // Add monthly accrual
                $newRemaining = $currentRemaining + $monthlyAccrual;

                // Make sure total entitlement does not exceed annual entitlement
                $leaveSummary->entitled = min(($leaveSummary->entitled ?? 0) + $monthlyAccrual, $annualEntitlement);

                $leaveSummary->available_actual = min($newRemaining, $annualEntitlement);

                // Keep other fields
                $leaveSummary->taken = $leaveSummary->taken ?? 0;
                $leaveSummary->planned = $leaveSummary->planned ?? 0;
                $leaveSummary->requested = $leaveSummary->requested ?? 0;
                $leaveSummary->available_simulated = $leaveSummary->available_actual;

                $leaveSummary->save();
            }
        }

        return redirect()->route('leave-summaries.index')
            ->with('success', 'Leave summaries updated for all leave types: monthly accrual added (max 18 days/year).');
    }

    /**
     * Show the form for editing the specified resource.
     * This method retrieves a specific leave summary record for editing.
     * It checks if the user has permission to edit the record and then returns the edit view
     * with the leave summary data.
     */
    public function edit(LeaveSummary $leaveSummary)
    {
        $this->authorize('update', $leaveSummary);

        $leaveTypes = LeaveType::all();
        $departments = Department::all();

        return view('leave_summaries.edit', compact('leaveSummary', 'leaveTypes', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     * This method updates leave summary records for multiple leave types.
     * It checks if the user has permission to update and processes each selected leave type.
     */
    public function update(Request $request, LeaveSummary $leaveSummary)
    {
        $request->validate([
            'leave_type_ids' => 'required|array',
            'leave_type_ids.*' => 'exists:leave_types,id',
            'department_id' => 'required|exists:departments,id',
            'report_date' => 'required|date',
        ]);

        $reportDate = \Carbon\Carbon::parse($request->report_date);
        $departmentId = $request->department_id;
        $leaveTypeIds = $request->leave_type_ids;
        $users = User::where('department_id', $departmentId)->get();

        // Delete existing summaries for this department and report date to avoid duplicates
        LeaveSummary::where('department_id', $departmentId)
            ->where('report_date', $reportDate->format('Y-m-d'))
            ->delete();

        // Create or update summaries for each selected leave type
        foreach ($leaveTypeIds as $leaveTypeId) {
            $leaveType = LeaveType::findOrFail($leaveTypeId);
            $entitled = $leaveType->typical_annual_requests ?? 0;

            foreach ($users as $user) {
                $newSummary = LeaveSummary::firstOrNew([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveTypeId,
                    'department_id' => $departmentId,
                    'report_date' => $reportDate->format('Y-m-d'),
                ]);

                $newSummary->entitled = $entitled;
                $newSummary->available_actual = $newSummary->available_actual ?? $entitled;
                $newSummary->available_simulated = $newSummary->available_simulated ?? $entitled;
                $newSummary->taken = $newSummary->taken ?? 0;
                $newSummary->planned = $newSummary->planned ?? 0;
                $newSummary->requested = $newSummary->requested ?? 0;

                $newSummary->save();
            }
        }

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summaries updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * This method deletes all leave summary records for the given department and report date.
     */
    public function destroy(LeaveSummary $leaveSummary)
    {
        $this->authorize('delete', $leaveSummary);

        LeaveSummary::where('department_id', $leaveSummary->department_id)
            ->where('report_date', $leaveSummary->report_date)
            ->delete();

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summaries deleted successfully.');
    }

    /**
     * Display the leave summary for the authenticated user.
     * This method retrieves the leave summary information for the current user,
     * including their entitlements, usage, and available leave.
     */
    public function userLeave()
    {
        $user = auth()->user();
        $departmentId = $user->department_id;
        $userId = $user->id;

        // Get entitled days per leave type from department
        $deptEntitlements = LeaveSummary::with('leaveType')
            ->where('department_id', $departmentId)
            ->get()
            ->keyBy('leave_type_id');

        // Get total taken (Accepted + Planned) for this user
        $taken = \App\Models\LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_taken'))
            ->where('user_id', $userId)
            ->where('status', 'Accepted')
            ->groupBy('leave_type_id')
            ->pluck('total_taken', 'leave_type_id');

        // Get total requested (Requested) for this user
        $requested = \App\Models\LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_requested'))
            ->where('user_id', $userId)
            ->where('status', 'Requested')
            ->groupBy('leave_type_id')
            ->pluck('total_requested', 'leave_type_id');

        $planned = \App\Models\LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_planned'))
            ->where('user_id', $userId)
            ->where('status', 'Planned')
            ->groupBy('leave_type_id')
            ->pluck('total_planned', 'leave_type_id');

        // Build summaries
        $summaries = $deptEntitlements->map(function ($entitlement, $leaveTypeId) use ($taken, $requested, $planned, $user) {
            $baseEntitled = $entitlement->entitled ?? 0;

            $takenDays = $taken[$leaveTypeId] ?? 0;
            $requestedDays = $requested[$leaveTypeId] ?? 0;
            $plannedDays = $planned[$leaveTypeId] ?? 0;

            // Calculate available days — allow negative if over-requested
            $availableActual = $baseEntitled - $takenDays;
            $availableSimulated = $baseEntitled - ($takenDays + $requestedDays);

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

        return view('user_leaves.index', compact('summaries'));
    }
}
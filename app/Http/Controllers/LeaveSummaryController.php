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
     * This method retrieves the latest leave summaries for each department and leave type,
     * ensuring that only the most recent summary for each combination is shown.
     *
     * @return \Illuminate\View\View
     */
    
    public function index()
    {
        $subquery = DB::table('leave_summaries')
            ->selectRaw('MAX(id) as id')
            ->groupBy('department_id', 'leave_type_id');

        $summaries = LeaveSummary::with(['department', 'leaveType'])
            ->joinSub($subquery, 'latest', 'leave_summaries.id', '=', 'latest.id')
            ->select('leave_summaries.*')
            ->orderByDesc('id')
            ->paginate(10);

        $departments = Department::all();
        $leaveTypes = LeaveType::all();

        return view('leave_summaries.index', compact('summaries', 'departments', 'leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     * This method retrieves all leave types and departments to populate the form for creating a new leave summary.
     * It checks if the user has permission to create a leave summary before returning the view.
     */

    public function create()
    {
        $this->authorize('create', LeaveSummary::class);

        $leaveTypes = LeaveType::all();
        $departments = Department::all();

        return view('leave_summaries.create', compact('leaveTypes', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     * This method creates leave summaries for all users in the specified department
     * based on the leave type and report date provided in the request.
     */

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'department_id' => 'required|exists:departments,id',
            'report_date'   => 'required|date',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        $annualEntitlement = 18;               // Total days per year
        $monthlyAccrual = $annualEntitlement / 12; // 1.5 days per month

        $reportDate = \Carbon\Carbon::parse($request->report_date);
        $users = User::where('department_id', $request->department_id)->get();

        foreach ($users as $user) {
            // Get or create leave summary for this month
            $leaveSummary = LeaveSummary::firstOrNew([
                'user_id'        => $user->id,
                'leave_type_id'  => $request->leave_type_id,
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

        return redirect()->route('leave-summaries.index')
            ->with('success', 'Leave summaries updated: monthly accrual added (max 18 days/year).');
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
     * 
     * Update the specified resource in storage.
     * This method updates an existing leave summary record.
     * It checks if the user has permission to update the record and then updates it with the
     *  validated data from the request.
     * If the update is successful, it redirects back to the index with a success message.
     */

    public function update(Request $request, LeaveSummary $leaveSummary)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'department_id' => 'required|exists:departments,id',
            'report_date' => 'required|date',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $entitled = $leaveType->typical_annual_requests ?? 0;

        $leaveSummary->update([
            'leave_type_id' => $request->leave_type_id,
            'department_id' => $request->department_id,
            'report_date' => $request->report_date,
            'entitled' => $entitled,
        ]);

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summary updated successfully.');
    }

    /**
     * 
     * Remove the specified resource from storage.
     * This method deletes a leave summary record.
     * It checks if the user has permission to delete the record and then deletes it.
     * If the deletion is successful, it redirects back to the index with a success message.
     * 
     */

    public function destroy(LeaveSummary $leaveSummary)
    {
        $this->authorize('delete', $leaveSummary);

        $leaveSummary->delete();

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summary deleted successfully.');
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

            // // If user is a Manager, add 2 extra entitled days
            // if ($user->hasRole('Manager')) {
            //     $baseEntitled += 2;
            // }

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
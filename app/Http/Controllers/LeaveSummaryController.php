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
    public function index()
    {
        $subquery = DB::table('leave_summaries')
            ->selectRaw('MAX(id) as id')
            ->groupBy('department_id', 'leave_type_id');

        $summaries = LeaveSummary::with(['department', 'leaveType'])
            ->whereIn('id', $subquery->pluck('id'))
            ->paginate(10);

        return view('leave_summaries.index', compact('summaries'));
    }
   

    public function create()
    {
        $this->authorize('create', LeaveSummary::class);

        $leaveTypes = LeaveType::all();
        $departments = Department::all();

        return view('leave_summaries.create', compact('leaveTypes', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'department_id' => 'required|exists:departments,id',
            'report_date' => 'required|date',
            'entitled' => 'required|numeric|min:0',
        ]);

        $users = User::where('department_id', $request->department_id)->get();

        foreach ($users as $user) {
            LeaveSummary::updateOrCreate(
                [
                    'user_id'        => $user->id,
                    'leave_type_id'  => $request->leave_type_id,
                    'department_id'  => $request->department_id,
                    'report_date'    => $request->report_date,
                ],
                [
                    'entitled'           => $request->entitled,
                    'taken'              => 0,
                    'planned'            => 0,
                    'requested'          => 0,
                    'available_actual'   => $request->entitled,
                    'available_simulated'=> $request->entitled,
                ]
            );
        }

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summaries created for all users in department.');
    }

    public function edit(LeaveSummary $leaveSummary)
    {
        $this->authorize('update', $leaveSummary);

        $leaveTypes = LeaveType::all();
        $departments = Department::all();

        return view('leave_summaries.edit', compact('leaveSummary', 'leaveTypes', 'departments'));
    }

    public function update(Request $request, LeaveSummary $leaveSummary)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'department_id' => 'required|exists:departments,id',
            'report_date' => 'required|date',
            'entitled' => 'required|numeric',
        ]);

        $leaveSummary->update([
            'leave_type_id' => $request->leave_type_id,
            'department_id' => $request->department_id,
            'report_date' => $request->report_date,
            'entitled' => $request->entitled,
        ]);

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summary updated successfully.');
    }


    public function destroy(LeaveSummary $leaveSummary)
    {
        $this->authorize('delete', $leaveSummary);

        $leaveSummary->delete();

        return redirect()->route('leave-summaries.index')->with('success', 'Leave summary deleted successfully.');
    }

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
            ->whereIn('status', ['Accepted', 'Planned'])
            ->groupBy('leave_type_id')
            ->pluck('total_taken', 'leave_type_id');

        // Get total requested (Requested) for this user
        $requested = \App\Models\LeaveRequest::select('leave_type_id', DB::raw('SUM(duration) as total_requested'))
            ->where('user_id', $userId)
            ->where('status', 'Requested')
            ->groupBy('leave_type_id')
            ->pluck('total_requested', 'leave_type_id');

        // Build summaries
        $summaries = $deptEntitlements->map(function ($entitlement, $leaveTypeId) use ($taken, $requested, $user) {
            $baseEntitled = $entitlement->entitled;

            // If user is a Manager, add 2 extra entitled days
            if ($user->hasRole('Manager')) {
                $baseEntitled += 2;
            }

            $takenDays = $taken[$leaveTypeId] ?? 0;
            $requestedDays = $requested[$leaveTypeId] ?? 0;
            $availableActual = max($baseEntitled - $takenDays, 0);
            $availableSimulated = max($baseEntitled - ($takenDays + $requestedDays), 0);

            return (object)[
                'leaveType' => $entitlement->leaveType,
                'entitled' => $baseEntitled,
                'taken' => $takenDays,
                'requested' => $requestedDays,
                'available_actual' => $availableActual,
                'available_simulated' => $availableSimulated,
                'planned' => 0,
            ];
        });

        return view('user_leaves.index', compact('summaries'));
    }

}

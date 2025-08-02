<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OTController extends Controller
{
    public function index()
    {
        $overtimes = OvertimeRequest::with('user')->latest()->get();
        return view('over_time.list_over_time', compact('overtimes'));
    }

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        return view('over_time.create', compact('departments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'department_id'  => 'required|exists:departments,id',
            'overtime_date'  => 'required|date',
            'time_period'    => 'required|in:before_shift,after_shift,weekend,holiday',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'duration'       => 'required|numeric|min:0.5|max:24',
            'reason'         => 'nullable|string',
        ]);

        OvertimeRequest::create([
            'user_id'         => Auth::id(),
            'department_id'   => $request->department_id,
            'overtime_date'   => $request->overtime_date,
            'time_period'     => $request->time_period,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'duration'        => $request->duration,
            'reason'          => $request->reason,
            'last_changed_at' => now(),
        ]);


        return redirect()->route('over-time.index')->with('success', 'Overtime request submitted.');
    }

    public function edit($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to edit this request.');
        }

        return view('over_time.edit', compact('overtime'));  // <-- fixed typo here
    }

    public function update(Request $request, $id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to edit this request.');
        }

        $request->validate([
            'department'     => 'required|string',
            'overtime_date'  => 'required|date',
            'time_period'    => 'required|in:before_shift,after_shift,weekend,holiday',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'duration'       => 'required|numeric|min:0.5|max:24',
            'reason'         => 'nullable|string',
        ]);

        $overtime->update([
            'department'     => $request->department,
            'overtime_date'  => $request->overtime_date,
            'time_period'    => $request->time_period,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
            'duration'       => $request->duration,
            'reason'         => $request->reason,
            'last_changed_at' => now(),
        ]);

        return redirect()->route('over-time.index')->with('success', 'Overtime request updated successfully.');
    }

    public function show($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to view this request.');
        }

        return view('over_time.show', compact('overtime'));
    }

    public function destroy($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->user_id !== Auth::id()) {
            return redirect()->route('over-time.index')->with('error', 'You are not authorized to delete this request.');
        }

        $overtime->delete();

        return redirect()->route('over-time.index')->with('success', 'Overtime request deleted successfully.');
    }
}

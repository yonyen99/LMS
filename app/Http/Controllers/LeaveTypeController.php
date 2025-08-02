<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::latest()->paginate(10);
        $totalLeaveTypes = LeaveType::count();
        return view('leave_types.index', compact('leaveTypes', 'totalLeaveTypes'));
    }

    public function create()
    {
        return view('leave_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:leave_types,name',
            'description' => 'nullable|string',
            'typical_annual_requests' => 'nullable|string|max:255',
        ]);

        LeaveType::create($request->only('name', 'description', 'typical_annual_requests'));

        return redirect()->route('leave-types.index')->with('success', 'Leave type created successfully.');
    }

    public function show(LeaveType $leaveType)
    {
        return view('leave_types.show', compact('leaveType'));
    }

    public function edit(LeaveType $leaveType)
    {
        return view('leave_types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|unique:leave_types,name,' . $leaveType->id,
            'description' => 'nullable|string',
            'typical_annual_requests' => 'nullable|string|max:255',
        ]);

        $leaveType->update($request->only('name', 'description', 'typical_annual_requests'));

        return redirect()->route('leave-types.index')->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('leave-types.index')->with('success', 'Leave type deleted successfully.');
    }
}
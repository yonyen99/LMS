<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     * This method retrieves all leave types and paginates them.
     * It also counts the total number of leave types for display.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $leaveTypes = LeaveType::latest()->paginate(10);
        $totalLeaveTypes = LeaveType::count();
        return view('leave_types.index', compact('leaveTypes', 'totalLeaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     * This method returns the view for creating a new leave type.
     *
     * @return \Illuminate\View\View
     */


    public function create()
    {
        return view('leave_types.create');
    }

    /**
     * Store a newly created resource in storage.
     * This method validates the request data and creates a new leave type.
     * If successful, it redirects back to the index with a success message.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */


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

    /**
     * Display the specified resource.
     * This method retrieves a specific leave type by its ID and returns the view to show it.
     *
     * @param LeaveType $leaveType
     * @return \Illuminate\View\View
     */


    public function show(LeaveType $leaveType)
    {
        return view('leave_types.show', compact('leaveType'));
    }

    /**
     * Show the form for editing the specified resource.
     * This method retrieves a specific leave type for editing.
     * It returns the view with the leave type data.
     *
     * @param LeaveType $leaveType
     * @return \Illuminate\View\View
     */


    public function edit(LeaveType $leaveType)
    {
        return view('leave_types.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     * This method validates the request data and updates an existing leave type.
     * If successful, it redirects back to the index with a success message.
     *
     * @param Request $request
     * @param LeaveType $leaveType
     * @return \Illuminate\Http\RedirectResponse
     */

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

    /**
     * Remove the specified resource from storage.
     * This method deletes a leave type by its ID and redirects back to the index with a success message.
     *
     * @param LeaveType $leaveType
     * @return \Illuminate\Http\RedirectResponse
     */
    

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('leave-types.index')->with('success', 'Leave type deleted successfully.');
    }
}
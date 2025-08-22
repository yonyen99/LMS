<?php

namespace App\Http\Controllers;

use App\Models\NonWorkingDay;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonWorkingDayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-non-working-day', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-non-working-day', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-non-working-day', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-non-working-day', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the non-working days.
     * This method retrieves all non-working days and their associated departments,
     * ordered by the most recent entry first.
     *
     * @return \Illuminate\View\View
     */


    public function index()
    {
        $nonWorkingDays = NonWorkingDay::with(['department', 'creator'])->get();
        return view('non-working-days.index', compact('nonWorkingDays'));
    }

    /**
     * Show the form for creating a new non-working day.
     * This method retrieves all departments to populate the dropdown for department selection.
     *
     * @return \Illuminate\View\View
     */


    public function create()
    {
        $departments = Department::all();
        return view('non-working-days.create', compact('departments'));
    }

    /**
     * Store a newly created non-working day in storage.
     * This method validates the request data and creates a new non-working day entry.
     * It also ensures that managers can only create entries for their own department.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:holiday,meeting,event,maintenance,training,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string'
        ]);

        // For managers, force their department ID
        if (auth()->user()->hasRole('Manager') && !auth()->user()->hasRole('Admin')) {
            $request->merge(['department_id' => auth()->user()->department_id]);
        }

        NonWorkingDay::create([
            'title' => $request->title,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'department_id' => $request->department_id,
            'description' => $request->description,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('leave-requests.calendar')
            ->with('success', 'Calendar entry added successfully.');
    }

    /**
     * Display the specified non-working day.
     * This method retrieves a specific non-working day entry by its ID and returns the view to show it.
     *
     * @param NonWorkingDay $nonWorkingDay
     * @return \Illuminate\View\View
     */


    public function show(NonWorkingDay $nonWorkingDay)
    {
        return view('non-working-days.show', compact('nonWorkingDay'));
    }

    /**
     * Show the form for editing the specified non-working day.
     * This method retrieves a specific non-working day entry for editing.
     * It also retrieves all departments to populate the dropdown for department selection.
     *
     * @param NonWorkingDay $nonWorkingDay
     * @return \Illuminate\View\View
     */

    public function edit(NonWorkingDay $nonWorkingDay)
    {
        $this->authorize('edit', $nonWorkingDay);
        
        $departments = Department::all();
        return view('non-working-days.edit', compact('nonWorkingDay', 'departments'));
    }

    /**
     * Update the specified non-working day in storage.
     * This method validates the request data and updates an existing non-working day entry.
     * It also ensures that managers can only update entries for their own department.
     *
     * @param Request $request
     * @param NonWorkingDay $nonWorkingDay
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request, NonWorkingDay $nonWorkingDay)
    {
        $this->authorize('update', $nonWorkingDay);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:holiday,meeting,event,maintenance,training,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string'
        ]);

        // For managers, force their department ID
        if (auth()->user()->hasRole('Manager') && !auth()->user()->hasRole('Admin')) {
            $request->merge(['department_id' => auth()->user()->department_id]);
        }

        $nonWorkingDay->update([
            'title' => $request->title,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'department_id' => $request->department_id,
            'description' => $request->description
        ]);

        return redirect()->route('leave-requests.calendar')
            ->with('success', 'Calendar entry updated successfully.');
    }

    /**
     * Remove the specified non-working day from storage.
     * This method deletes a non-working day entry and redirects back to the calendar with a success message.
     *
     * @param NonWorkingDay $nonWorkingDay
     * @return \Illuminate\Http\RedirectResponse
     */
    
    public function destroy(NonWorkingDay $nonWorkingDay)
    {
        $this->authorize('delete', $nonWorkingDay);

        $nonWorkingDay->delete();

        return redirect()->route('leave-requests.calendar')
            ->with('success', 'Calendar entry deleted successfully.');
    }
}
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

    public function index()
    {
        $nonWorkingDays = NonWorkingDay::with(['department', 'creator'])->get();
        return view('non-working-days.index', compact('nonWorkingDays'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('non-working-days.create', compact('departments'));
    }

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
        if (auth()->user()->hasRole('Manager') && !auth()->user()->hasRole('Super Admin')) {
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

    public function show(NonWorkingDay $nonWorkingDay)
    {
        return view('non-working-days.show', compact('nonWorkingDay'));
    }

    public function edit(NonWorkingDay $nonWorkingDay)
    {
        $this->authorize('edit', $nonWorkingDay);
        
        $departments = Department::all();
        return view('non-working-days.edit', compact('nonWorkingDay', 'departments'));
    }

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
        if (auth()->user()->hasRole('Manager') && !auth()->user()->hasRole('Super Admin')) {
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

    public function destroy(NonWorkingDay $nonWorkingDay)
    {
        $this->authorize('delete', $nonWorkingDay);

        $nonWorkingDay->delete();

        return redirect()->route('leave-requests.calendar')
            ->with('success', 'Calendar entry deleted successfully.');
    }
}
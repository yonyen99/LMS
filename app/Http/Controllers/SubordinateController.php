<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubordinateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Initialize query
        $query = User::query();

        // For Admin and Super Admin, fetch all users with Manager or Employee roles across all departments
        if ($user->hasRole(['Admin', 'HR'])) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Manager', 'Employee']);
            });
        } else {
            // For Managers, fetch only Employees in their department, excluding themselves
            $query->where('department_id', $user->department_id)
                  ->whereHas('roles', function ($q) {
                      $q->where('name', 'Employee');
                  })
                  ->where('id', '!=', $user->id);
        }

        // Apply department filter for Admins/Super Admins if selected
        if ($user->hasRole(['Admin', 'HR']) && $request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Fetch departments for the filter dropdown (Admins/Super Admins only)
        $departments = Department::orderBy('name')->pluck('name', 'id')->toArray();

        // Pagination size control
        $perPage = $request->input('per_page', 10);
        $subordinates = $query->with('roles')->paginate($perPage);

        return view('subordinate.index', compact('subordinates', 'departments'));
    }
}
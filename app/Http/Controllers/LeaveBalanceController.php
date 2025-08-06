<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Initialize the query for leave summaries
        $query = LeaveSummary::query()
            ->with(['user', 'leaveType']) // Eager load relationships
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('leaveType', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });

        // Filter based on user role
        if ($user->hasRole('Admin')) {
            // Admin sees all leave summaries
            $leaveBalances = $query->paginate(10);
        } elseif ($user->hasRole('Manager')) {
            // Manager sees only summaries from their department
            $leaveBalances = $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })->paginate(10);
        } else {
            // Employee sees only their own summaries
            $leaveBalances = $query->where('user_id', $user->id)->paginate(10);
        }

        return view('leave_types.leave_balance', compact('leaveBalances'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

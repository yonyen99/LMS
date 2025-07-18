<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        // $leaveRequests = LeaveRequest::latest()->get();
        return view('/leaveRequest/leave_requests', compact('leaveRequests'));
    }
}

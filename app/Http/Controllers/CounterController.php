<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index()
    {
        // Sample data for leave summary (replace with database query if available)
        $counters = [
            [
                'type' => 'Compensate/Offset',
                'available_actual' => 0,
                'available_simulated' => 0,
                'entitled' => 1,
                'taken' => 1,
                'planned' => 0,
                'requested' => 0,
            ],
            [
                'type' => 'Paid Leave',
                'available_actual' => 5.5,
                'available_simulated' => 5.5,
                'entitled' => 14,
                'taken' => 8.5,
                'planned' => 0,
                'requested' => 0,
            ],
            [
                'type' => 'Sick Leave (No Certificate)',
                'available_actual' => 3,
                'available_simulated' => 3,
                'entitled' => 4,
                'taken' => 1,
                'planned' => 0,
                'requested' => 0,
            ],
        ];

        return view('employee_counter.index', compact('counters'));
        
    
    }
}
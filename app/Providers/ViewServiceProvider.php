<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\LeaveRequest;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share leave requests with layout
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $query = LeaveRequest::with(['user', 'leaveType']);

                // Managers only see their own department requests
                if (auth()->user()->hasRole('Manager')) {
                    $departmentId = auth()->user()->department_id;
                    $query->whereHas('user', function ($q) use ($departmentId) {
                        $q->where('department_id', $departmentId);
                    });
                }

                // Get latest 10 requests
                $leaveRequests = $query->latest()->take(10)->get();

                $view->with('leaveRequests', $leaveRequests);
            } else {
                // If user not logged in, avoid error
                $view->with('leaveRequests', collect());
            }
        });
    }
}

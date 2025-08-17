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
        // Share leave requests with ALL views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $query = LeaveRequest::with(['user', 'leaveType']);

                // Managers only see their own department requests
                if (auth()->user()->hasRole('Manager')) {
                    $departmentId = auth()->user()->department_id;
                    $query->whereHas('user', function ($q) use ($departmentId) {
                        $q->where('department_id', $departmentId);
                    });
                }

                // Show latest 10 requests
                // $leaveRequests = $query->latest()->take(10)->get();
                $leaveRequests = $query->latest()->paginate(10);


                // Pass variable to all views
                $view->with('leaveRequests', $leaveRequests);
            } else {
                // If user not logged in, avoid error
                $view->with('leaveRequests', collect());
            }
        });
    }
}

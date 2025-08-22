<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    protected $policies = [
        \App\Models\LeaveRequest::class => \App\Policies\LeaveRequestPolicy::class,
        \App\Models\LeaveSummary::class => \App\Policies\LeaveSummaryPolicy::class,
        \App\Models\LeaveRequest::class => \App\Policies\LeaveRequestPolicy::class,
        \App\Models\LeaveRequest::class => \App\Policies\LeaveRequestPolicy::class,
    ];


    public function boot()
    {
        Paginator::useBootstrapFive();
        View::composer('*', function ($view) {
            $requests = 0;

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->hasRole(['Admin', 'HR'])) {
                    // Admins see all requested leave requests
                    $requests = LeaveRequest::where('status', 'Requested')->count();
                } elseif ($user->hasRole('Manager')) {
                    // Manager sees requests from others in the same department (not their own)
                    $requests = LeaveRequest::where('status', 'Requested')
                        ->whereHas('user', function ($q) use ($user) {
                            $q->where('department_id', $user->department_id)
                                ->where('id', '!=', $user->id); // exclude own requests
                        })
                        ->count();
                }
            }

            $view->with('requests', $requests);
        });

        View::composer('*', function ($view) {
            $requestCount = LeaveRequest::where('status', 'Requested')->count();
            $view->with('requestCount', $requestCount);
        });

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $notificationsQuery = LeaveRequest::with(['user', 'leaveType'])
                    ->where('status', 'Requested');

                if ($user->hasRole('Manager')) {
                    $notificationsQuery->whereHas('user', function ($q) use ($user) {
                        $q->where('department_id', $user->department_id)
                        ->where('id', '!=', $user->id);
                    });
                }

                $messages = $notificationsQuery->latest()->get();
            } else {
                $messages = collect(); // empty collection if not logged in
            }

            $view->with('messages', $messages);
        });
    }
}

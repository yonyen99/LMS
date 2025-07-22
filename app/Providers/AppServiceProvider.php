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
    ];

    public function boot()
    {
        Paginator::useBootstrapFive();
        View::composer('*', function ($view) {
            $requests = 0;

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->hasRole(['Super Admin', 'Admin', 'HR'])) {
                    $requests = LeaveRequest::where('status', 'Requested')->count();

                } elseif ($user->hasRole('Manager')) {
                    $managerDeptId = $user->department_id;

                    $requests = LeaveRequest::where('status', 'Requested')
                        ->whereHas('user', fn($q) => $q->where('department_id', $managerDeptId))
                        ->count();

                } elseif ($user->hasRole('Employee')) {
                    $requests = LeaveRequest::where('status', 'Requested')
                        ->where('user_id', $user->id)
                        ->count();
                }
            }

            $view->with('requests', $requests);
        });
    }
}
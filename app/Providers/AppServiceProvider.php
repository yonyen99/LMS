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
            $statusRequestOptions = ['Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'];
            $view->with('statusRequestOptions', $statusRequestOptions);
        });

        View::composer('*', function ($view) {
        $totalRequests = 0;

        if (Auth::check() && !Auth::user()->hasRole('Employee')) {
            $totalRequests = LeaveRequest::where('status', 'Requested')
            ->where('user_id', '!=', Auth::id())
            ->count();
        }

        $view->with('totalRequests', $totalRequests);
    });
    }
}
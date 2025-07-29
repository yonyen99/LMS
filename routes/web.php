<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\NonWorkingDayController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SubordinateController;
use App\Http\Controllers\LeaveSummaryController;
use App\Http\Controllers\LeaveRequestActionController;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', fn() => redirect()->route('login'));

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/leave-requests/calendar', [LeaveRequestController::class, 'calendar'])->name('leave-requests.calendar');
    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::get('/leave-requests/{id}/history', [LeaveRequestController::class, 'history'])->name('leave-requests.history');
    Route::post('/leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');

    // Only keep one definition of the export-pdf Route with all middleware
    Route::get('/leave-requests/export-pdf', [LeaveRequestController::class, 'exportPDF'])
        ->middleware('auth')
        ->name('leave-requests.exportPDF')
        ->can('export', \App\Models\LeaveRequest::class);
    Route::get('/leave-requests/export-excel', [LeaveRequestController::class, 'exportExcel'])
        ->middleware('auth')
        ->name('leave-requests.exportExcel')
        ->can('export', \App\Models\LeaveRequest::class);

    Route::get('/leave-requests/print', [LeaveRequestController::class, 'print'])
        ->middleware('auth')
        ->name('leave-requests.print')
        ->can('export', \App\Models\LeaveRequest::class);


    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/update-status', [NotificationController::class, 'updateStatus'])->name('notifications.update-status');

    Route::get('/subordinates', [SubordinateController::class, 'index'])->name('subordinates.index');

    Route::resource('leave-summaries', LeaveSummaryController::class);
    Route::get('/user-leaves', [LeaveSummaryController::class, 'userLeave'])->name('user-leave.index');

    Route::resources([
        'roles' => RoleController::class,
        'users' => UserController::class,
        'departments' => DepartmentController::class,
        'leave-types' => LeaveTypeController::class,
        'leave-requests' => LeaveRequestController::class,
        'non-working-days' => NonWorkingDayController::class,
        'subordinates' => SubordinateController::class,
    ]);

    Route::get('/users/view/{id}', [UserController::class, 'view'])
        ->name('users.view')
        ->middleware(['role:Admin|Super Admin|HR|Manager|Team Lead|Employee']);
});

Route::middleware(['signed'])->group(function () {
    Route::get('/leave-requests/email/accept/{id}', [LeaveRequestActionController::class, 'accept'])->name('leave-request.email.accept');
    Route::get('/leave-requests/email/reject/{id}', [LeaveRequestActionController::class, 'reject'])->name('leave-request.email.reject');
    Route::get('/leave-requests/email/cancel/{id}', [LeaveRequestActionController::class, 'cancel'])->name('leave-request.email.cancel');
});

Route::get('auth/google', [GoogleController::class, 'googlepage'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'googlecallback'])->name('google.callback');
Route::get('/leave-requests/{id}/history', [LeaveRequestController::class, 'history'])->name('leave-requests.history');
Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::post('/notifications/{id}/mark-read', [MessageController::class, 'markAsRead']);




Route::get('/leave-requests/export-pdf', [LeaveRequestController::class, 'exportPDF'])
    ->middleware('auth')
    ->name('leave-requests.exportPDF')
    ->can('export', \App\Models\LeaveRequest::class);

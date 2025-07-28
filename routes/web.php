<?php

use App\Http\Controllers\LeaveSummaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeCounterController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\NonWorkingDayController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\SubordinateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])
    ->name('leave-requests.cancel');

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/update-status', [NotificationController::class, 'updateStatus'])
    ->name('notifications.update-status');

Route::get('/leave-requests/calendar', [LeaveRequestController::class, 'calendar'])
    ->name('leave-requests.calendar')
    ->middleware('auth');

// Add route for Subordinates page
Route::get('/subordinates', [SubordinateController::class, 'index'])->name('subordinates.index')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::resource('leave-summaries', LeaveSummaryController::class);
    Route::get('/user-leaves', [LeaveSummaryController::class, 'userLeave'])->name('user-leave.index');
});

Route::resources([
    'roles' => RoleController::class,
    'users' => UserController::class,
    'departments' => DepartmentController::class,
    'leave-types' => LeaveTypeController::class,
    'leave-requests' => LeaveRequestController::class,
    'non-working-days' => NonWorkingDayController::class,
    'subordinates' => SubordinateController::class,
]);

Route::get('leave-request/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');

Route::get('/users/view/{id}', [UserController::class, 'view'])->name('users.view')->middleware(['auth', 'role:Admin|Super Admin|HR|Manager|Team Lead|Employee']);

Route::get('auth/google', [GoogleController::class, 'googlepage'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'googlecallback'])->name('google.callback');
Route::get('/leave-requests/{id}/history', [LeaveRequestController::class, 'history'])->name('leave-requests.history');

Route::middleware(['signed'])->group(function () {
    Route::get('/leave-requests/email/accept/{id}', [App\Http\Controllers\LeaveRequestActionController::class, 'accept'])
        ->name('leave-request.email.accept');
    Route::get('/leave-requests/email/reject/{id}', [App\Http\Controllers\LeaveRequestActionController::class, 'reject'])
        ->name('leave-request.email.reject');
    Route::get('/leave-requests/email/cancel/{id}', [App\Http\Controllers\LeaveRequestActionController::class, 'cancel'])
        ->name('leave-request.email.cancel');
});
Route::get('/leave-requests/{id}/history', [LeaveRequestController::class, 'history'])->name('leave-requests.history');

Route::get('/leave-requests/export-pdf', [LeaveRequestController::class, 'exportPDF'])->name('leave-requests.exportPDF');

<?php

use App\Http\Controllers\CounterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeCounterController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;

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

Route::post('leave-requests/{id}/cancel', [LeaveRequestController::class, 'cancel'])
    ->name('leave-requests.cancel')
    ->middleware('auth');

Route::get('/leave-requests/calendar', [LeaveRequestController::class, 'calendar'])
    ->name('leave-requests.calendar')
    ->middleware('auth');

Route::resources([
    'roles' => RoleController::class,
    'users' => UserController::class,
    'departments' => DepartmentController::class,
    'leave-types' => LeaveTypeController::class,
    'leave-requests' => LeaveRequestController::class,
]);
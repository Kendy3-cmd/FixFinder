<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TotalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// LOGOUT
Route::post('/Logout', [AuthController::class, 'Logout'])->name('logout');

Route::middleware(['auth:employees'])->group(function () {
  // EMPLOYEE DASHBOARD
  Route::inertia('/EmployeeDashboardManage', 'FixFinderEmployee/EmployeeDashboardManage')->name('EmployeeDashboardManage');
  
  Route::get('/EmployeeDashboardProfile', [AuthController::class, 'indexServiceEmployee'])
    ->middleware('auth:employees')
    ->name('EmployeeDashboardProfile');

  Route::get('/EmployeeDashboardManage', [AuthController::class, 'indexServiceEmployeeManage'])
  ->middleware('auth:employees')
  ->name('EmployeeDashboardManage');
  
  Route::put('/employees/{employee_ID}', [AuthController::class, 'EmployeeUpdate'])->name('employee.update');

  Route::put('/employees/{employee_ID}/update-password', [AuthController::class, 'EmployeePasswordUpdate'])
  ->name('employee.password.update')
  ->middleware('auth:employees');

  Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
  Route::patch('/bookings/{booking_ID}/status', [BookingController::class, 'updateStatus']);

  // Correct route for getting availability
  Route::get('/employee/{employee_ID}/availability', [AuthController::class, 'getAvailability'])
  ->name('employee.getAvailability');

  // Route for updating availability
  Route::post('/employee/{employee_ID}/availability/update', [AuthController::class, 'updateAvailability'])
  ->name('employee.updateAvailability');

  Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
  });

Route::middleware(['auth:members'])->group(function () {
  // MEMBER DASHBOARD
  Route::inertia('/MemberDashboardBooking', 'FixFinderMember/MemberDashboardBooking')->name('MemberDashboardBooking');

  Route::get('/MemberDashboardProfile', [AuthController::class, 'indexMember'])
  ->name('MemberDashboardProfile');

  Route::get('/MemberDashboardBooking', [AuthController::class, 'indexMemberBooking'])
  ->middleware('auth:members')
  ->name('MemberDashboardBooking');

  Route::put('/members/{member_ID}', [AuthController::class, 'MemberUpdate'])->name('member.update');

  Route::put('/members/{member_ID}/update-password', [AuthController::class, 'MemberPasswordUpdate'])
    ->name('member.password.update')
    ->middleware('auth:members');

    Route::get('/MemberDashboardViewProfile/{employee_ID}', [AuthController::class, 'showViewProfile'])
    ->name('MemberDashboardViewProfile');

    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

Route::middleware('guest')->group(function () {
  Route::inertia('/LoginRegister', 'LoginRegister')->name('login');

  // REGISTER MEMBER

  Route::inertia('/MemberLoginRegister', 'MemberLoginRegister')->name('MemberLoginRegister');

  Route::post('/MemberRegister', [AuthController::class, 'MemberRegister'])->name('/MemberRegister');
  Route::post('/MemberLogin', [AuthController::class, 'MemberLogin'])->name('/MemberLogin');

  // REGISTER EMPLOYEE

  Route::inertia('/EmployeeLoginRegister', 'EmployeeLoginRegister')->name('EmployeeLoginRegister');
  Route::post('/EmployeeRegister', [AuthController::class, 'EmployeeRegister'])->name('/EmployeeRegister');
  Route::post('/EmployeeLogin', [AuthController::class, 'EmployeeLogin'])->name('/EmployeeLogin');
  
  Route::inertia('/', 'homepage')->name('homepage');
});

// ADMIN
Route::inertia('/AdminDashboardManageUsers', 'FixFinderAdmin/AdminDashboardManageUsers')->name('AdminDashboardManageUsers');
Route::inertia('/AdminDashboardServices', 'FixFinderAdmin/AdminDashboardServices')->name('AdminDashboardServices');
Route::get('/AdminDashboardStatistics', [TotalController::class, 'total'])->name('AdminDashboardStatistics');

// MANAGE USERS
Route::get('/AdminDashboardManageUsers', [AuthController::class, 'indexAdminManageUsers'])->name('AdminDashboardManageUsers');
// Update user route
Route::put('/user/{id}', [AuthController::class, 'updateUsers'])->name('user.update');
// Delete user route
Route::delete('/user/{id}', [AuthController::class, 'deleteUser'])->name('user.delete');

// ADD SERVICES
Route::post('/AddService', [AuthController::class, 'serviceAdd'])->name('serviceAdd');
Route::get('/AdminDashboardServices', [AuthController::class, 'indexService'])->name('AdminDashboardServices');
Route::delete('/service/{service_ID}', [AuthController::class, 'deleteService'])->name('services.destroy');
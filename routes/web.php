<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/register', [RegisterController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/stand/pilih', [DashboardController::class, 'selectStand'])->name('stand.select');
Route::post('/stand/reset', [DashboardController::class, 'clearStand'])->name('stand.clear');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/absensi/masuk', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/absensi/keluar', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
});

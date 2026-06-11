<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $data = [
        'deviceCount' => \App\Models\Device::count(),
        'userCount' => \App\Models\User::count(),
        'attendanceCount' => \App\Models\Attendance::count(),
    ];
    return view('dashboard', $data);
})->name('dashboard');

Route::view('/users', 'users')->name('users');
Route::view('/schedules', 'schedules')->name('schedules');
Route::view('/logs', 'logs')->name('logs');
Route::view('/devices', 'devices')->name('devices');
Route::view('/reports', 'reports')->name('reports');
Route::view('/settings', 'settings')->name('settings');

use App\Http\Controllers\AdmsController;

Route::get('/iclock/cdata', [AdmsController::class, 'handshake']);
Route::post('/iclock/cdata', [AdmsController::class, 'receiveData']);
Route::get('/iclock/getrequest', [AdmsController::class, 'getRequests']);
Route::post('/iclock/devicecmd', [AdmsController::class, 'deviceCmd']);
Route::get('/testcmd', [AdmsController::class, 'testCmd']);

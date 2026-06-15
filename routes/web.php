<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;



Route::get('/', [DashboardController::class, 'index']);

Route::get('/iclock/cdata', [DeviceController::class, 'handshake']);
Route::post('/iclock/cdata', [DeviceController::class, 'receiveData']);

Route::get('/devices', [DashboardController::class, 'devicesIndex']);
Route::get('/devices/pending', [DashboardController::class, 'pendingDevices']);
Route::post('/devices/approve', [DashboardController::class, 'approveDevice']);

Route::get('/employees', [DashboardController::class, 'employeesIndex']);

Route::get('/employees/create', [EmployeeController::class, 'create']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit']);
Route::post('/employees/{id}/update', [EmployeeController::class, 'update']);
Route::post('/employees/{id}/delete', [EmployeeController::class, 'destroy']);
Route::post('/employees/{id}/delete-fingerprint', [EmployeeController::class, 'deleteFingerprint']);

Route::get('/iclock/getrequest', [DeviceController::class, 'getRequests']);
Route::post('/iclock/devicecmd', [DeviceController::class, 'deviceCmd']);

Route::post('/employees/fetch-from-device', [EmployeeController::class, 'fetchFromDevice']);


Route::post('/employees/{id}/photo', [EmployeeController::class, 'uploadPhoto']);
Route::post('/employees/{id}/delete-face', [EmployeeController::class, 'deleteFace']);
Route::post('/employees/{id}/credentials', [EmployeeController::class, 'quickCredentials']);


Route::post('/employees/{id}/enroll-fingerprint', [EmployeeController::class, 'enrollFingerprint']);
Route::post('/employees/{id}/photo-base64', [EmployeeController::class, 'uploadPhotoBase64']);


Route::post('/employees/{id}/delete-photo', [EmployeeController::class, 'deletePhoto']);

Route::post('/employees/{id}/delete-fingerprint/{fid}', [EmployeeController::class, 'deleteSpecificFingerprint']);

Route::post('/employees/{id}/work-pattern', [EmployeeController::class, 'updateWorkPattern']);

// Schedules and Shifts Management
Route::get('/schedules', [ScheduleController::class, 'index']);
Route::get('/roster', [ScheduleController::class, 'roster']);
Route::post('/roster/generate', [ScheduleController::class, 'generateRoster']);
Route::post('/roster/update-day', [ScheduleController::class, 'updateRosterDay']);

Route::post('/schedules/shifts', [ScheduleController::class, 'storeShift']);
Route::post('/schedules/shifts/{id}', [ScheduleController::class, 'updateShift']);
Route::post('/schedules/shifts/{id}/delete', [ScheduleController::class, 'destroyShift']);

Route::post('/schedules/patterns', [ScheduleController::class, 'storePattern']);
Route::post('/schedules/patterns/{id}', [ScheduleController::class, 'updatePattern']);
Route::post('/schedules/patterns/{id}/delete', [ScheduleController::class, 'destroyPattern']);
Route::post('/schedules/patterns/{id}/days', [ScheduleController::class, 'updatePatternDays']);
Route::post('/schedules/patterns/{id}/assign', [ScheduleController::class, 'assignPattern']);

Route::post('/schedules/monthly-patterns', [ScheduleController::class, 'storeMonthlyPattern']);
Route::post('/schedules/monthly-patterns/{id}', [ScheduleController::class, 'updateMonthlyPattern']);
Route::post('/schedules/monthly-patterns/{id}/delete', [ScheduleController::class, 'destroyMonthlyPattern']);
Route::post('/schedules/monthly-patterns/{id}/days', [ScheduleController::class, 'updateMonthlyPatternDays']);
Route::post('/schedules/monthly-patterns/{id}/assign', [ScheduleController::class, 'assignMonthlyPattern']);

// Attendance
use App\Http\Controllers\AttendanceController;
Route::get('/attendance', [AttendanceController::class, 'index']);
Route::post('/attendance/fetch', [AttendanceController::class, 'fetch']);

// Reports
use App\Http\Controllers\ReportController;
Route::get('/reports/absence', [ReportController::class, 'absence'])->name('reports.absence');
Route::get('/reports/absence/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.absence.pdf');
Route::get('/reports/absence/export/excel', [ReportController::class, 'exportExcel'])->name('reports.absence.excel');

// Settings
use App\Http\Controllers\SettingsController;
Route::get('/settings', [SettingsController::class, 'index']);
Route::post('/settings/time', [SettingsController::class, 'updateTime']);
Route::post('/settings/logging-mode', [SettingsController::class, 'updateLoggingMode']);
Route::post('/settings/wallpaper', [SettingsController::class, 'updateWallpaper']);

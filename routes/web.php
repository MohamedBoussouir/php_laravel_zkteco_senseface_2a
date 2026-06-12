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
Route::post('/schedules/shifts', [ScheduleController::class, 'storeShift']);
Route::post('/schedules/shifts/{id}', [ScheduleController::class, 'updateShift']);
Route::post('/schedules/shifts/{id}/delete', [ScheduleController::class, 'destroyShift']);

Route::post('/schedules/patterns', [ScheduleController::class, 'storePattern']);
Route::post('/schedules/patterns/{id}', [ScheduleController::class, 'updatePattern']);
Route::post('/schedules/patterns/{id}/delete', [ScheduleController::class, 'destroyPattern']);
Route::post('/schedules/patterns/{id}/days', [ScheduleController::class, 'updatePatternDays']);

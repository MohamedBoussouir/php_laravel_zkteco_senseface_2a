<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Payload;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::where('status', 'approved')->get();
        $payloadsCount = Payload::count();

        return view('dashboard', compact('devices', 'payloadsCount'));
    }
    
    // عرض صفحة إدارة الأجهزة (Discovery)
    public function devicesIndex()
    {
        $approvedDevices = Device::where('status', 'approved')->get();
        return view('devices.index', compact('approvedDevices'));
    }

    // جلب الأجهزة قيد الانتظار (للـ AJAX)
    public function pendingDevices()
    {
        $pending = Device::where('status', 'pending')->get();
        return response()->json($pending);
    }

    // الموافقة على جهاز (AJAX)
    public function approveDevice(Request $request)
    {
        $device = Device::find($request->id);
        if ($device) {
            $device->update(['status' => 'approved']);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    // عرض صفحة الموظفين
    public function employeesIndex()
    {
        $employees = \App\Models\User::all();
        $workPatterns = \App\Models\WorkPattern::all();
        $devices = \App\Models\Device::all();
        return view('employees.index', compact('employees', 'workPatterns', 'devices'));
    }
}


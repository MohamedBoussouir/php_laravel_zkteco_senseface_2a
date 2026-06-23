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

        // HR & Attendance Metrics
        $totalEmployees = \App\Models\User::count();
        $today = \Carbon\Carbon::today()->toDateString();
        
        $presentToday = \App\Models\Attendance::whereDate('timestamp', $today)
            ->distinct('user_id')
            ->count('user_id');
            
        $absentToday = max(0, $totalEmployees - $presentToday);
        
        $recentPunches = \App\Models\Attendance::with('user')
            ->orderBy('timestamp', 'desc')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'devices', 'payloadsCount', 'totalEmployees', 'presentToday', 'absentToday', 'recentPunches'
        ));
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
        $employees = \App\Models\User::paginate(20);
        $workPatterns = \App\Models\WorkPattern::all();
        $monthlyPatterns = \App\Models\MonthlyPattern::all();
        $devices = \App\Models\Device::all();
        return view('employees.index', compact('employees', 'workPatterns', 'monthlyPatterns', 'devices'));
    }

    // عرض الموظفين المحذوفين (الأرشيف)
    public function archivedEmployeesIndex()
    {
        $employees = \App\Models\User::onlyTrashed()->paginate(20);
        return view('employees.archived', compact('employees'));
    }
}


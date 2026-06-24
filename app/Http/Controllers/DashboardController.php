<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\MonthlyPattern;
use App\Models\Payload;
use App\Models\User;
use App\Models\WorkPattern;
use Illuminate\Http\Request;

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

    // إزالة جهاز (AJAX)
    public function removeDevice(Request $request)
    {
        $device = Device::find($request->id);
        if ($device) {
            $device->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    // عرض صفحة الموظفين
    public function employeesIndex(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_face')) {
            $query->where('has_face', 1);
        }
        if ($request->filled('filter_fp')) {
            $query->whereNotNull('fingerprints')->where('fingerprints', '!=', '[]')->where('fingerprints', '!=', 'null');
        }
        if ($request->filled('filter_photo')) {
            $query->whereNotNull('photo')->where('photo', '!=', '');
        }
        if ($request->filled('filter_card')) {
            $query->whereNotNull('card_number')->where('card_number', '!=', '');
        }
        if ($request->filled('filter_pass')) {
            $query->whereNotNull('password')->where('password', '!=', '');
        }

        // Sort by user_id numerically (MySQL compatible)
        $query->orderByRaw('CAST(user_id AS UNSIGNED) asc');

        $employees = $query->paginate(20)->appends($request->all());
        $workPatterns = WorkPattern::all();
        $monthlyPatterns = MonthlyPattern::all();
        $devices = Device::all();
        return view('employees.index', compact('employees', 'workPatterns', 'monthlyPatterns', 'devices'));
    }

    // عرض الموظفين المحذوفين (الأرشيف)
    public function archivedEmployeesIndex()
    {
        $employees = \App\Models\User::onlyTrashed()->paginate(20);
        return view('employees.archived', compact('employees'));
    }
}


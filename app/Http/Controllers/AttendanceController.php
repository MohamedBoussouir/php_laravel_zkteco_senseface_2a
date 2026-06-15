<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        $query = Attendance::query();

        $query->orderBy('timestamp', 'desc');

        if ($startDate) {
            $query->where('timestamp', '>=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $query->where('timestamp', '<=', $endDate . ' 23:59:59');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('user_id', 'LIKE', '%' . $search . '%');
            });
        }

        $attendances = $query->paginate(50);
        $employees = User::all();

        $calcMode = \App\Models\Setting::where('key', 'calc_mode')->value('value') ?? 'calculated';

        if ($calcMode === 'calculated') {
            foreach ($attendances as $att) {
                $date = \Carbon\Carbon::parse($att->timestamp)->format('Y-m-d');
                
                // Fetch all logs for this user on this day to properly sort and find first/last
                $dayLogs = Attendance::where('user_id', $att->user_id)
                                ->where('timestamp', 'LIKE', $date . '%')
                                ->get()
                                ->sortBy(function($log) {
                                    return \Carbon\Carbon::parse($log->timestamp)->timestamp;
                                })->values();
                                
                if ($dayLogs->count() > 0) {
                    $firstLog = $dayLogs->first();
                    $lastLog = $dayLogs->last();
                    
                    if ($firstLog->id === $att->id) {
                        $att->calculated_status = 'Check-In';
                    } elseif ($lastLog->id === $att->id) {
                        $att->calculated_status = 'Check-Out';
                    } else {
                        $att->calculated_status = 'Intermediate';
                    }
                }
            }
        }

        // Calculate quick stats for the filtered period
        $totalPunches = $query->count();
        
        $uniqueQuery = Attendance::query();
        if ($startDate) $uniqueQuery->where('timestamp', '>=', $startDate . ' 00:00:00');
        if ($endDate) $uniqueQuery->where('timestamp', '<=', $endDate . ' 23:59:59');
        if ($search) {
            $uniqueQuery->where(function ($q) use ($search) {
                $q->where('user_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('user_id', 'LIKE', '%' . $search . '%');
            });
        }
        
        $activeEmployeesCount = $uniqueQuery->distinct('user_id')->count();

        return view('attendance.index', compact('attendances', 'employees', 'startDate', 'endDate', 'search', 'totalPunches', 'activeEmployeesCount'));
    }

    public function fetch()
    {
        $devices = \App\Models\Device::all();
        $count = 0;
        
        foreach ($devices as $device) {
            \App\Models\DeviceCommand::create([
                'device_id' => $device->id,
                'command' => 'DATA QUERY ATTLOG StartTime=2000-01-01 00:00:00 EndTime=2099-12-31 23:59:59', // ADMS command to fetch all logs or trigger sync
            ]);
            $count++;
        }

        return back()->with('success', "Fetch command queued for $count devices. Logs will appear automatically as devices process the command.");
    }
}

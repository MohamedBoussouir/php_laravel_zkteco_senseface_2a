<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\WorkPattern;
use App\Models\WorkPatternDay;

class ScheduleController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        $workPatterns = WorkPattern::with('days')->get();
        $monthlyPatterns = \App\Models\MonthlyPattern::with('days')->get();
        $employees = \App\Models\User::all();
        return view('schedules.index', compact('shifts', 'workPatterns', 'monthlyPatterns', 'employees'));
    }

    public function assignPattern(Request $request, $id)
    {
        $pattern = WorkPattern::findOrFail($id);
        
        // Remove this pattern from all users who currently have it
        \App\Models\User::where('work_pattern_id', $pattern->id)->update(['work_pattern_id' => null]);
        
        // Assign the pattern to the selected users
        $userIds = $request->input('user_ids', []);
        if (!empty($userIds)) {
            \App\Models\User::whereIn('id', $userIds)->update(['work_pattern_id' => $pattern->id]);
        }

        return redirect('/schedules')->with('success', 'Work pattern assigned to selected users successfully.');
    }

    // --- Shifts Management ---
    public function storeShift(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|string',
            'end_time' => 'required|string'
        ]);

        Shift::create($request->only('name', 'start_time', 'end_time'));

        return redirect('/schedules')->with('success', 'Shift created successfully.');
    }

    public function updateShift(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|string',
            'end_time' => 'required|string'
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update($request->only('name', 'start_time', 'end_time'));

        return redirect('/schedules')->with('success', 'Shift updated successfully.');
    }

    public function destroyShift($id)
    {
        Shift::findOrFail($id)->delete();
        return redirect('/schedules')->with('success', 'Shift deleted successfully.');
    }

    // --- Work Patterns Management ---
    public function storePattern(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $pattern = WorkPattern::create($request->only('name'));
        
        // Initialize 7 days empty
        for ($i = 0; $i <= 6; $i++) {
            WorkPatternDay::create([
                'pattern_id' => $pattern->id,
                'day_of_week' => $i,
                'shift_id' => null
            ]);
        }

        return redirect('/schedules')->with('success', 'Work pattern created successfully.');
    }

    public function updatePattern(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $pattern = WorkPattern::findOrFail($id);
        $pattern->update($request->only('name'));
        return redirect('/schedules')->with('success', 'Work pattern updated successfully.');
    }

    public function destroyPattern($id)
    {
        WorkPattern::findOrFail($id)->delete();
        return redirect('/schedules')->with('success', 'Work pattern deleted successfully.');
    }

    public function updatePatternDays(Request $request, $id)
    {
        $pattern = WorkPattern::findOrFail($id);
        
        foreach ($request->input('days', []) as $dayOfWeek => $shiftId) {
            $shiftId = $shiftId ?: null; // Handle empty string as null
            WorkPatternDay::updateOrCreate(
                ['pattern_id' => $pattern->id, 'day_of_week' => $dayOfWeek],
                ['shift_id' => $shiftId]
            );
        }

        return redirect('/schedules')->with('success', 'Work pattern configuration saved.');
    }

    // --- Monthly Patterns Management ---
    public function storeMonthlyPattern(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $pattern = \App\Models\MonthlyPattern::create($request->only('name'));
        
        // Initialize 31 days empty
        for ($i = 1; $i <= 31; $i++) {
            \App\Models\MonthlyPatternDay::create([
                'monthly_pattern_id' => $pattern->id,
                'day_of_month' => $i,
                'shift_id' => null
            ]);
        }

        return redirect('/schedules')->with('success', 'Monthly pattern created successfully.');
    }

    public function updateMonthlyPattern(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $pattern = \App\Models\MonthlyPattern::findOrFail($id);
        $pattern->update($request->only('name'));
        return redirect('/schedules')->with('success', 'Monthly pattern updated successfully.');
    }

    public function destroyMonthlyPattern($id)
    {
        \App\Models\MonthlyPattern::findOrFail($id)->delete();
        return redirect('/schedules')->with('success', 'Monthly pattern deleted successfully.');
    }

    public function updateMonthlyPatternDays(Request $request, $id)
    {
        $pattern = \App\Models\MonthlyPattern::findOrFail($id);
        
        foreach ($request->input('days', []) as $dayOfMonth => $shiftId) {
            $shiftId = $shiftId ?: null; 
            \App\Models\MonthlyPatternDay::updateOrCreate(
                ['monthly_pattern_id' => $pattern->id, 'day_of_month' => $dayOfMonth],
                ['shift_id' => $shiftId]
            );
        }

        return redirect('/schedules')->with('success', 'Monthly pattern configuration saved.');
    }

    public function assignMonthlyPattern(Request $request, $id)
    {
        $pattern = \App\Models\MonthlyPattern::findOrFail($id);
        
        \App\Models\User::where('monthly_pattern_id', $pattern->id)->update(['monthly_pattern_id' => null]);
        
        $userIds = $request->input('user_ids', []);
        if (!empty($userIds)) {
            \App\Models\User::whereIn('id', $userIds)->update([
                'monthly_pattern_id' => $pattern->id,
                'work_pattern_id' => null
            ]);
        }

        return redirect('/schedules')->with('success', 'Monthly pattern assigned to selected users successfully.');
    }

    // --- Premium Monthly Roster ---
    public function roster(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $selectedUserId = $request->input('user_id');
        
        $employees = \App\Models\User::all();
        $shifts = Shift::all();
        
        $roster = [];
        $selectedUser = null;

        if ($selectedUserId) {
            $selectedUser = \App\Models\User::find($selectedUserId);
            $schedules = \App\Models\Schedule::where('user_id', $selectedUserId)
                                            ->whereYear('date', $year)
                                            ->whereMonth('date', $month)
                                            ->get();
            foreach ($schedules as $sched) {
                $roster[$sched->date] = $sched;
            }
        }

        return view('schedules.roster', compact('employees', 'shifts', 'month', 'year', 'selectedUser', 'roster'));
    }

    public function generateRoster(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $userId = $request->input('user_id');
        
        if (!$userId) return back()->with('error', 'No user selected.');

        $employee = \App\Models\User::findOrFail($userId);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $count = 0;

        if ($employee->monthly_pattern_id) {
            $pattern = \App\Models\MonthlyPattern::with('days')->find($employee->monthly_pattern_id);
            if ($pattern) {
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $d);
                    $patternDay = $pattern->days->where('day_of_month', $d)->first();
                    $shiftId = $patternDay ? $patternDay->shift_id : null;
                    $isOff = $shiftId ? 0 : 1;
                    
                    \App\Models\Schedule::updateOrCreate(
                        ['user_id' => $employee->user_id, 'date' => $dateStr],
                        ['shift_id' => $shiftId, 'is_off_day' => $isOff]
                    );
                    $count++;
                }
            }
        } elseif ($employee->work_pattern_id) {
            $pattern = \App\Models\WorkPattern::with('days')->find($employee->work_pattern_id);
            if ($pattern) {
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $d);
                    $dayOfWeek = date('w', strtotime($dateStr)); // 0 (Sun) to 6 (Sat)
                    
                    $patternDay = $pattern->days->where('day_of_week', $dayOfWeek)->first();
                    $shiftId = $patternDay ? $patternDay->shift_id : null;
                    $isOff = $shiftId ? 0 : 1;
                    
                    \App\Models\Schedule::updateOrCreate(
                        ['user_id' => $employee->user_id, 'date' => $dateStr],
                        ['shift_id' => $shiftId, 'is_off_day' => $isOff]
                    );
                    $count++;
                }
            }
        } else {
            return back()->with('error', 'Employee has no assigned Work Pattern or Monthly Pattern.');
        }

        return redirect("/roster?user_id=$employee->id&month=$month&year=$year")->with('success', "Generated $count schedule entries.");
    }

    public function updateRosterDay(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'date' => 'required|date',
            'shift_id' => 'nullable'
        ]);

        $employee = \App\Models\User::where('user_id', $request->user_id)->first();
        if (!$employee) return back();

        $shiftId = $request->shift_id ?: null;
        $isOff = $shiftId ? 0 : 1;

        \App\Models\Schedule::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            ['shift_id' => $shiftId, 'is_off_day' => $isOff]
        );

        $month = date('m', strtotime($request->date));
        $year = date('Y', strtotime($request->date));
        
        return redirect("/roster?user_id=$employee->id&month=$month&year=$year")->with('success', 'Schedule updated.');
    }
}

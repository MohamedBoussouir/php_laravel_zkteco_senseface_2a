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
        return view('schedules.index', compact('shifts', 'workPatterns'));
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
}

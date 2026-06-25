<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    private function getReportData(Request $request)
    {
        $mode = $request->input('mode', 'day');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));
        $startDateParam = $request->input('start_date', date('Y-m-01'));
        $endDateParam = $request->input('end_date', date('Y-m-d'));
        
        $defaultStartTime = $request->input('start_time', '09:00');
        $defaultEndTime = $request->input('end_time', '17:00');

        $startDate = $date;
        $endDate = $date;

        if ($mode === 'month') {
            $startDate = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');
        } elseif ($mode === 'year') {
            $startDate = Carbon::parse($year . '-01-01')->format('Y-m-d');
            $endDate = Carbon::parse($year . '-12-31')->format('Y-m-d');
        } elseif ($mode === 'range') {
            $startDate = $startDateParam;
            $endDate = $endDateParam;
        }

        $employeeId = $request->input('employee_id');
        $search = $request->input('search');

        $employeesQuery = User::with(['workPattern.days', 'monthlyPattern.days']);
        if ($employeeId) {
            $employeesQuery->where('user_id', $employeeId);
        }
        if ($search) {
            $employeesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('user_id', 'like', "%{$search}%");
            });
        }
        
        $employees = $employeesQuery->get();
        $allEmployees = User::select('user_id', 'name')->orderBy('name')->get();
        $schedules = Schedule::whereBetween('date', [$startDate, $endDate])->get();
        $shifts = Shift::all();
        
        $endDatePlusOne = Carbon::parse($endDate)->addDay()->format('Y-m-d');
        $logs = Attendance::whereRaw("CAST(timestamp AS DATETIME) >= ?", [$startDate . ' 00:00:00'])
            ->whereRaw("CAST(timestamp AS DATETIME) <= ?", [$endDatePlusOne . ' 23:59:59'])
            ->orderByRaw("CAST(timestamp AS DATETIME) asc")
            ->get();

        $period = CarbonPeriod::create($startDate, $endDate);
        
        $reportData = [];
        
        $totalWorkingDays = 0;
        $totalPresentDays = 0;
        $totalAbsentDays = 0;
        $totalLateDays = 0;
        $totalDelayMinutes = 0;
        $totalWorkedHours = 0;

        foreach ($employees as $employee) {
            $userLogs = $logs->where('user_id', $employee->user_id)
                ->sortBy(function($log) {
                    return \Carbon\Carbon::parse($log->timestamp)->timestamp;
                })->values();
                
            $userSchedules = $schedules->where('user_id', $employee->user_id)->values();

            $dailyRecords = [];
            $empTotalDays = 0; 
            $empPresentDays = 0; 
            $empAbsentDays = 0; 
            $empLateDays = 0; 
            $empOffDaysCount = 0; 
            $empWorkedOffDays = 0; 
            $empTotalDelayMins = 0;
            $empTotalWorkedHrs = 0;

            foreach ($period as $currentCarbonDate) {
                $currentDate = $currentCarbonDate->format('Y-m-d');
                $userSchedule = $userSchedules->firstWhere('date', $currentDate);
                
                $expectedStart = $defaultStartTime;
                $expectedEnd = $defaultEndTime;
                $isOffDay = false;
                $shiftName = '';

                $foundShiftInSchedule = false;
                if ($userSchedule) {
                    if ($userSchedule->is_off_day) {
                        $isOffDay = true;
                        $foundShiftInSchedule = true;
                    } elseif ($userSchedule->shift_id) {
                        $shift = $shifts->firstWhere('id', $userSchedule->shift_id);
                        if ($shift) {
                            $expectedStart = Carbon::parse($shift->start_time)->format('H:i');
                            $expectedEnd = Carbon::parse($shift->end_time)->format('H:i');
                            $shiftName = $shift->name;
                            $foundShiftInSchedule = true;
                        }
                    }
                } 
                
                if (!$foundShiftInSchedule) {
                    if ($employee->monthly_pattern_id && $employee->monthlyPattern) {
                        $dayOfMonth = $currentCarbonDate->format('j');
                        $patternDay = $employee->monthlyPattern->days->firstWhere('day_of_month', $dayOfMonth);
                        if ($patternDay) {
                            if ($patternDay->shift_id) {
                                $shift = $shifts->firstWhere('id', $patternDay->shift_id);
                                if ($shift) {
                                    $expectedStart = Carbon::parse($shift->start_time)->format('H:i');
                                    $expectedEnd = Carbon::parse($shift->end_time)->format('H:i');
                                    $shiftName = $shift->name;
                                }
                            } else {
                                $isOffDay = true;
                            }
                        }
                    } elseif ($employee->work_pattern_id && $employee->workPattern) {
                        $dOfWeek = $currentCarbonDate->format('w');
                        $patternDay = $employee->workPattern->days->firstWhere('day_of_week', $dOfWeek);
                        if ($patternDay) {
                            if ($patternDay->shift_id) {
                                $shift = $shifts->firstWhere('id', $patternDay->shift_id);
                                if ($shift) {
                                    $expectedStart = Carbon::parse($shift->start_time)->format('H:i');
                                    $expectedEnd = Carbon::parse($shift->end_time)->format('H:i');
                                    $shiftName = $shift->name;
                                }
                            } else {
                                $isOffDay = true;
                            }
                        }
                    } else {
                        $dOfWeek = $currentCarbonDate->format('w');
                        if ($dOfWeek == 0 || $dOfWeek == 6) {
                            $isOffDay = true;
                        }
                    }
                }

                $startMin = (int)Carbon::parse($expectedStart)->format('H') * 60 + (int)Carbon::parse($expectedStart)->format('i');
                $endMin = (int)Carbon::parse($expectedEnd)->format('H') * 60 + (int)Carbon::parse($expectedEnd)->format('i');
                $isNightShift = $endMin < $startMin;
                
                $dayLogs = [];
                if ($isNightShift) {
                    $nextDateStr = $currentCarbonDate->copy()->addDay()->format('Y-m-d');
                    foreach($userLogs as $l) {
                        $ts = $l->timestamp;
                        if (str_starts_with($ts, $currentDate)) {
                            $m = (int)Carbon::parse($ts)->format('H') * 60 + (int)Carbon::parse($ts)->format('i');
                            if ($m >= $startMin - 300) $dayLogs[] = $l;
                        } elseif (str_starts_with($ts, $nextDateStr)) {
                            $m = (int)Carbon::parse($ts)->format('H') * 60 + (int)Carbon::parse($ts)->format('i');
                            if ($m <= $endMin + 360) $dayLogs[] = $l;
                        }
                    }
                } else {
                    foreach($userLogs as $l) {
                        if (str_starts_with($l->timestamp, $currentDate)) {
                            $dayLogs[] = $l;
                        }
                    }
                }

                if ($isOffDay) {
                    $empOffDaysCount++;
                    if (count($dayLogs) === 0) {
                        $dailyRecords[] = [
                            'date' => $currentDate,
                            'first' => '--',
                            'last' => '--',
                            'worked' => '--',
                            'delay' => '--',
                            'expected' => 'Off Day',
                            'status' => 'Off Day'
                        ];
                    } else {
                        $empWorkedOffDays++;
                        
                        $calcMode = \App\Models\Setting::where('key', 'calc_mode')->value('value') ?? 'calculated';
                        $firstLog = null;
                        $lastLog = null;
                        
                        if ($calcMode === 'device') {
                            foreach($dayLogs as $dl) {
                                if ((string)$dl->status === '0') {
                                    if (!$firstLog) $firstLog = $dl;
                                }
                                if ((string)$dl->status === '1') {
                                    $lastLog = $dl;
                                }
                            }
                        } else {
                            $firstLog = $dayLogs[0];
                            $lastLog = $dayLogs[count($dayLogs) - 1];
                        }

                        if (!$firstLog || !$lastLog) {
                            $dailyRecords[] = [
                                'date' => $currentDate,
                                'first' => $firstLog ? Carbon::parse($firstLog->timestamp)->format('H:i') : '--',
                                'last' => $lastLog ? Carbon::parse($lastLog->timestamp)->format('H:i') : '--',
                                'worked' => '--',
                                'delay' => '--',
                                'expected' => 'Off Day (Worked)',
                                'status' => 'Invalid Punch'
                            ];
                            continue;
                        }

                        $first = $firstLog->timestamp;
                        $last = $lastLog->timestamp;
                        $diffMs = Carbon::parse($last)->diffInSeconds(Carbon::parse($first));
                        $hoursWorked = round($diffMs / 3600, 2);
                        $empTotalWorkedHrs += $hoursWorked;
                        $totalWorkedHours += $hoursWorked;

                        $dailyRecords[] = [
                            'date' => $currentDate,
                            'first' => Carbon::parse($first)->format('H:i'),
                            'last' => Carbon::parse($last)->format('H:i'),
                            'worked' => $hoursWorked . ' hrs',
                            'delay' => '--',
                            'expected' => 'Off Day (Worked)',
                            'status' => 'On Time'
                        ];
                    }
                } else {
                    $empTotalDays++; 
                    $totalWorkingDays++;
                    if (count($dayLogs) === 0) {
                        $empAbsentDays++;
                        $totalAbsentDays++;
                        $dailyRecords[] = [
                            'date' => $currentDate,
                            'first' => '--',
                            'last' => '--',
                            'worked' => '--',
                            'delay' => '--',
                            'expected' => $expectedStart . ' - ' . $expectedEnd . ($shiftName ? " ($shiftName)" : ''),
                            'status' => 'Absent'
                        ];
                    } else {
                        $empPresentDays++;
                        $totalPresentDays++;
                        
                        $calcMode = \App\Models\Setting::where('key', 'calc_mode')->value('value') ?? 'calculated';
                        
                        $firstLog = null;
                        $lastLog = null;
                        
                        if ($calcMode === 'device') {
                            foreach($dayLogs as $dl) {
                                if ((string)$dl->status === '0') {
                                    if (!$firstLog) $firstLog = $dl;
                                }
                                if ((string)$dl->status === '1') {
                                    $lastLog = $dl; // keeps overwriting so it gets the last one
                                }
                            }
                        } else {
                            $firstLog = $dayLogs[0];
                            $lastLog = $dayLogs[count($dayLogs) - 1];
                        }
                        
                        if (!$firstLog || !$lastLog) {
                            // If device mode and missing check-in or check-out, we consider it absent or missing punch
                            $dailyRecords[] = [
                                'date' => $currentDate,
                                'first' => '--',
                                'last' => '--',
                                'worked' => '--',
                                'delay' => '--',
                                'expected' => $expectedStart . ' - ' . $expectedEnd . ($shiftName ? " ($shiftName)" : ''),
                                'status' => 'Invalid Punch (Check Mode)'
                            ];
                            continue; // skip the rest of the calculations for this day
                        }
                        
                        $first = $firstLog->timestamp;
                        $last = $lastLog->timestamp;

                        $diffMs = abs(Carbon::parse($last)->diffInSeconds(Carbon::parse($first)));
                        $hoursWorked = round($diffMs / 3600, 2);
                        $empTotalWorkedHrs += $hoursWorked;
                        $totalWorkedHours += $hoursWorked;

                        $firstPunchDate = Carbon::parse($first);
                        $expectedStartDate = Carbon::parse($currentDate . ' ' . $expectedStart . ':00');
                        
                        $delayVal = 0;
                        $delayStr = '--';
                        $status = 'On Time';
                        
                        $actualDelayMins = (int) abs($firstPunchDate->diffInMinutes($expectedStartDate));
                        if ($firstPunchDate->gt($expectedStartDate) && $actualDelayMins > 0) {
                            $delayVal = $actualDelayMins;
                            $delayStr = '+' . $delayVal . ' mins';
                            $status = 'Late';
                            $empLateDays++;
                            $totalLateDays++;
                            $empTotalDelayMins += $delayVal;
                            $totalDelayMinutes += $delayVal;
                        }

                        $dailyRecords[] = [
                            'date' => $currentDate,
                            'first' => Carbon::parse($first)->format('H:i'),
                            'last' => Carbon::parse($last)->format('H:i'),
                            'worked' => $hoursWorked . ' hrs',
                            'delay' => $delayStr,
                            'expected' => $expectedStart . ' - ' . $expectedEnd . ($shiftName ? " ($shiftName)" : ''),
                            'status' => $status
                        ];
                    }
                }
            }

            if ($mode === 'day') {
                $record = $dailyRecords[0] ?? [
                    'date' => $date,
                    'first' => '--',
                    'last' => '--',
                    'worked' => '--',
                    'delay' => '--',
                    'expected' => $defaultStartTime . ' - ' . $defaultEndTime,
                    'status' => 'Absent'
                ];
                $reportData[] = [
                    'name' => $employee->name,
                    'user_id' => $employee->user_id,
                    'first' => $record['first'],
                    'last' => $record['last'],
                    'worked' => $record['worked'],
                    'delay' => $record['delay'],
                    'expected' => $record['expected'],
                    'status' => $record['status'],
                    'daily_records' => $dailyRecords
                ];
            } else {
                $reportData[] = [
                    'name' => $employee->name,
                    'user_id' => $employee->user_id,
                    'totalDays' => $empTotalDays,
                    'presentDays' => $empPresentDays,
                    'absentDays' => $empAbsentDays,
                    'lateDays' => $empLateDays,
                    'totalDelayMins' => $empTotalDelayMins,
                    'totalWorkedHrs' => $empTotalWorkedHrs,
                    'daily_records' => $dailyRecords
                ];
            }
        }

        $attendanceRate = $totalWorkingDays > 0 ? round(($totalPresentDays / $totalWorkingDays) * 100, 1) : 0;

        return compact(
            'reportData', 'mode', 'date', 'month', 'year', 'startDateParam', 'endDateParam', 'defaultStartTime', 'defaultEndTime',
            'totalWorkingDays', 'totalPresentDays', 'totalAbsentDays', 'totalLateDays', 'totalDelayMinutes', 'totalWorkedHours', 'attendanceRate',
            'allEmployees', 'employeeId'
        );
    }

    public function absence(Request $request)
    {
        $data = $this->getReportData($request);
        return view('reports.absence', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', $data);
        // Using landscape orientation for better table fit
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Attendance_Lateness_Report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AttendanceReportExport($data), 'Attendance_Lateness_Report.xlsx');
    }
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 22px; color: #111; }
        .header p { margin: 3px 0; color: #555; font-size: 11px; }
        .kpi-container { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .kpi-container td { padding: 8px; border: 1px solid #ddd; text-align: center; background: #f9fafb; width: 25%; }
        .kpi-container h3 { margin: 3px 0; font-size: 16px; color: #111; }
        .kpi-container p { margin: 2px 0; font-size: 10px; color: #555; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; page-break-inside: auto; }
        table.data-table th, table.data-table td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 10px; }
        table.data-table th { background-color: #1e293b; font-weight: bold; color: #fff; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; display: inline-block; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
        .text-muted { color: #999; }
        .bg-danger { background: #fee2e2; color: #dc2626; }
        .bg-success { background: #dcfce7; color: #16a34a; }
        .bg-warning { background: #fef9c3; color: #ca8a04; }
        .bg-secondary { background: #f1f5f9; color: #64748b; }
        .bg-info { background: #e0f2fe; color: #0284c7; }
        .bg-off { background: #ede9fe; color: #7c3aed; }
        .employee-section { margin-top: 20px; page-break-inside: avoid; }
        .employee-header { background: #f1f5f9; padding: 8px 12px; border-radius: 4px; margin-bottom: 8px; font-size: 13px; font-weight: bold; color: #1e293b; border-left: 4px solid #3b82f6; }
        .daily-table th { font-size: 9px; padding: 5px 6px; }
        .daily-table td { font-size: 9px; padding: 5px 6px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Attendance & Lateness Report</h1>
        <p>
            Mode: {{ ucfirst($mode) }} | 
            @if($mode === 'day')
                Date: {{ $date }}
            @elseif($mode === 'month')
                Month: {{ $month }}
            @elseif($mode === 'year')
                Year: {{ $year }}
            @else
                Range: {{ $startDateParam }} to {{ $endDateParam }}
            @endif
        </p>
    </div>

    <table class="kpi-container">
        <tr>
            <td>
                <p>Working Days</p>
                <h3>{{ $totalWorkingDays }}</h3>
            </td>
            <td>
                <p>Present</p>
                <h3>{{ $totalPresentDays }}</h3>
            </td>
            <td>
                <p>Absent</p>
                <h3>{{ $totalAbsentDays }}</h3>
            </td>
            <td>
                <p>Late Days</p>
                <h3 class="text-danger">{{ $totalLateDays }}</h3>
            </td>
        </tr>
    </table>

    @if($mode === 'day')
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>ID</th>
                    <th>First Punch</th>
                    <th>Last Punch</th>
                    <th>Expected</th>
                    <th>Status</th>
                    <th>Delay (Mins)</th>
                    <th>Worked Hrs</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                    <tr>
                        <td><strong>{{ $row['name'] }}</strong></td>
                        <td>{{ $row['user_id'] }}</td>
                        <td>{{ $row['first'] }}</td>
                        <td>{{ $row['last'] }}</td>
                        <td>{{ $row['expected'] }}</td>
                        <td>
                            @if($row['status'] == 'Late')
                                <span class="badge bg-danger">Late</span>
                            @elseif($row['status'] == 'On Time')
                                <span class="badge bg-success">On Time</span>
                            @elseif($row['status'] == 'Absent')
                                <span class="badge bg-secondary">Absent</span>
                            @elseif($row['status'] == 'Invalid Punch')
                                <span class="badge bg-warning">Invalid</span>
                            @else
                                <span class="badge bg-info">Off Day</span>
                            @endif
                        </td>
                        <td class="{{ $row['status'] == 'Late' ? 'text-danger' : '' }}">
                            {{ $row['delay'] }}
                        </td>
                        <td>{{ $row['worked'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @foreach($reportData as $row)
            <div class="employee-section">
                <div class="employee-header">
                    {{ $row['name'] }} (ID: {{ $row['user_id'] }})
                    &mdash;
                    Work Days: {{ $row['totalDays'] }} |
                    Present: {{ $row['presentDays'] }} |
                    Absent: {{ $row['absentDays'] }} |
                    Late: {{ $row['lateDays'] }} |
                    Delay: {{ $row['totalDelayMins'] }} mins |
                    Worked: {{ $row['totalWorkedHrs'] }} hrs
                </div>

                @if(!empty($row['daily_records']) && count($row['daily_records']) > 0)
                    <table class="data-table daily-table">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Date</th>
                                <th style="width: 10%;">First Punch</th>
                                <th style="width: 10%;">Last Punch</th>
                                <th style="width: 20%;">Shift Expected</th>
                                <th style="width: 12%;">Delay</th>
                                <th style="width: 10%;">Work Hrs</th>
                                <th style="width: 10%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row['daily_records'] as $day)
                                <tr>
                                    <td>{{ $day['date'] }}</td>
                                    <td>{{ $day['first'] }}</td>
                                    <td>{{ $day['last'] }}</td>
                                    <td>{{ $day['expected'] }}</td>
                                    <td class="{{ $day['status'] === 'Late' ? 'text-danger' : '' }}">{{ $day['delay'] }}</td>
                                    <td>{{ $day['worked'] }}</td>
                                    <td>
                                        @if($day['status'] === 'On Time')
                                            <span class="badge bg-success">On Time</span>
                                        @elseif($day['status'] === 'Late')
                                            <span class="badge bg-danger">Late</span>
                                        @elseif($day['status'] === 'Absent')
                                            <span class="badge bg-secondary">Absent</span>
                                        @elseif($day['status'] === 'Off Day')
                                            <span class="badge bg-off">Off Day</span>
                                        @elseif($day['status'] === 'Invalid Punch')
                                            <span class="badge bg-warning">Invalid</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $day['status'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted" style="padding: 5px 12px;">No daily records available.</p>
                @endif
            </div>
        @endforeach
    @endif

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #111; }
        .header p { margin: 5px 0; color: #555; }
        .kpi-container { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .kpi-container td { padding: 10px; border: 1px solid #ddd; text-align: center; background: #f9fafb; width: 25%; }
        .kpi-container h3 { margin: 5px 0; font-size: 18px; color: #111; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; font-weight: bold; color: #333; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
        .bg-danger { background: #fee2e2; color: #dc2626; border: 1px solid #f87171; }
        .bg-success { background: #dcfce7; color: #16a34a; border: 1px solid #4ade80; }
        .bg-warning { background: #fef9c3; color: #ca8a04; border: 1px solid #facc15; }
        .bg-secondary { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
        .bg-info { background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc; }
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

    <table class="data-table">
        <thead>
            @if($mode === 'day')
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
            @else
                <tr>
                    <th>Employee Name</th>
                    <th>ID</th>
                    <th>Work Days</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Total Delay</th>
                    <th>Worked Hrs</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($reportData as $row)
                @if($mode === 'day')
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
                @else
                    <tr>
                        <td><strong>{{ $row['name'] }}</strong></td>
                        <td>{{ $row['user_id'] }}</td>
                        <td>{{ $row['totalDays'] }}</td>
                        <td>{{ $row['presentDays'] }}</td>
                        <td>{{ $row['absentDays'] }}</td>
                        <td class="{{ $row['lateDays'] > 0 ? 'text-danger' : '' }}">{{ $row['lateDays'] }}</td>
                        <td class="{{ $row['totalDelayMins'] > 0 ? 'text-danger' : '' }}">{{ $row['totalDelayMins'] }} mins</td>
                        <td>{{ $row['totalWorkedHrs'] }} hrs</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

</body>
</html>

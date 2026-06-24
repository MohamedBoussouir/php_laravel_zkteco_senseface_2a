<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Attendance Records') }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 22px; color: #111; }
        .header p { margin: 3px 0; color: #555; font-size: 11px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; page-break-inside: auto; }
        table.data-table th, table.data-table td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 10px; }
        table.data-table th { background-color: #1e293b; font-weight: bold; color: #fff; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; display: inline-block; }
        .bg-success { background: #dcfce7; color: #16a34a; }
        .bg-danger { background: #fee2e2; color: #dc2626; }
        .bg-warning { background: #fef9c3; color: #ca8a04; }
        .bg-info { background: #e0f2fe; color: #0284c7; }
        .bg-secondary { background: #f1f5f9; color: #64748b; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ __('Attendance Records') }}</h1>
        <p>
            @if($startDate)
                {{ __('From:') }} {{ $startDate }}
            @endif
            @if($endDate)
                {{ __('To:') }} {{ $endDate }}
            @endif
        </p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('Employee ID') }}</th>
                <th>{{ __('Employee Name') }}</th>
                <th>{{ __('Timestamp') }}</th>
                <th>{{ __('Device State') }}</th>
                <th>{{ __('Calculated Status') }}</th>
                <th>{{ __('Device ID') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $att)
                <tr>
                    <td>{{ $att->user_id }}</td>
                    <td><strong>{{ $att->user ? $att->user->name : $att->user_name }}</strong></td>
                    <td>{{ $att->timestamp }}</td>
                    <td>
                        @if($att->state == 1)
                            <span class="badge bg-success">{{ __('Check-In') }}</span>
                        @elseif($att->state == 2)
                            <span class="badge bg-danger">{{ __('Check-Out') }}</span>
                        @elseif($att->state == 4)
                            <span class="badge bg-info">{{ __('Overtime In') }}</span>
                        @elseif($att->state == 5)
                            <span class="badge bg-warning">{{ __('Overtime Out') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                        @endif
                    </td>
                    <td>
                        @if(isset($att->calculated_status))
                            @if($att->calculated_status == 'Check-In')
                                <span class="badge bg-success">{{ __('Check-In') }}</span>
                            @elseif($att->calculated_status == 'Check-Out')
                                <span class="badge bg-danger">{{ __('Check-Out') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Intermediate') }}</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                        @endif
                    </td>
                    <td>{{ $att->device_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>

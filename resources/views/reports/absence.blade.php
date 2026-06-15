@extends('layouts.app')

@push('styles')
<style>
    .stats-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .kpi-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: 0.3s;
    }
    .kpi-card:hover { transform: translateY(-5px); }
    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    .kpi-info h3 { margin: 0; font-size: 1.5rem; color: white; }
    .kpi-info p { margin: 5px 0 0 0; color: var(--text-muted); font-size: 0.85rem; }

    .filter-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .table-container {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px 20px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { background: rgba(0,0,0,0.2); color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    td { color: var(--text-main); font-size: 0.95rem; }
    tr:hover td { background: rgba(255,255,255,0.02); }
    
    .form-control, .form-select { width: 100%; padding: 10px; background: rgba(0,0,0,0.2); color: white; border-radius: 6px; border: 1px solid var(--border-color); outline:none; }
    .form-control:focus, .form-select:focus { border-color: var(--accent); }
    
    .btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem; transition: 0.2s;}
    .btn-primary { background: var(--accent); color: white; }
    .btn-primary:hover { filter: brightness(1.1); }
    
    .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: bold; }
    .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .badge-danger { background: rgba(244, 63, 94, 0.2); color: #f43f5e; }
    .badge-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .badge-secondary { background: rgba(100, 116, 139, 0.2); color: #94a3b8; }
    
    .details-row { background: rgba(0,0,0,0.15); display: none; }
    .details-row.active { display: table-row; }
    .details-table { margin: 10px 0; border: 1px solid rgba(255,255,255,0.05); }
    .details-table th { background: rgba(0,0,0,0.3); font-size: 0.75rem; padding: 10px; }
    .details-table td { font-size: 0.85rem; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }
</style>
@endpush

@section('content')

<div class="top-bar" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title" style="margin: 0;"><i class="fas fa-file-alt text-blue-400"></i> Attendance & Lateness Report</h1>
</div>

<!-- Filters -->
<div class="filter-card">
    <form action="/reports/absence" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: flex-end;">
        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">Select Period Mode</label>
            <select name="mode" id="report-mode" class="form-select" onchange="toggleInputs(this.value)">
                <option value="day" {{ $mode == 'day' ? 'selected' : '' }}>Daily</option>
                <option value="month" {{ $mode == 'month' ? 'selected' : '' }}>Monthly</option>
                <option value="year" {{ $mode == 'year' ? 'selected' : '' }}>Yearly</option>
                <option value="range" {{ $mode == 'range' ? 'selected' : '' }}>Date Range</option>
            </select>
        </div>

        <div id="group-day" style="display: {{ $mode == 'day' ? 'block' : 'none' }};">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">Select Date</label>
            <input type="date" name="date" class="form-control" value="{{ $date }}">
        </div>

        <div id="group-month" style="display: {{ $mode == 'month' ? 'block' : 'none' }};">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">Select Month</label>
            <input type="month" name="month" class="form-control" value="{{ $month }}">
        </div>

        <div id="group-year" style="display: {{ $mode == 'year' ? 'block' : 'none' }};">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">Select Year</label>
            <input type="number" name="year" class="form-control" value="{{ $year }}" min="2000" max="2100">
        </div>

        <div id="group-range" style="display: {{ $mode == 'range' ? 'flex' : 'none' }}; gap: 10px; align-items: center; grid-column: span 2;">
            <div style="flex:1;">
                <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDateParam }}">
            </div>
            <span style="color:var(--text-muted); padding-top:25px;">to</span>
            <div style="flex:1;">
                <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDateParam }}">
            </div>
        </div>

        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">Default Start Time</label>
            <input type="time" name="start_time" class="form-control" value="{{ $defaultStartTime }}">
        </div>

        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">Default End Time</label>
            <input type="time" name="end_time" class="form-control" value="{{ $defaultEndTime }}">
        </div>

        <div style="display: flex; gap: 10px; align-items: flex-end;">
            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
            
            <div style="position: relative; flex: 1;">
                <button type="button" class="btn" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);" onclick="document.getElementById('exportMenu').classList.toggle('show')">
                    <i class="fas fa-download"></i> Export <i class="fas fa-chevron-down" style="font-size: 0.8em; margin-left: 5px;"></i>
                </button>
                <div id="exportMenu" class="dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--card-bg); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; margin-top: 5px; z-index: 100; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);">
                    <a href="{{ route('reports.absence.pdf', request()->all()) }}" target="_blank" style="display: block; padding: 10px 15px; color: white; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <i class="fas fa-file-pdf" style="color: #f43f5e; width: 20px;"></i> Export PDF
                    </a>
                    <a href="{{ route('reports.absence.excel', request()->all()) }}" style="display: block; padding: 10px 15px; color: white; text-decoration: none;">
                        <i class="fas fa-file-excel" style="color: #10b981; width: 20px;"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- KPI Row -->
<div class="stats-container">
    <div class="kpi-card" style="border-left: 4px solid #10b981;">
        <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="kpi-info">
            <p>Attendance Rate</p>
            <h3 style="color: #10b981;">{{ $attendanceRate }}%</h3>
            <p style="font-size: 0.75rem;">{{ $totalPresentDays }} / {{ $totalWorkingDays }} Work Days</p>
        </div>
    </div>
    
    <div class="kpi-card" style="border-left: 4px solid #6366f1;">
        <div class="kpi-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="kpi-info">
            <p>Present</p>
            <h3>{{ $totalPresentDays }} <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted)">Days</span></h3>
            <p style="font-size: 0.75rem;">Worked Hrs: {{ number_format($totalWorkedHours, 1) }} hrs</p>
        </div>
    </div>
    
    <div class="kpi-card" style="border-left: 4px solid #64748b;">
        <div class="kpi-icon" style="background: rgba(100, 116, 139, 0.1); color: #94a3b8;">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="kpi-info">
            <p>Absent</p>
            <h3>{{ $totalAbsentDays }} <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted)">Days</span></h3>
        </div>
    </div>
    
    <div class="kpi-card" style="border-left: 4px solid #f43f5e;">
        <div class="kpi-icon" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="kpi-info">
            <p>Late / Tardy</p>
            <h3 style="color: #f43f5e;">{{ $totalLateDays }} <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted)">Days</span></h3>
            <p style="font-size: 0.75rem; color:#f43f5e;">Total Delay: {{ $totalDelayMinutes }} mins</p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container" style="overflow-x: auto;">
    <table style="min-width: 900px;">
        <thead>
            @if($mode === 'day')
                <tr>
                    <th>Employee</th>
                    <th>First Punch</th>
                    <th>Last Punch</th>
                    <th>Expected</th>
                    <th>Status</th>
                    <th>Delay (Mins)</th>
                    <th>Worked Hrs</th>
                    <th style="width: 80px;">Action</th>
                </tr>
            @else
                <tr>
                    <th>Employee</th>
                    <th>Work Days</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Total Delay</th>
                    <th>Worked Hrs</th>
                    <th style="width: 80px;">Action</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($reportData as $index => $row)
                @if($mode === 'day')
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: white;">{{ $row['name'] }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">ID: {{ $row['user_id'] }}</div>
                        </td>
                        <td>{{ $row['first'] }}</td>
                        <td>{{ $row['last'] }}</td>
                        <td><span style="background: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 4px;">{{ $row['expected'] }}</span></td>
                        <td>
                            @if($row['status'] == 'Late')
                                <span class="badge badge-danger">Late 🔴</span>
                            @elseif($row['status'] == 'On Time')
                                <span class="badge badge-success">On Time 🟢</span>
                            @elseif($row['status'] == 'Absent')
                                <span class="badge badge-secondary">Absent 📁</span>
                            @elseif($row['status'] == 'Invalid Punch')
                                <span class="badge badge-warning">Invalid Punch ⚠️</span>
                            @else
                                <span class="badge" style="background: rgba(56, 189, 248, 0.1); color: #38bdf8;">Off Day 🔵</span>
                            @endif
                        </td>
                        <td style="{{ $row['status'] == 'Late' ? 'color:#f43f5e; font-weight:bold;' : '' }}">{{ $row['delay'] }}</td>
                        <td>{{ $row['worked'] }}</td>
                        <td>
                            <button class="btn" style="background: rgba(255,255,255,0.1); padding: 6px 10px; font-size: 0.8rem;" onclick="toggleDetails({{ $index }})">
                                Details
                            </button>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: white;">{{ $row['name'] }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">ID: {{ $row['user_id'] }}</div>
                        </td>
                        <td>{{ $row['totalDays'] }}</td>
                        <td style="color:#34d399; font-weight:bold;">{{ $row['presentDays'] }}</td>
                        <td style="color:#94a3b8;">{{ $row['absentDays'] }}</td>
                        <td style="{{ $row['lateDays'] > 0 ? 'color:#f43f5e; font-weight:bold;' : '' }}">{{ $row['lateDays'] }}</td>
                        <td style="{{ $row['totalDelayMins'] > 0 ? 'color:#f43f5e; font-weight:bold;' : '' }}">{{ $row['totalDelayMins'] }} mins</td>
                        <td>{{ number_format($row['totalWorkedHrs'], 1) }} hrs</td>
                        <td>
                            <button class="btn" style="background: rgba(255,255,255,0.1); padding: 6px 10px; font-size: 0.8rem;" onclick="toggleDetails({{ $index }})">
                                Details
                            </button>
                        </td>
                    </tr>
                @endif
                
                <!-- Drawer Row -->
                <tr class="details-row" id="details-{{ $index }}">
                    <td colspan="8" style="padding: 0;">
                        <div style="padding: 20px; background: rgba(0,0,0,0.2);">
                            <h6 style="margin-top: 0; color: var(--accent); margin-bottom: 15px;"><i class="fas fa-calendar-alt"></i> Daily Attendance Log for {{ $row['name'] }}</h6>
                            <table class="details-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>First Punch</th>
                                        <th>Last Punch</th>
                                        <th>Shift Expected</th>
                                        <th>Delay</th>
                                        <th>Work Hrs</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($row['daily_records'] as $daily)
                                        <tr>
                                            <td style="color:white;">{{ $daily['date'] }}</td>
                                            <td>{{ $daily['first'] }}</td>
                                            <td>{{ $daily['last'] }}</td>
                                            <td><span style="background: rgba(255,255,255,0.05); padding: 2px 6px; border-radius: 4px;">{{ $daily['expected'] }}</span></td>
                                            <td style="{{ $daily['status'] == 'Late' ? 'color:#f43f5e; font-weight:bold;' : '' }}">{{ $daily['delay'] }}</td>
                                            <td>{{ $daily['worked'] }}</td>
                                            <td>
                                                @if($daily['status'] == 'Late')
                                                    <span style="color:#f43f5e;"><i class="fas fa-circle" style="font-size:8px;"></i> Late</span>
                                                @elseif($daily['status'] == 'On Time')
                                                    <span style="color:#34d399;"><i class="fas fa-circle" style="font-size:8px;"></i> On Time</span>
                                                @elseif($daily['status'] == 'Absent')
                                                    <span style="color:#94a3b8;"><i class="fas fa-circle" style="font-size:8px;"></i> Absent</span>
                                                @elseif($daily['status'] == 'Invalid Punch')
                                                    <span style="color:#fbbf24;"><i class="fas fa-circle" style="font-size:8px;"></i> Invalid Punch</span>
                                                @else
                                                    <span style="color:#38bdf8;"><i class="fas fa-circle" style="font-size:8px;"></i> Off Day</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p style="font-size: 1.1rem;">No records found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
function toggleInputs(mode) {
    document.getElementById('group-day').style.display = 'none';
    document.getElementById('group-month').style.display = 'none';
    document.getElementById('group-year').style.display = 'none';
    document.getElementById('group-range').style.display = 'none';
    
    if(mode === 'day') document.getElementById('group-day').style.display = 'block';
    if(mode === 'month') document.getElementById('group-month').style.display = 'block';
    if(mode === 'year') document.getElementById('group-year').style.display = 'block';
    if(mode === 'range') {
        document.getElementById('group-range').style.display = 'flex';
    }
}

function toggleDetails(index) {
    const el = document.getElementById('details-' + index);
    if(el.classList.contains('active')) {
        el.classList.remove('active');
    } else {
        el.classList.add('active');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    var menu = document.getElementById('exportMenu');
    if (menu && menu.classList.contains('show') && !event.target.closest('.dropdown-menu') && !event.target.closest('button[onclick*="exportMenu"]')) {
        menu.classList.remove('show');
    }
});
</script>
<style>
    .dropdown-menu.show { display: block !important; }
    .dropdown-menu a:hover { background: rgba(255,255,255,0.05) !important; }
</style>
@endpush

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
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
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
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .table-container {
        overflow: hidden;
    }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th, td { padding: 15px 20px; text-align: left; }
    th { background: rgba(0,0,0,0.3); color: var(--text-muted); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--glass-border); }
    td { color: var(--text-main); font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.02); transition: background 0.3s; }
    tr:hover td { background: rgba(255,255,255,0.03); }
    
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; }
    .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
    .badge-danger { background: rgba(244, 63, 94, 0.2); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.4); }
    .badge-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
    .badge-secondary { background: rgba(100, 116, 139, 0.2); color: #94a3b8; border: 1px solid rgba(100, 116, 139, 0.4); }
    
    .details-row { background: rgba(0,0,0,0.15); display: none; }
    .details-row.active { display: table-row; }
    .details-table { margin: 10px 0; border: 1px solid rgba(255,255,255,0.05); }
    .details-table th { background: rgba(0,0,0,0.3); font-size: 0.75rem; padding: 10px; }
    .details-table td { font-size: 0.85rem; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }

    @media (max-width: 1024px) {
        .stats-container { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stats-container { grid-template-columns: 1fr; }
        .top-bar { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .filter-card { padding: 15px; }
    }
</style>
@endpush

@section('content')

<div class="top-bar" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title" style="margin: 0;"><i class="fas fa-file-alt text-blue-400"></i> {{ __('Attendance & Lateness Report') }}</h1>
</div>

<!-- Filters -->
<div class="filter-card glass-card">
    <form id="reportFilterForm" action="/reports/absence" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: flex-end;">
        <div style="grid-column: span 2;">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Search Employee') }}</label>
            <input id="autoSearchInput" type="text" name="search" class="glass-input" style="width:100%;" value="{{ request('search') }}" placeholder="{{ __('Search by name or ID...') }}">
        </div>

        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Select Period Mode') }}</label>
            <select name="mode" id="report-mode" class="glass-input" style="width:100%;" onchange="toggleInputs(this.value)">
                <option value="day" {{ $mode == 'day' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                <option value="month" {{ $mode == 'month' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                <option value="year" {{ $mode == 'year' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                <option value="range" {{ $mode == 'range' ? 'selected' : '' }}>{{ __('Date Range') }}</option>
            </select>
        </div>

        <div id="group-day" style="display: {{ $mode == 'day' ? 'block' : 'none' }};">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Select Date') }}</label>
            <input type="date" name="date" class="glass-input" style="width:100%;" value="{{ $date }}">
        </div>

        <div id="group-month" style="display: {{ $mode == 'month' ? 'block' : 'none' }};">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Select Month') }}</label>
            <input type="month" name="month" class="glass-input" style="width:100%;" value="{{ $month }}">
        </div>

        <div id="group-year" style="display: {{ $mode == 'year' ? 'block' : 'none' }};">
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Select Year') }}</label>
            <input type="number" name="year" class="glass-input" style="width:100%;" value="{{ $year }}" min="2000" max="2100">
        </div>

        <div id="group-range" style="display: {{ $mode == 'range' ? 'flex' : 'none' }}; gap: 10px; align-items: center; grid-column: span 2;">
            <div style="flex:1;">
                <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('From Date') }}</label>
                <input type="date" name="start_date" class="glass-input" style="width:100%;" value="{{ $startDateParam }}">
            </div>
            <span style="color:var(--text-muted); padding-top:25px;">{{ __('to') }}</span>
            <div style="flex:1;">
                <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('To Date') }}</label>
                <input type="date" name="end_date" class="glass-input" style="width:100%;" value="{{ $endDateParam }}">
            </div>
        </div>

        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Default Start Time') }}</label>
            <input type="time" name="start_time" class="glass-input" style="width:100%;" value="{{ $defaultStartTime }}">
        </div>

        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Default End Time') }}</label>
            <input type="time" name="end_time" class="glass-input" style="width:100%;" value="{{ $defaultEndTime }}">
        </div>

        <div>
            <label style="display:block; margin-bottom:8px; color:var(--text-muted); font-size:0.9rem;">{{ __('Employee (Dropdown)') }}</label>
            <select name="employee_id" class="glass-input" style="width:100%;">
                <option value="">{{ __('All Employees') }}</option>
                @foreach($allEmployees as $emp)
                    <option value="{{ $emp->user_id }}" {{ ($employeeId ?? '') == $emp->user_id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->user_id }})</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 10px; align-items: flex-end;">
            <button type="submit" class="glass-btn" style="flex: 1; justify-content: center;">
                <i class="fas fa-filter"></i> {{ __('Apply Filters') }}
            </button>
            
            <div style="position: relative; flex: 1;">
                <button type="button" class="glass-btn" style="width: 100%; justify-content: center; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); box-shadow: none;" onclick="document.getElementById('exportMenu').classList.toggle('show')">
                    <i class="fas fa-download"></i> {{ __('Export') }} <i class="fas fa-chevron-down" style="font-size: 0.8em; margin-left: 5px;"></i>
                </button>
                <div id="exportMenu" class="dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--card-bg); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; margin-top: 5px; z-index: 100; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);">
                    <a href="{{ route('reports.absence.pdf', request()->all()) }}" target="_blank" style="display: block; padding: 10px 15px; color: white; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <i class="fas fa-file-pdf" style="color: #f43f5e; width: 20px;"></i> {{ __('Export PDF') }}
                    </a>
                    <a href="{{ route('reports.absence.excel', request()->all()) }}" style="display: block; padding: 10px 15px; color: white; text-decoration: none;">
                        <i class="fas fa-file-excel" style="color: #10b981; width: 20px;"></i> {{ __('Export Excel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- KPI Row -->
<div class="stats-container">
    <div class="kpi-card glass-card" style="border-left: 4px solid #10b981;">
        <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="kpi-info">
            <p>{{ __('Attendance Rate') }}</p>
            <h3 style="color: #10b981;">{{ $attendanceRate }}%</h3>
            <p style="font-size: 0.75rem;">{{ $totalPresentDays }} / {{ $totalWorkingDays }} {{ __('Work Days') }}</p>
        </div>
    </div>
    
    <div class="kpi-card glass-card" style="border-left: 4px solid #6366f1;">
        <div class="kpi-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="kpi-info">
            <p>{{ __('Present') }}</p>
            <h3>{{ $totalPresentDays }} <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted)">{{ __('Days') }}</span></h3>
            <p style="font-size: 0.75rem;">{{ __('Worked Hrs') }}: {{ number_format($totalWorkedHours, 1) }} {{ __('hrs') }}</p>
        </div>
    </div>
    
    <div class="kpi-card glass-card" style="border-left: 4px solid #64748b;">
        <div class="kpi-icon" style="background: rgba(100, 116, 139, 0.1); color: #94a3b8;">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="kpi-info">
            <p>{{ __('Absent') }}</p>
            <h3>{{ $totalAbsentDays }} <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted)">{{ __('Days') }}</span></h3>
        </div>
    </div>
    
    <div class="kpi-card glass-card" style="border-left: 4px solid #f43f5e;">
        <div class="kpi-icon" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="kpi-info">
            <p>{{ __('Late / Tardy') }}</p>
            <h3 style="color: #f43f5e;">{{ $totalLateDays }} <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted)">{{ __('Days') }}</span></h3>
            <p style="font-size: 0.75rem; color:#f43f5e;">{{ __('Total Delay') }}: {{ $totalDelayMinutes }} {{ __('mins') }}</p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container glass-card" style="overflow-x: auto;">
    <table style="min-width: 900px;">
        <thead>
            @if($mode === 'day')
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('First Punch') }}</th>
                    <th>{{ __('Last Punch') }}</th>
                    <th>{{ __('Expected') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Delay (Mins)') }}</th>
                    <th>{{ __('Worked Hrs') }}</th>
                    <th style="width: 80px;">{{ __('Action') }}</th>
                </tr>
            @else
                <tr>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Work Days') }}</th>
                    <th>{{ __('Present') }}</th>
                    <th>{{ __('Absent') }}</th>
                    <th>{{ __('Late / Tardy') }}</th>
                    <th>{{ __('Total Delay') }}</th>
                    <th>{{ __('Worked Hrs') }}</th>
                    <th style="width: 80px;">{{ __('Action') }}</th>
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
                                <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> {{ __('Late') }}</span>
                            @elseif($row['status'] == 'On Time')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> {{ __('On Time') }}</span>
                            @elseif($row['status'] == 'Absent')
                                <span class="badge badge-secondary"><i class="fas fa-folder-open"></i> {{ __('Absent') }}</span>
                            @elseif($row['status'] == 'Invalid Punch')
                                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> {{ __('Invalid Punch') }}</span>
                            @else
                                <span class="badge" style="background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4);"><i class="fas fa-bed"></i> {{ __('Off Day') }}</span>
                            @endif
                        </td>
                        <td style="{{ $row['status'] == 'Late' ? 'color:#f43f5e; font-weight:bold;' : '' }}">{{ $row['delay'] }}</td>
                        <td>{{ $row['worked'] }}</td>
                        <td>
                            <button class="glass-btn" style="padding: 6px 12px; font-size: 0.8rem;" onclick="toggleDetails({{ $index }})">
                                <i class="fas fa-list"></i> {{ __('Details') }}
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
                        <td style="{{ $row['totalDelayMins'] > 0 ? 'color:#f43f5e; font-weight:bold;' : '' }}">{{ $row['totalDelayMins'] }} {{ __('mins') }}</td>
                        <td>{{ number_format($row['totalWorkedHrs'], 1) }} {{ __('hrs') }}</td>
                        <td>
                            <button class="glass-btn" style="padding: 6px 12px; font-size: 0.8rem;" onclick="toggleDetails({{ $index }})">
                                <i class="fas fa-list"></i> {{ __('Details') }}
                            </button>
                        </td>
                    </tr>
                @endif
                
                <!-- Drawer Row -->
                <tr class="details-row" id="details-{{ $index }}">
                    <td colspan="8" style="padding: 0;">
                        <div style="padding: 20px; background: rgba(0,0,0,0.2);">
                            <h6 style="margin-top: 0; color: var(--accent); margin-bottom: 15px;"><i class="fas fa-calendar-alt"></i> {{ __('Daily Attendance Log for') }} {{ $row['name'] }}</h6>
                            <table class="details-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('First Punch') }}</th>
                                        <th>{{ __('Last Punch') }}</th>
                                        <th>{{ __('Shift Expected') }}</th>
                                        <th>{{ __('Delay') }}</th>
                                        <th>{{ __('Work Hrs') }}</th>
                                        <th>{{ __('Status') }}</th>
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
                                                    <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i> {{ __('Late') }}</span>
                                                @elseif($daily['status'] == 'On Time')
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> {{ __('On Time') }}</span>
                                                @elseif($daily['status'] == 'Absent')
                                                    <span style="color:#94a3b8;"><i class="fas fa-circle" style="font-size:8px;"></i> {{ __('Absent') }}</span>
                                                @elseif($daily['status'] == 'Invalid Punch')
                                                    <span style="color:#fbbf24;"><i class="fas fa-circle" style="font-size:8px;"></i> {{ __('Invalid Punch') }}</span>
                                                @else
                                                    <span style="color:#38bdf8;"><i class="fas fa-circle" style="font-size:8px;"></i> {{ __('Off Day') }}</span>
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
                        <p style="font-size: 1.1rem;">{{ __('No records found.') }}</p>
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
        if(mode === 'range') document.getElementById('group-range').style.display = 'flex';
    }

    function toggleDetails(index) {
        const row = document.getElementById('details-' + index);
        if (row.classList.contains('active')) {
            row.classList.remove('active');
        } else {
            row.classList.add('active');
        }
    }
    
    // Auto Search Script
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('autoSearchInput');
        const searchForm = document.getElementById('reportFilterForm');
        let debounceTimer;

        if (searchInput) {
            const urlParams = new URLSearchParams(window.location.search);
            if (searchInput.value || urlParams.has('search')) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchForm.submit();
                }, 800); // 800ms for reports as they are heavy
            });
        }
    });

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

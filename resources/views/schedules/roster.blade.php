@extends('layouts.app')

@push('styles')
<style>
    .split-layout {
        display: flex;
        gap: 30px;
        height: calc(100vh - 150px);
    }
    
    /* Left Panel: Employee List */
    .employee-panel {
        width: 320px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .panel-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        background: rgba(0,0,0,0.2);
    }
    .employee-list {
        flex-grow: 1;
        overflow-y: auto;
    }
    .emp-item {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: 0.2s;
    }
    .emp-item:hover { background: rgba(255,255,255,0.05); }
    .emp-item.active {
        background: rgba(59, 130, 246, 0.1);
        border-left: 4px solid var(--accent);
    }
    .emp-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
    }
    
    /* Right Panel: Calendar */
    .calendar-panel {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .calendar-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0,0,0,0.2);
    }
    .calendar-grid {
        flex-grow: 1;
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        grid-template-rows: 40px repeat(5, 1fr); /* 1 row for days, 5/6 for weeks */
        background: var(--border-color); /* Acts as border between cells */
        gap: 1px;
    }
    .cal-day-header {
        background: rgba(0,0,0,0.4);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .cal-cell {
        background: var(--card-bg);
        padding: 10px;
        display: flex;
        flex-direction: column;
        transition: 0.2s;
    }
    .cal-cell:hover { background: rgba(255,255,255,0.02); }
    .cal-cell.empty { background: rgba(0,0,0,0.1); }
    
    .date-number {
        font-size: 1.1rem;
        font-weight: bold;
        color: var(--text-muted);
        margin-bottom: 5px;
    }
    .date-number.today { color: var(--accent); }
    
    .shift-badge {
        flex-grow: 1;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.85rem;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
        padding: 5px;
    }
    .shift-badge:hover { transform: scale(1.02); filter: brightness(1.2); }
    .shift-assigned { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); }
    .shift-off { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
    .shift-unassigned { background: transparent; border: 1px dashed var(--border-color); color: var(--text-muted); cursor: pointer; }
    .shift-unassigned:hover { background: rgba(255,255,255,0.05); }

    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6);  z-index: 9999; align-items: center; justify-content: center; }
    .modal-content { padding: 30px; width: 400px; max-width: 90%; }
    .form-control { width: 100%; padding: 10px; background: rgba(0,0,0,0.2); color: white; border-radius: 6px; border: 1px solid var(--border-color); margin-bottom: 15px;}

    @media (max-width: 768px) {
        .split-layout { flex-direction: column; height: auto; gap: 15px; }
        .employee-panel { width: 100%; max-height: 300px; }
        .calendar-grid { grid-template-columns: repeat(7, minmax(40px, 1fr)); overflow-x: auto; }
        .calendar-panel { overflow-x: auto; }
        .top-bar form { flex-direction: column; width: 100%; align-items: stretch !important; }
        .top-bar form select, .top-bar form a { width: 100% !important; text-align: center; justify-content: center; }
        .top-bar { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .calendar-header { flex-direction: column; gap: 10px; text-align: center; }
    }
</style>
@endpush

@section('content')

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #34d399; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #f87171; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

<div class="top-bar" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <h1 class="page-title"><i class="fas fa-calendar-days text-blue-400"></i> Monthly Roster</h1>
    
    <form action="/roster" method="GET" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
        <select name="month" class="form-control" style="width: auto; margin: 0; padding: 6px 12px;">
            @for($m=1; $m<=12; $m++)
                <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                </option>
            @endfor
        </select>
        <select name="year" class="form-control" style="width: auto; margin: 0; padding: 6px 12px;">
            @for($y=date('Y')-1; $y<=date('Y')+2; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <a href="/schedules" class="glass-btn" style="background: rgba(255,255,255,0.05); box-shadow: none;">
        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
    </a>
</div>

<div class="split-layout">
    <!-- Left Panel -->
    <div class="employee-panel glass-card" style="padding: 0;">
        <div class="panel-header">
            <h3 style="color:white; margin:0;"><i class="fas fa-users text-blue-400"></i> Select Employee</h3>
            <p style="color:var(--text-muted); font-size:0.85rem; margin-top:5px; margin-bottom:15px;">Choose an employee to view their specific month.</p>
            <input type="text" id="employeeSearch" placeholder="Search by name or PIN..." style="width: 100%; padding: 10px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(0,0,0,0.2); color: white; outline: none;">
        </div>
        <div class="employee-list" id="employeeList">
            @foreach($employees as $emp)
                <a href="/roster?user_id={{ $emp->id }}&month={{ $month }}&year={{ $year }}" class="emp-item {{ request('user_id') == $emp->id ? 'active' : '' }}" data-search="{{ strtolower($emp->name . ' ' . $emp->user_id) }}">
                    <div class="emp-avatar">
                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 1rem;">{{ $emp->name }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">PIN: {{ $emp->user_id }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Right Panel: Calendar Grid -->
    <div class="calendar-panel">
        @if($selectedUser)
            <div class="calendar-header">
                <div>
                    <h2 style="color: white; margin: 0; font-size: 1.5rem;">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h2>
                    <p style="color: var(--text-muted); margin-top: 5px; margin-bottom: 0;">
                        Schedule for <strong style="color:white;">{{ $selectedUser->name }}</strong>
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <form action="/roster/generate" method="POST" onsubmit="return confirm('This will automatically fill the entire month based on {{ $selectedUser->name }}\'s assigned work pattern. Proceed?');">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="glass-btn" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); box-shadow: none;">
                            <i class="fas fa-magic"></i> Auto-Generate Month
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="calendar-grid">
                <!-- Day Headers -->
                <div class="cal-day-header">Sun</div>
                <div class="cal-day-header">Mon</div>
                <div class="cal-day-header">Tue</div>
                <div class="cal-day-header">Wed</div>
                <div class="cal-day-header">Thu</div>
                <div class="cal-day-header">Fri</div>
                <div class="cal-day-header">Sat</div>
                
                @php
                    $firstDayOfMonth = date('w', mktime(0, 0, 0, $month, 1, $year));
                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $totalCells = 35; // 5 rows * 7 cols
                    if ($firstDayOfMonth + $daysInMonth > 35) { $totalCells = 42; } // 6 rows if needed
                @endphp
                
                @for($i = 0; $i < $totalCells; $i++)
                    @if($i < $firstDayOfMonth || $i >= $firstDayOfMonth + $daysInMonth)
                        <div class="cal-cell empty"></div>
                    @else
                        @php
                            $d = $i - $firstDayOfMonth + 1;
                            $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $d);
                            $sched = $roster[$dateStr] ?? null;
                        @endphp
                        <div class="cal-cell">
                            <div class="date-number {{ $dateStr == date('Y-m-d') ? 'today' : '' }}">{{ $d }}</div>
                            @if($sched)
                                @if($sched->is_off_day)
                                    <div class="shift-badge shift-off" onclick="openEditDay('{{ $dateStr }}', 'OFF')" title="Click to edit">OFF</div>
                                @else
                                    @php $sName = $shifts->firstWhere('id', $sched->shift_id)?->name ?? 'Shift'; @endphp
                                    <div class="shift-badge shift-assigned" onclick="openEditDay('{{ $dateStr }}', '{{ $sched->shift_id }}')" title="Click to edit">
                                        {{ $sName }}
                                    </div>
                                @endif
                            @else
                                <div class="shift-badge shift-unassigned" onclick="openEditDay('{{ $dateStr }}', '')" title="Click to assign">
                                    <i class="fas fa-plus"></i>
                                </div>
                            @endif
                        </div>
                    @endif
                @endfor
            </div>
        @else
            <div style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted);">
                <i class="fas fa-calendar-alt" style="font-size: 4rem; opacity: 0.2; margin-bottom: 20px;"></i>
                <h2>Select an Employee</h2>
                <p>Choose an employee from the list on the left to view and manage their monthly schedule.</p>
            </div>
        @endif
    </div>
</div>

<!-- Edit Day Modal -->
@if($selectedUser)
<div id="editDayModal" class="modal-overlay">
    <div class="modal-content glass-card">
        <h3 style="color: white; margin-top: 0;"><i class="fas fa-edit text-blue-400"></i> Change Schedule</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;" id="modalDesc"></p>
        
        <form action="/roster/update-day" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $selectedUser->user_id }}">
            <input type="hidden" name="date" id="edit_date">
            
            <label style="color: white; display: block; margin-bottom: 5px;">Select Shift for this day:</label>
            <select name="shift_id" id="edit_shift_id" class="glass-input" style="width: 100%;">
                <option value="" style="color: #ef4444;">-- OFF DAY --</option>
                @foreach($shifts as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->start_time }} - {{ $s->end_time }})</option>
                @endforeach
            </select>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 15px;">
                <button type="button" class="glass-btn" style="background: transparent; box-shadow: none; border: 1px solid var(--glass-border);" onclick="document.getElementById('editDayModal').style.display='none'">Cancel</button>
                <button type="submit" class="glass-btn">Save Change</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.getElementById('employeeSearch').addEventListener('input', function(e) {
        let searchTerm = e.target.value.toLowerCase();
        let items = document.querySelectorAll('#employeeList .emp-item');
        
        items.forEach(function(item) {
            let searchData = item.getAttribute('data-search');
            if (searchData.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
<script>
    function openEditDay(date, currentShift) {
        document.getElementById('edit_date').value = date;
        document.getElementById('modalDesc').innerText = "Changing schedule for " + date;
        
        const shiftSelect = document.getElementById('edit_shift_id');
        if (currentShift === 'OFF' || currentShift === '') {
            shiftSelect.value = "";
        } else {
            shiftSelect.value = currentShift;
        }
        
        document.getElementById('editDayModal').style.display = 'flex';
    }
</script>
@endpush

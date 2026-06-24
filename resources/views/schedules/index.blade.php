@extends('layouts.app')

@push('styles')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media (max-width: 1024px) {
        .grid-container { grid-template-columns: 1fr; }
    }
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    .card-title { font-size: 1.2rem; font-weight: 600; color: white; display: flex; align-items: center; gap: 10px;}
    .list-item {
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: 0.2s;
    }
    .list-item:hover {
        border-color: rgba(255,255,255,0.2);
        background: rgba(0,0,0,0.3);
    }
    .item-info h4 { margin: 0 0 5px 0; color: white; }
    .item-info p { margin: 0; color: var(--text-muted); font-size: 0.9rem; }
    
    .btn { padding: 8px 15px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.9rem; }
    .btn-primary { background: var(--accent); color: white; }
    .btn-outline { background: transparent; color: white; border: 1px solid var(--border-color); }
    .btn-outline:hover { background: rgba(255,255,255,0.05); }
    .action-btn { width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: white; border: none; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    .btn-blue { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
    .btn-blue:hover { background: rgba(59, 130, 246, 0.4); }
    .btn-red { background: rgba(239, 68, 68, 0.2); color: #f87171; }
    .btn-red:hover { background: rgba(239, 68, 68, 0.4); }
    .btn-green { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .btn-green:hover { background: rgba(16, 185, 129, 0.4); }
    .btn-yellow { background: rgba(234, 179, 8, 0.2); color: #facc15; }
    .btn-yellow:hover { background: rgba(234, 179, 8, 0.4); }

    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; }
    .modal-content { background: var(--sidebar-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); width: 450px; max-width: 90%; }
    .modal-content h3 { margin-top: 0; color: white; margin-bottom: 20px;}
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; color: var(--text-muted); font-size: 0.9rem; }
    .form-control { width: 100%; padding: 10px; background: rgba(0,0,0,0.2); color: white; border-radius: 6px; border: 1px solid var(--border-color); }
    
    .day-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .day-row:last-child { border-bottom: none; }
    .day-name { width: 100px; color: white; font-weight: 500; }
</style>
@endpush

@section('content')

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #34d399; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="top-bar" style="margin-bottom: 30px;">
    <h1 class="page-title"><i class="fas fa-calendar-alt text-blue-400"></i> {{ __('Schedules & Shifts') }}</h1>
</div>

<div class="grid-container">
    <!-- SHIFTS SECTION -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-clock text-blue-400"></i> {{ __('Time Shifts') }}</div>
            <button class="btn btn-primary" onclick="document.getElementById('addShiftModal').style.display='flex'">
                <i class="fas fa-plus"></i> {{ __('Add Shift') }}
            </button>
        </div>
        
        <div>
            @forelse($shifts as $shift)
            <div class="list-item">
                <div class="item-info">
                    <h4>{{ $shift->name }}</h4>
                    <p><i class="fas fa-hourglass-start"></i> {{ $shift->start_time }} &nbsp;&nbsp; <i class="fas fa-hourglass-end"></i> {{ $shift->end_time }}</p>
                </div>
                <div style="display:flex; gap: 8px;">
                    <button class="action-btn btn-blue" onclick="openEditShift({{ $shift->id }}, '{{ addslashes($shift->name) }}', '{{ $shift->start_time }}', '{{ $shift->end_time }}')" title="Edit Shift"><i class="fas fa-edit"></i></button>
                    <form action="/schedules/shifts/{{ $shift->id }}/delete" method="POST" onsubmit="return confirm('Delete this shift?');">
                        @csrf
                        <button type="submit" class="action-btn btn-red" title="Delete Shift"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding: 20px; color: var(--text-muted);">No shifts defined.</div>
            @endforelse
        </div>
    </div>

    <!-- WORK PATTERNS SECTION -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-layer-group text-blue-400"></i> {{ __('Work Patterns') }}</div>
            <button class="btn btn-primary" onclick="document.getElementById('addPatternModal').style.display='flex'">
                <i class="fas fa-plus"></i> {{ __('Add Pattern') }}
            </button>
        </div>

        <div>
            @forelse($workPatterns as $pattern)
            <div class="list-item">
                <div class="item-info">
                    <h4>{{ $pattern->name }}</h4>
                    <p>{{ $pattern->days->where('shift_id', '!=', null)->count() }} Work Days Configured</p>
                </div>
                <div style="display:flex; gap: 8px;">
                    <button class="action-btn btn-yellow" onclick="document.getElementById('assignPatternModal{{ $pattern->id }}').style.display='flex'" title="Assign to Users"><i class="fas fa-users"></i></button>
                    <button class="action-btn btn-green" onclick="document.getElementById('configPatternModal{{ $pattern->id }}').style.display='flex'" title="Configure Days"><i class="fas fa-calendar-week"></i></button>
                    <button class="action-btn btn-blue" onclick="openEditPattern({{ $pattern->id }}, '{{ addslashes($pattern->name) }}')" title="Rename Pattern"><i class="fas fa-edit"></i></button>
                    <form action="/schedules/patterns/{{ $pattern->id }}/delete" method="POST" onsubmit="return confirm('Delete this work pattern?');">
                        @csrf
                        <button type="submit" class="action-btn btn-red" title="Delete Pattern"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>

            <!-- Configure Pattern Days Modal -->
            <div id="configPatternModal{{ $pattern->id }}" class="modal-overlay">
                <div class="modal-content" style="width: 500px;">
                    <h3><i class="fas fa-calendar-week text-blue-400"></i> Configure: {{ $pattern->name }}</h3>
                    <form action="/schedules/patterns/{{ $pattern->id }}/days" method="POST">
                        @csrf
                        @php
                            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        
                        @for($i = 0; $i <= 6; $i++)
                            @php
                                $currentShiftId = $pattern->days->where('day_of_week', $i)->first()?->shift_id;
                            @endphp
                            <div class="day-row">
                                <div class="day-name">{{ __($daysOfWeek[$i]) }}</div>
                                <select name="days[{{ $i }}]" class="form-control" style="width: 70%;">
                                    <option value="" style="color: #ef4444;">-- {{ __('Off Day') }} --</option>
                                    @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" {{ $currentShiftId == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->start_time }} - {{ $s->end_time }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endfor

                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 20px;">
                            <button type="button" class="btn btn-outline" onclick="document.getElementById('configPatternModal{{ $pattern->id }}').style.display='none'">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save Configuration') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assign Pattern Modal -->
            <div id="assignPatternModal{{ $pattern->id }}" class="modal-overlay">
                <div class="modal-content" style="width: 500px;">
                    <h3><i class="fas fa-users text-blue-400"></i> {{ __('Assign:') }} {{ $pattern->name }}</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">{{ __('Select the employees who should follow this work pattern. Unchecking an employee will remove this pattern from them.') }}</p>
                    <form action="/schedules/patterns/{{ $pattern->id }}/assign" method="POST">
                        @csrf
                        <div style="max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 20px;">
                            @foreach($employees as $emp)
                                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer; color: white;">
                                    <input type="checkbox" name="user_ids[]" value="{{ $emp->id }}" {{ $emp->work_pattern_id == $pattern->id ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--accent);">
                                    {{ $emp->name }} <span style="color: var(--text-muted); font-size: 0.85rem;">(PIN: {{ $emp->user_id }})</span>
                                </label>
                            @endforeach
                            @if($employees->isEmpty())
                                <div style="color: var(--text-muted); text-align: center;">No employees found.</div>
                            @endif
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px;">
                            <button type="button" class="btn btn-outline" onclick="document.getElementById('assignPatternModal{{ $pattern->id }}').style.display='none'">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save Assignments') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            @empty
            <div style="text-align:center; padding: 20px; color: var(--text-muted);">No work patterns defined.</div>
            @endforelse
        </div>
    </div>
</div>

<div style="margin-top: 20px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-calendar-days text-blue-400"></i> {{ __('Monthly Work Patterns (31 Days)') }}</div>
            <button class="btn btn-primary" onclick="document.getElementById('addMonthlyPatternModal').style.display='flex'">
                <i class="fas fa-plus"></i> {{ __('Add Monthly Pattern') }}
            </button>
        </div>
        
        <div class="grid-container">
            @forelse($monthlyPatterns as $pattern)
            <div class="list-item">
                <div class="item-info">
                    <h4>{{ $pattern->name }}</h4>
                    <p>{{ $pattern->days->count() }} days configured</p>
                </div>
                <div style="display:flex; gap: 5px;">
                    <button class="action-btn btn-blue" onclick="document.getElementById('configMonthlyPatternModal{{ $pattern->id }}').style.display='flex'" title="Configure 31 Days">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button class="action-btn btn-yellow" onclick="document.getElementById('assignMonthlyPatternModal{{ $pattern->id }}').style.display='flex'" title="Assign to Users">
                        <i class="fas fa-users"></i>
                    </button>
                    <button class="action-btn btn-green" onclick="openEditMonthlyPattern({{ $pattern->id }}, '{{ addslashes($pattern->name) }}')" title="Edit Pattern Name">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="/schedules/monthly-patterns/{{ $pattern->id }}/delete" method="POST" onsubmit="return confirm('Delete this monthly pattern?');" style="display:inline;">
                        @csrf
                        <button type="submit" class="action-btn btn-red" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>

            <!-- Configure 31 Days Modal -->
            <div id="configMonthlyPatternModal{{ $pattern->id }}" class="modal-overlay">
                <div class="modal-content" style="width: 800px;">
                    <h3><i class="fas fa-cog text-blue-400"></i> Configure: {{ $pattern->name }} (31 Days)</h3>
                    <form action="/schedules/monthly-patterns/{{ $pattern->id }}/days" method="POST">
                        @csrf
                        <div style="max-height: 400px; overflow-y: auto; padding-right: 10px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                            @for($i = 1; $i <= 31; $i++)
                                @php
                                    $currentShift = $pattern->days->where('day_of_month', $i)->first()?->shift_id;
                                @endphp
                                <div style="background: rgba(0,0,0,0.2); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <label style="color:white; font-size: 0.85rem; margin-bottom: 5px; display: block;">Day {{ $i }}</label>
                                    <select name="days[{{ $i }}]" style="width: 100%; padding: 5px; background: rgba(0,0,0,0.5); color: white; border: 1px solid var(--border-color); border-radius: 4px; font-size: 0.8rem;">
                                        <option value="" style="color:#ef4444;">-- OFF DAY --</option>
                                        @foreach($shifts as $s)
                                            <option value="{{ $s->id }}" {{ $currentShift == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endfor
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 20px;">
                            <button type="button" class="btn btn-outline" onclick="document.getElementById('configMonthlyPatternModal{{ $pattern->id }}').style.display='none'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Configuration</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assign Monthly Pattern Modal -->
            <div id="assignMonthlyPatternModal{{ $pattern->id }}" class="modal-overlay">
                <div class="modal-content" style="width: 500px;">
                    <h3><i class="fas fa-users text-blue-400"></i> Assign: {{ $pattern->name }}</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Select the employees who should follow this monthly pattern.</p>
                    <form action="/schedules/monthly-patterns/{{ $pattern->id }}/assign" method="POST">
                        @csrf
                        <div style="max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 20px;">
                            @foreach($employees as $emp)
                                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer; color: white;">
                                    <input type="checkbox" name="user_ids[]" value="{{ $emp->id }}" {{ $emp->monthly_pattern_id == $pattern->id ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--accent);">
                                    {{ $emp->name }} <span style="color: var(--text-muted); font-size: 0.85rem;">(PIN: {{ $emp->user_id }})</span>
                                </label>
                            @endforeach
                            @if($employees->isEmpty())
                                <div style="color: var(--text-muted); text-align: center;">No employees found.</div>
                            @endif
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px;">
                            <button type="button" class="btn btn-outline" onclick="document.getElementById('assignMonthlyPatternModal{{ $pattern->id }}').style.display='none'">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Assignments</button>
                        </div>
                    </form>
                </div>
            </div>

            @empty
            <div style="text-align:center; padding: 20px; color: var(--text-muted); grid-column: 1 / -1;">No monthly patterns defined.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modals for Creation/Editing -->

<!-- Add Shift Modal -->
<div id="addShiftModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-plus text-blue-400"></i> Add New Shift</h3>
        <form action="/schedules/shifts" method="POST">
            @csrf
            <div class="form-group">
                <label>Shift Name (e.g. Morning Shift)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="grid-container" style="gap: 15px;">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addShiftModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Shift</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Shift Modal -->
<div id="editShiftModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-edit text-blue-400"></i> Edit Shift</h3>
        <form id="editShiftForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Shift Name</label>
                <input type="text" id="edit_shift_name" name="name" class="form-control" required>
            </div>
            <div class="grid-container" style="gap: 15px;">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" id="edit_shift_start" name="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" id="edit_shift_end" name="end_time" class="form-control" required>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editShiftModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Shift</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Pattern Modal -->
<div id="addPatternModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-plus text-blue-400"></i> Add Work Pattern</h3>
        <form action="/schedules/patterns" method="POST">
            @csrf
            <div class="form-group">
                <label>Pattern Name (e.g. Standard 5-Days)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addPatternModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Pattern</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Pattern Modal -->
<div id="editPatternModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-edit text-blue-400"></i> Rename Pattern</h3>
        <form id="editPatternForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Pattern Name</label>
                <input type="text" id="edit_pattern_name" name="name" class="form-control" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editPatternModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Pattern</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Monthly Pattern Modal -->
<div id="addMonthlyPatternModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-plus text-blue-400"></i> Add Monthly Pattern</h3>
        <form action="/schedules/monthly-patterns" method="POST">
            @csrf
            <div class="form-group">
                <label>Pattern Name (e.g. Ramadan Schedule)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addMonthlyPatternModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Monthly Pattern</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Monthly Pattern Modal -->
<div id="editMonthlyPatternModal" class="modal-overlay">
    <div class="modal-content">
        <h3><i class="fas fa-edit text-blue-400"></i> Rename Monthly Pattern</h3>
        <form id="editMonthlyPatternForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Pattern Name</label>
                <input type="text" id="edit_monthly_pattern_name" name="name" class="form-control" required>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editMonthlyPatternModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Pattern</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openEditShift(id, name, start, end) {
        document.getElementById('editShiftForm').action = '/schedules/shifts/' + id;
        document.getElementById('edit_shift_name').value = name;
        document.getElementById('edit_shift_start').value = start;
        document.getElementById('edit_shift_end').value = end;
        document.getElementById('editShiftModal').style.display = 'flex';
    }

    function openEditPattern(id, name) {
        document.getElementById('editPatternForm').action = '/schedules/patterns/' + id;
        document.getElementById('edit_pattern_name').value = name;
        document.getElementById('editPatternModal').style.display = 'flex';
    }

    function openEditMonthlyPattern(id, name) {
        document.getElementById('editMonthlyPatternForm').action = '/schedules/monthly-patterns/' + id;
        document.getElementById('edit_monthly_pattern_name').value = name;
        document.getElementById('editMonthlyPatternModal').style.display = 'flex';
    }
</script>
@endpush

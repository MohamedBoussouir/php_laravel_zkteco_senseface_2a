@extends('layouts.app')

@push('styles')
<style>
    .card {
        background-color: var(--card-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .section-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: white;
    }
    .btn {
        padding: 8px 15px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        transition: 0.2s;
    }
    .btn-secondary {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-secondary:hover { background: rgba(255,255,255,0.15); }
    
    .search-filter-bar {
        margin-bottom: 15px;
        display: flex;
        gap: 15px;
        align-items: center;
        background: rgba(0, 0, 0, 0.15);
        padding: 15px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.05);
        flex-wrap: wrap;
    }
    
    .search-box {
        position: relative;
        flex: 1;
        min-width: 250px;
    }
    .search-box input {
        background: rgba(0,0,0,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.05);
        padding: 10px 15px 10px 40px;
        border-radius: 10px;
        font-size: 0.9rem;
        width: 100%;
        outline: none;
        transition: border-color 0.3s;
    }
    .search-box input:focus { border-color: var(--accent); }
    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .table-container {
        background: var(--bg-color);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px 20px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
    th { background: rgba(0,0,0,0.2); color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    td { color: var(--text-main); font-size: 0.95rem; }
    tr:hover td { background: rgba(255,255,255,0.02); }
    tr:last-child td { border-bottom: none; }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        display: inline-block;
        letter-spacing: 0.5px;
    }
    .badge-checkin { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
    .badge-checkout { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
    .badge-break { background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.4); }
    .badge-overtime { background: rgba(168, 85, 247, 0.2); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
    .badge-unknown { background: rgba(255, 255, 255, 0.1); color: var(--text-muted); border: 1px solid var(--border-color); }
    /* Pagination Styles - Connected Block Style */
    nav { display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 20px; }
    
    /* Hide Laravel's mobile pagination and 'Showing...' text */
    nav > div:first-child, nav p, nav .text-sm, nav > div > div:first-child { display: none !important; }
    
    /* Show only the desktop pagination container */
    nav > div:last-child { display: flex; justify-content: center; width: 100%; }
    nav > div > div:last-child { display: flex; justify-content: center; width: 100%; }
    
    .pagination, nav span.relative.z-0.inline-flex { 
        display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 0; 
        background: transparent; 
        border-radius: 6px; 
        border: 1px solid var(--border-color); 
        overflow: hidden;
    }
    
    .pagination li, nav span.relative.z-0.inline-flex > span, nav span.relative.z-0.inline-flex > a { 
        display: inline-flex; 
        border-right: 1px solid var(--border-color);
    }
    
    /* Remove right border from last item */
    .pagination li:last-child, nav span.relative.z-0.inline-flex > *:last-child {
        border-right: none;
    }
    
    .pagination li a, .pagination li span, nav span.relative.z-0.inline-flex > span > span, nav span.relative.z-0.inline-flex > a { 
        padding: 8px 16px; 
        background: rgba(255, 255, 255, 0.03); 
        color: #cbd5e1; 
        text-decoration: none; 
        font-size: 0.95rem; 
        font-weight: 500; 
        transition: background 0.2s; 
        display: flex; align-items: center; justify-content: center; min-width: 40px; 
        border: none; border-radius: 0;
    }
    
    .pagination li a:hover, nav span.relative.z-0.inline-flex > a:hover { 
        background: rgba(255, 255, 255, 0.08); 
        color: white; 
        text-decoration: none; 
    }
    
    .pagination li.active span, nav span.relative.z-0.inline-flex > span[aria-current="page"] > span { 
        background: #3b82f6; 
        color: white; 
        border: none;
        font-weight: bold;
    }
    
    .pagination li.disabled span, nav span.relative.z-0.inline-flex > span[aria-disabled="true"] > span { 
        background: rgba(0, 0, 0, 0.2);
        opacity: 0.5; 
        cursor: not-allowed; 
    }
</style>
@endpush

@section('content')

<div class="card">
    <div class="section-header">
        <h2><i class="fas fa-history text-blue-400"></i> Attendance Records</h2>
        <div style="display: flex; gap: 10px;">
            <form action="/attendance/fetch" method="POST" onsubmit="return confirm('This will send a command to all connected devices to fetch new logs. Proceed?');" style="margin: 0;">
                @csrf
                <button type="submit" class="btn" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3);">
                    <i class="fas fa-bolt"></i> Fast Sync Logs
                </button>
            </form>
        </div>
    </div>
    
    <form action="/attendance" method="GET">
        <div class="search-filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or ID...">
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">From:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" style="background: rgba(0,0,0,0.2); color: white; border: 1px solid rgba(255,255,255,0.05); padding: 8px 12px; border-radius: 10px; font-size: 0.9rem; outline: none; cursor: pointer;">
                
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">To:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" style="background: rgba(0,0,0,0.2); color: white; border: 1px solid rgba(255,255,255,0.05); padding: 8px 12px; border-radius: 10px; font-size: 0.9rem; outline: none; cursor: pointer;">
                
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="/attendance" class="btn btn-secondary" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); text-decoration: none;">
                    <i class="fas fa-times-circle"></i> Clear Filters
                </a>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>User ID</th>
                    <th>Timestamp</th>
                    <th>Status</th>
                    <th style="text-align: right;">Device ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr>
                        <td>
                            <div style="font-weight: 600; font-size: 1rem; color: white;">{{ $att->user_name ?? 'Unknown' }}</div>
                        </td>
                        <td>
                            <div style="color: var(--text-muted);">{{ $att->user_id }}</div>
                        </td>
                        <td>
                            <div style="color: white; font-weight: 500;">{{ date('Y-m-d', strtotime($att->timestamp)) }}</div>
                            <div style="color: var(--text-muted); font-size: 0.85rem;">{{ date('H:i:s', strtotime($att->timestamp)) }}</div>
                        </td>
                        <td>
                            @if(isset($att->calculated_status))
                                @if($att->calculated_status == 'Check-In')
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 10px; border-radius: 6px;"><i class="fas fa-arrow-right-to-bracket"></i> Check-In (Arrivée)</span>
                                @elseif($att->calculated_status == 'Check-Out')
                                    <span class="badge" style="background: rgba(244, 63, 94, 0.15); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); padding: 5px 10px; border-radius: 6px;"><i class="fas fa-arrow-right-from-bracket"></i> Check-Out (Départ)</span>
                                @else
                                    <span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #94a3b8; border: 1px solid rgba(100, 116, 139, 0.3); padding: 5px 10px; border-radius: 6px;"><i class="fas fa-clock"></i> Intermediate Log</span>
                                @endif
                            @else
                                @if($att->status == 0 || $att->status == '0' || $att->status == 'Check-In')
                                    <span class="badge badge-checkin"><i class="fas fa-arrow-right-to-bracket"></i> Check-In (Arrivée)</span>
                                @elseif($att->status == 1 || $att->status == '1' || $att->status == 'Check-Out')
                                    <span class="badge badge-checkout"><i class="fas fa-arrow-right-from-bracket"></i> Check-Out (Départ)</span>
                                @elseif($att->status == 2 || $att->status == '2')
                                    <span class="badge badge-break"><i class="fas fa-mug-hot"></i> Break-Out</span>
                                @elseif($att->status == 3 || $att->status == '3')
                                    <span class="badge badge-break"><i class="fas fa-mug-hot"></i> Break-In</span>
                                @elseif($att->status == 4 || $att->status == '4')
                                    <span class="badge badge-overtime"><i class="fas fa-clock"></i> OT-In</span>
                                @elseif($att->status == 5 || $att->status == '5')
                                    <span class="badge badge-overtime"><i class="fas fa-clock"></i> OT-Out</span>
                                @else
                                    <span class="badge badge-unknown">{{ $att->status }}</span>
                                @endif
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="color: var(--text-muted); font-family: monospace;">{{ $att->device_id ?? 'Unknown' }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-history" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>No attendance records found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($attendances->hasPages())
        {{ $attendances->appends(request()->query())->links('pagination::bootstrap-5') }}
    @endif
</div>

@endsection

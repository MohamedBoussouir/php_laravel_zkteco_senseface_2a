@push('styles')
<style>
    /* KPI Cards */
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .kpi-card { display: flex; align-items: center; gap: 15px; padding: 20px; text-decoration: none; }
    .kpi-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; justify-content: center; align-items: center; font-size: 1.6rem; flex-shrink: 0; transition: transform 0.3s; }
    .kpi-card:hover .kpi-icon { transform: scale(1.1) rotate(5deg); }
    .kpi-icon.blue { background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0.05)); color: #60a5fa; box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); border: 1px solid rgba(59, 130, 246, 0.3); }
    .kpi-icon.green { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.05)); color: #34d399; box-shadow: 0 0 15px rgba(16, 185, 129, 0.3); border: 1px solid rgba(16, 185, 129, 0.3); }
    .kpi-icon.red { background: linear-gradient(135deg, rgba(244, 63, 94, 0.2), rgba(244, 63, 94, 0.05)); color: #fb7185; box-shadow: 0 0 15px rgba(244, 63, 94, 0.3); border: 1px solid rgba(244, 63, 94, 0.3); }
    .kpi-icon.purple { background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(168, 85, 247, 0.05)); color: #c084fc; box-shadow: 0 0 15px rgba(168, 85, 247, 0.3); border: 1px solid rgba(168, 85, 247, 0.3); }
    .kpi-info h3 { margin: 0; font-size: 0.85rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .kpi-info .value { font-size: 2rem; font-weight: 800; color: white; line-height: 1.2; margin-top: 5px; }
    
    /* Main Layout Grid */
    .dashboard-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; align-items: start; }
    @media (max-width: 1024px) {
        .dashboard-layout { grid-template-columns: 1fr; }
    }

    /* Section Cards */
    .section-header { padding: 24px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); }
    .section-title { margin: 0; font-size: 1.2rem; font-weight: 700; color: white; display: flex; align-items: center; gap: 12px; }
    
    /* Recent Activity List */
    .activity-list { display: flex; flex-direction: column; }
    .activity-item { padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 15px; transition: background 0.2s; }
    .activity-item:hover { background: rgba(255,255,255,0.02); }
    .activity-item:last-child { border-bottom: none; }
    .activity-avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; justify-content: center; align-items: center; overflow: hidden; border: 2px solid #3b82f6; }
    .activity-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .activity-details { flex: 1; }
    .activity-name { font-weight: 600; color: white; margin-bottom: 3px; font-size: 0.95rem; }
    .activity-time { color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }
    
    /* Devices List */
    .device-list-item { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; gap: 10px; }
    .device-list-item:last-child { border-bottom: none; }
    .device-info-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .device-name { font-weight: 600; color: white; font-size: 1.05rem; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
    .device-sn { color: var(--text-muted); font-size: 0.8rem; font-family: monospace; }
    
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .status-online { background: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-offline { background: rgba(244, 63, 94, 0.15); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; box-shadow: 0 0 5px currentColor; }
    
    .device-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 5px; }
    .d-stat { background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); }
    .d-stat-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 2px; }
    .d-stat-val { font-size: 0.9rem; font-weight: bold; color: #e2e8f0; }

    .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
    .empty-state i { font-size: 3rem; opacity: 0.5; margin-bottom: 15px; }
</style>    
@endpush

@extends('layouts.app')
@section('content')

<div class="container">
    <div class="header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1><i class="fas fa-chart-pie" style="color: var(--accent);"></i> {{ __('Dashboard') }}</h1>
            <p>{{ __('Overview of Human Resources & Hardware') }}</p>
        </div>
        <div style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 12px; text-align: center;">
            <div id="live-clock" style="font-size: 1.5rem; font-weight: bold; font-variant-numeric: tabular-nums; letter-spacing: 1px; color: white;">00:00:00</div>
            <div id="live-date" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">--</div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card glass-card">
            <div class="kpi-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Total Employees') }}</h3>
                <div class="value">{{ number_format($totalEmployees) }}</div>
            </div>
        </div>
        
        <a href="/attendance?start_date={{ date('Y-m-d') }}&end_date={{ date('Y-m-d') }}" class="kpi-card glass-card">
            <div class="kpi-icon green">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Present Today') }}</h3>
                <div class="value">{{ number_format($presentToday) }}</div>
            </div>
        </a>

        <a href="/reports/absence?mode=day&date={{ date('Y-m-d') }}" class="kpi-card glass-card">
            <div class="kpi-icon red">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Absent Today') }}</h3>
                <div class="value">{{ number_format($absentToday) }}</div>
            </div>
        </a>

        <div class="kpi-card glass-card">
            <div class="kpi-icon purple">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Connected Devices') }}</h3>
                <div class="value">{{ $devices->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="dashboard-layout">
        
        <!-- Left Column: Recent Punches -->
        <div class="section-card glass-card" style="overflow: hidden;">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-clock text-blue-400"></i> {{ __('Recent Punches') }}</h2>
                <a href="/attendance" class="glass-btn" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; text-decoration: none; color: white;">{{ __('View All') }}</a>
            </div>
            
            <div class="activity-list">
                @forelse($recentPunches as $punch)
                <div class="activity-item">
                    <div class="activity-avatar">
                        @if($punch->user && $punch->user->photo)
                            <img src="data:image/jpeg;base64,{{ $punch->user->photo }}" alt="User">
                        @else
                            <i class="fas fa-user" style="color: rgba(255,255,255,0.4);"></i>
                        @endif
                    </div>
                    <div class="activity-details">
                        <div class="activity-name">{{ $punch->user_name ?? ($punch->user ? $punch->user->name : __('Unknown Employee')) }}</div>
                        <div class="activity-time">
                            <i class="fas fa-history"></i>
                            {{ \Carbon\Carbon::parse($punch->timestamp)->diffForHumans() }} 
                            <span style="opacity: 0.5; margin: 0 5px;">•</span> 
                            {{ \Carbon\Carbon::parse($punch->timestamp)->format('h:i A') }}
                        </div>
                    </div>
                    <div>
                        <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                            ID: {{ $punch->user_id }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-mug-hot"></i>
                    <h3>{{ __('No Punches Yet') }}</h3>
                    <p>{{ __('No one has punched in or out recently.') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Connected Devices -->
        <div class="section-card glass-card" style="overflow: hidden;">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-server text-purple-400"></i> {{ __('Hardware Status') }}</h2>
                <a href="/devices" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;"><i class="fas fa-cog"></i> {{ __('Manage') }}</a>
            </div>
            
            <div class="device-list">
                @forelse($devices as $device)
                    @php
                        $meta = is_array($device->metadata) ? $device->metadata : json_decode($device->metadata, true);
                        $deviceName = $meta['DeviceName'] ?? $device->name ?? __('Unknown Device');
                        $isOnline = $device->last_seen ? \Carbon\Carbon::parse($device->last_seen)->diffInMinutes(now()) < 5 : false;
                        
                        $users = intval($meta['UserCount'] ?? 0);
                        $logs = intval($meta['TransactionCount'] ?? 0);
                    @endphp
                    
                    <div class="device-list-item">
                        <div class="device-info-top">
                            <div>
                                <div class="device-name"><i class="fas fa-fingerprint text-purple-400"></i> {{ $deviceName }}</div>
                                <div class="device-sn">SN: {{ $device->sn }}</div>
                            </div>
                            <div class="status-badge {{ $isOnline ? 'status-online' : 'status-offline' }}">
                                <div class="status-dot"></div>
                                {{ $isOnline ? __('Online') : __('Offline') }}
                            </div>
                        </div>
                        <div class="device-stats">
                            <div class="d-stat">
                                <div class="d-stat-label">{{ __('Local Users') }}</div>
                                <div class="d-stat-val">{{ number_format($users) }}</div>
                            </div>
                            <div class="d-stat">
                                <div class="d-stat-label">{{ __('Att Logs') }}</div>
                                <div class="d-stat-val">{{ number_format($logs) }}</div>
                            </div>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-align: right; margin-top: 5px;">
                            IP: {{ $meta['IPAddress'] ?? $device->ip ?? __('Unknown') }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: 30px 20px;">
                        <i class="fas fa-satellite"></i>
                        <h3>{{ __('No Devices') }}</h3>
                        <p style="font-size: 0.85rem;">{{ __('Connect a device to monitor it.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function updateLiveClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute:'2-digit', second:'2-digit' });
        const dateStr = now.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        const clockEl = document.getElementById('live-clock');
        const dateEl = document.getElementById('live-date');
        
        if(clockEl) clockEl.textContent = timeStr;
        if(dateEl) dateEl.textContent = dateStr;
    }
    
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
</script>
@endpush
@endsection




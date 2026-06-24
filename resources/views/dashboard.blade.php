@push('styles')
<style>
    /* KPI Cards */
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .kpi-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease; }
    .kpi-card:hover { transform: translateY(-3px); }
    .kpi-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; flex-shrink: 0; }
    .kpi-icon.blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .kpi-icon.green { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .kpi-icon.red { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .kpi-icon.purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
    .kpi-info h3 { margin: 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-info .value { font-size: 1.8rem; font-weight: bold; color: white; line-height: 1.2; margin-top: 5px; }
    
    /* Main Layout Grid */
    .dashboard-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; align-items: start; }
    @media (max-width: 1024px) {
        .dashboard-layout { grid-template-columns: 1fr; }
    }

    /* Section Cards */
    .section-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .section-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.1); }
    .section-title { margin: 0; font-size: 1.1rem; color: white; display: flex; align-items: center; gap: 10px; }
    
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
    <div class="header" style="margin-bottom: 30px;">
        <h1><i class="fas fa-chart-pie" style="color: var(--accent);"></i> {{ __('Dashboard') }}</h1>
        <p>{{ __('Overview of Human Resources & Hardware') }}</p>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Total Employees') }}</h3>
                <div class="value">{{ number_format($totalEmployees) }}</div>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-icon green">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Present Today') }}</h3>
                <div class="value">{{ number_format($presentToday) }}</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon red">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ __('Absent Today') }}</h3>
                <div class="value">{{ number_format($absentToday) }}</div>
            </div>
        </div>

        <div class="kpi-card">
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
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-clock text-blue-400"></i> {{ __('Recent Punches') }}</h2>
                <a href="/attendance" class="btn-primary" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; text-decoration: none; color: white;">{{ __('View All') }}</a>
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

        <!-- Right Column: Hardware Status -->
        <div class="section-card">
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

@endsection




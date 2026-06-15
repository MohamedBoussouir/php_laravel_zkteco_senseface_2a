@push('styles')
<style>
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
        .glass-card { background: var(--card-bg); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid var(--border-color); border-radius: 16px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .glass-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 0 20px var(--accent-glow); border-color: rgba(255,255,255,0.2); }
        .device-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 20px; }
        .device-title { display: flex; align-items: center; gap: 12px; }
        .device-icon { background: rgba(59, 130, 246, 0.2); color: var(--accent); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .status-online { background: rgba(16, 185, 129, 0.2); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-offline { background: rgba(244, 63, 94, 0.2); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; box-shadow: 0 0 8px currentColor; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .info-item { display: flex; flex-direction: column; gap: 4px; }
        .info-label { color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-weight: 600; font-size: 1.05rem; color: #e2e8f0; }
        .progress-section { margin-top: 15px; }
        .progress-header { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
        .progress-bar { height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; margin-bottom: 15px; }
        .progress-fill { height: 100%; border-radius: 4px; transition: width 1s ease-in-out; }
        .fill-blue { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .fill-green { background: linear-gradient(90deg, #10b981, #34d399); }
        .fill-purple { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .empty-state { text-align: center; padding: 60px 20px; background: var(--card-bg); border-radius: 16px; border: 1px dashed var(--border-color); }
        .empty-state i { font-size: 4rem; color: var(--text-muted); margin-bottom: 20px; opacity: 0.5; }
</style>    
@endpush
@extends('layouts.app')
@section('content')

<div class="container">
    <div class="header">
        <h1><i class="fas fa-satellite-dish"></i> Hardware Command Center</h1>
        <p>Live Monitoring & Device Metadata Dashboard</p>
    </div>

    <div class="grid">
        @forelse($devices as $device)
            @php
                $meta = is_array($device->metadata) ? $device->metadata : json_decode($device->metadata, true);
                
                // Defaults if metadata is missing
                $deviceName = $meta['DeviceName'] ?? $device->name ?? 'Unknown Device';
                $mac = $meta['MAC'] ?? 'N/A';
                $fwVersion = $meta['FWVersion'] ?? 'N/A';
                $ip = $meta['IPAddress'] ?? $device->ip ?? 'N/A';
                
                // Progress calculations
                $users = intval($meta['UserCount'] ?? 0);
                $maxUsers = intval($meta['MaxUserCount'] ?? 10000);
                $userPct = min(100, ($maxUsers > 0) ? ($users / $maxUsers) * 100 : 0);

                $logs = intval($meta['TransactionCount'] ?? 0);
                $maxLogs = intval($meta['MaxAttLogCount'] ?? 100000);
                $logPct = min(100, ($maxLogs > 0) ? ($logs / $maxLogs) * 100 : 0);

                $fingers = intval($meta['FPCount'] ?? 0);
                $maxFingers = intval($meta['MaxFingerCount'] ?? 3000);
                $fingerPct = min(100, ($maxFingers > 0) ? ($fingers / $maxFingers) * 100 : 0);
                
                // Status Check (Online if seen in last 5 mins)
                $isOnline = $device->last_seen ? \Carbon\Carbon::parse($device->last_seen)->diffInMinutes(now()) < 5 : false;
            @endphp

            <div class="glass-card">
                <div class="device-header">
                    <div class="device-title">
                        <div class="device-icon">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.2rem;">{{ $deviceName }}</h2>
                            <span style="color: var(--text-muted); font-size: 0.85rem; font-family: monospace;">SN: {{ $device->sn }}</span>
                        </div>
                    </div>
                    <div class="status-badge {{ $isOnline ? 'status-online' : 'status-offline' }}">
                        <div class="status-dot"></div>
                        {{ $isOnline ? 'Online' : 'Offline' }}
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-network-wired text-purple-400"></i> MAC Address</span>
                        <span class="info-value" style="font-family: monospace;">{{ $meta['MAC'] ?? 'Unknown' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-microchip text-yellow-400"></i> Firmware</span>
                        <span class="info-value">{{ $meta['FWVersion'] ?? 'Unknown' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-server text-green-400"></i> IP Address</span>
                        <span class="info-value" style="font-family: monospace;">{{ $meta['IPAddress'] ?? 'Unknown' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-language text-pink-400"></i> Language Code</span>
                        <span class="info-value">{{ $meta['Language'] ?? 'Unknown' }}</span>
                    </div>
                </div>

                @php
                // Progress calculations with fallback for strange device metadata
                $users = intval($meta['UserCount'] ?? 0);
                $rawMaxUsers = intval($meta['MaxUserCount'] ?? 3000);
                $maxUsers = $rawMaxUsers < 100 ? $rawMaxUsers * 100 : $rawMaxUsers; // Fix for devices sending '30' instead of '3000'
                $userPct = min(100, ($maxUsers > 0) ? ($users / $maxUsers) * 100 : 0);

                $logs = intval($meta['TransactionCount'] ?? 0);
                $rawMaxLogs = intval($meta['MaxAttLogCount'] ?? 150000);
                $maxLogs = $rawMaxLogs < 100 ? $rawMaxLogs * 10000 : $rawMaxLogs; // Fix for devices sending '15' instead of '150000'
                $logPct = min(100, ($maxLogs > 0) ? ($logs / $maxLogs) * 100 : 0);

                $fingers = intval($meta['FPCount'] ?? 0);
                $rawMaxFingers = intval($meta['MaxFingerCount'] ?? 3000);
                $maxFingers = $rawMaxFingers < 100 ? $rawMaxFingers * 100 : $rawMaxFingers; // Fix for devices sending '30' instead of '3000'
                $fingerPct = min(100, ($maxFingers > 0) ? ($fingers / $maxFingers) * 100 : 0);
                @endphp

                <div class="progress-section">
                    <div class="progress-header">
                        <span><i class="fas fa-users text-blue-400"></i> Local Users</span>
                        <span>{{ number_format($users) }} / {{ number_format($maxUsers) }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill fill-blue" style="width: {{ $userPct }}%;"></div>
                    </div>

                    <div class="progress-header">
                        <span><i class="fas fa-list text-green-400"></i> Attendance Logs</span>
                        <span>{{ number_format($logs) }} / {{ number_format($maxLogs) }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill fill-green" style="width: {{ $logPct }}%;"></div>
                    </div>

                    <div class="progress-header">
                        <span><i class="fas fa-fingerprint text-purple-400"></i> Fingerprints</span>
                        <span>{{ number_format($fingers) }} / {{ number_format($maxFingers) }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill fill-purple" style="width: {{ $fingerPct }}%;"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column: 1 / -1;">
                <i class="fas fa-satellite"></i>
                <h2>Waiting for Devices...</h2>
                <p style="color: var(--text-muted); margin-top: 10px;">Please connect your ZKTeco device to the network and point the ADMS Cloud Server setting to this IP address.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection




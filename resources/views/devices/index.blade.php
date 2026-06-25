@extends('layouts.app')

@push('styles')
<style>
    .listen-btn {
        background: linear-gradient(to right, #3b82f6, #8b5cf6);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    }
    
    .listen-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }

    .listen-btn.listening {
        background: linear-gradient(to right, #f59e0b, #ef4444);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    /* Pending Modal */
    #pending-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(5px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        padding: 30px;
        width: 400px;
        text-align: center;
    }

    .approve-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        margin-top: 15px;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    .approve-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6); }

    .table-container {
        border-radius: 16px;
        overflow: hidden;
    }

    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th, td { padding: 15px 20px; text-align: left; }
    th { background: rgba(0,0,0,0.3); color: var(--text-muted); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--glass-border); }
    td { color: var(--text-main); font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.02); transition: background 0.3s; }
    tr:hover td { background: rgba(255,255,255,0.03); }
    tr:last-child td { border-bottom: none; }
</style>
@endpush

@section('content')

<div class="top-bar">
    <h1 class="page-title">{{ __('Device Management') }}</h1>
    <button class="listen-btn" id="listenBtn" onclick="toggleListening()">
        <i class="fas fa-radar"></i> <span>{{ __('Listen for Devices') }}</span>
    </button>
</div>

<div class="table-container glass-card">
    <table>
        <thead>
            <tr>
                <th>{{ __('Serial Number') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Last Seen') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvedDevices as $device)
            <tr>
                <td><strong>{{ $device->sn }}</strong></td>
                <td>
                    @php
                        // Device is online if seen in the last 5 minutes (300 seconds)
                        $isOnline = (time() - (int)$device->last_seen) < 300;
                    @endphp
                    @if($isOnline)
                        <span style="color: #10b981; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; background: rgba(16,185,129,0.1); padding: 4px 10px; border-radius: 12px;">
                            <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 8px #10b981;"></span>
                            {{ __('Online') }}
                        </span>
                    @else
                        <span style="color: #f43f5e; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; background: rgba(244,63,94,0.1); padding: 4px 10px; border-radius: 12px;">
                            <span style="width: 8px; height: 8px; background: #f43f5e; border-radius: 50%;"></span>
                            {{ __('Offline') }}
                        </span>
                    @endif
                </td>
                <td>
                    <div style="color: white;">{{ \Carbon\Carbon::createFromTimestamp((int)$device->last_seen)->diffForHumans() }}</div>
                    <div style="color: var(--text-muted); font-size: 0.8rem;">{{ \Carbon\Carbon::createFromTimestamp((int)$device->last_seen)->format('Y-m-d H:i:s') }}</div>
                </td>
                <td>
                    <button onclick="removeDevice({{ $device->id }})" style="background: rgba(244,63,94,0.2); color: #f43f5e; border:none; padding: 5px 10px; border-radius:4px; cursor:pointer;">
                        {{ __('Remove') }}
                    </button>
                </td>
            </tr>
            @endforeach
            @if($approvedDevices->isEmpty())
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    {{ __('No approved devices found. Click "Listen for Devices" to discover new ones.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="pending-modal">
    <div class="modal-content glass-card">
        <div style="font-size: 3rem; color: var(--warning); margin-bottom: 15px;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>New Device Detected!</h3>
        <p style="color: var(--text-muted); margin: 15px 0;">A new device is trying to connect to your ADMS server.</p>
        <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="font-size: 1.2rem; font-family: monospace;" id="pending-sn"></strong>
        </div>
        <button class="approve-btn" onclick="approveDevice()">Approve & Add Device</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let isListening = false;
    let pollInterval;
    let currentPendingId = null;

    function toggleListening() {
        isListening = !isListening;
        const btn = document.getElementById('listenBtn');
        const span = btn.querySelector('span');
        const icon = btn.querySelector('i');

        if (isListening) {
            btn.classList.add('listening');
            span.innerText = "Scanning network...";
            icon.className = "fas fa-spinner fa-spin";
            
            // Start polling every 2 seconds
            pollInterval = setInterval(checkPendingDevices, 2000);
        } else {
            btn.classList.remove('listening');
            span.innerText = "Listen for Devices";
            icon.className = "fas fa-radar";
            clearInterval(pollInterval);
        }
    }

    function checkPendingDevices() {
        fetch('/devices/pending')
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    // We found a pending device!
                    let device = data[0];
                    currentPendingId = device.id;
                    document.getElementById('pending-sn').innerText = "SN: " + device.sn;
                    document.getElementById('pending-modal').style.display = 'flex';
                    
                    // Stop listening temporarily
                    toggleListening();
                }
            });
    }

    function approveDevice() {
        if (!currentPendingId) return;

        fetch('/devices/approve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: currentPendingId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show the newly approved device in the table
                window.location.reload();
            } else {
                alert("Failed to approve device.");
            }
        });
    }
    function removeDevice(id) {
        if (!confirm("Are you sure you want to remove this device?")) return;

        fetch('/devices/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert("Failed to remove device.");
            }
        });
    }
</script>
@endpush


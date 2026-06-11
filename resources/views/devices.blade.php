@extends('layouts.app')
@section('content')
<div id="devices-page" class="page">
            <div class="stats-grid">
                <div class="card">
                    <div class="section-header">
                        <h2>Add New Terminal</h2>
                    </div>
                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" id="device-name" placeholder="Front Entrance">
                    </div>
                    <div class="form-group">
                        <label>Serial Number (SN) <span style="color:var(--text-muted); font-size:0.8rem;">*Required for ADMS</span></label>
                        <input type="text" id="device-sn" placeholder="e.g. CFEZ21326... ">
                    </div>
                    <div class="form-group">
                        <label>Local IP Address <span style="color:var(--text-muted); font-size:0.8rem;">(Optional)</span></label>
                        <input type="text" id="device-ip" placeholder="192.168.1.200">
                        <input type="hidden" id="device-port1" value="8088">
                        <input type="hidden" id="device-port2" value="8088">
                    </div>
                    <div style="display:flex; gap:10px; margin-top:20px">
                        <button class="btn btn-secondary flex-1" onclick="app.scanDevices()" id="scan-devices-btn"><i class="fas fa-search"></i> Auto Discover</button>
                        <button class="btn btn-primary flex-1" onclick="app.addDevice()">Save Device</button>
                    </div>
                </div>
                <div class="card" style="grid-column: span 2;">
                    <div class="section-header">
                        <h2>Connected Terminals</h2>
                    </div>
                    <div id="devices-grid" class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 15px;">
                        <!-- Device cards injected here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Reports Page -->
        
@endsection

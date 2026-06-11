@extends('layouts.app')
@section('content')
<div id="playground-page" class="page">
            <div class="stats-grid">
                <div class="card">
                    <div class="section-header"><h2>Hardware Terminal</h2></div>
                    <div class="form-group">
                        <label>Device IP</label>
                        <input type="text" id="pg-ip" value="192.168.0.157">
                    </div>
                    <div class="form-group">
                        <label>Port</label>
                        <input type="number" id="pg-port" value="4370">
                    </div>
                    <div class="form-group">
                        <label>Target UID</label>
                        <input type="number" id="pg-uid" value="1">
                    </div>
                    <div class="playground-controls" style="margin-top:20px">
                        <button class="btn btn-secondary" onclick="runPgCmd('beep')"><i class="fas fa-volume-up"></i> Beep</button>
                        <button class="btn btn-secondary" onclick="runPgCmd('getInfo')"><i class="fas fa-info-circle"></i> Info</button>
                        <button class="btn btn-secondary" onclick="runPgCmd('getUsers')"><i class="fas fa-users"></i> Users</button>
                        <button class="btn btn-secondary" onclick="runPgCmd('getAttendance')"><i class="fas fa-list"></i> Logs</button>
                        <button class="btn btn-secondary" onclick="runPgCmd('getFingers')"><i class="fas fa-fingerprint"></i> Fingers</button>
                        <button class="btn btn-red" onclick="runPgCmd('reboot')"><i class="fas fa-redo-alt"></i> Reboot</button>
                    </div>
                </div>
                <div class="card" style="grid-column: span 2;">
                    <div class="section-header"><h2>Raw Response</h2></div>
                    <div id="pg-response" style="background:#000; color:#50fa7b; padding:20px; border-radius:12px; height:350px; overflow:auto; font-family:monospace; border:1px solid var(--border-color)">
                        [System] Ready for hardware commands...
                    </div>
                </div>
            </div>
        </div>
    
@endsection

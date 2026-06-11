@extends('layouts.app')
@section('content')
<div id="dashboard-page" class="page active">
            <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="stat-card">
                    <div class="icon-box blue"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3>Local Users</h3>
                        <p id="total-employees">{{ $userCount }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box purple"><i class="fas fa-fingerprint"></i></div>
                    <div class="stat-info">
                        <h3>Fingerprints</h3>
                        <p id="total-fingerprints">{{ $userCount }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box orange"><i class="fas fa-list-check"></i></div>
                    <div class="stat-info">
                        <h3>Today's Punches</h3>
                        <p id="today-punches">{{ $attendanceCount }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon-box green"><i class="fas fa-database"></i></div>
                    <div class="stat-info">
                        <h3>Total Logs</h3>
                        <p id="total-logs">{{ $attendanceCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Hardware Overview -->
            <div class="card mt-4" style="margin-top: 15px;">
                <div class="section-header" style="margin-bottom: 10px;">
                    <h2><i class="fas fa-microchip"></i> Hardware Overview (Live)</h2>
                    <span id="device-status-text" class="status-badge online" style="background: #10b981; color: white;">Online</span>
                </div>
                <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50 flex justify-between items-center">
                        <div>
                            <h4 class="text-slate-400 text-sm mb-1">Users on Device</h4>
                            <div class="font-mono text-xl"><span id="hw-users">{{ $userCount }}</span> <span class="text-xs text-slate-500">/ <span id="hw-user-cap">--</span></span></div>
                        </div>
                        <i class="fas fa-user-tie text-blue-400 opacity-50 text-2xl"></i>
                    </div>
                    <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50 flex justify-between items-center">
                        <div>
                            <h4 class="text-slate-400 text-sm mb-1">Fingerprints on Device</h4>
                            <div class="font-mono text-xl"><span id="hw-fingers">0</span> <span class="text-xs text-slate-500">/ <span id="hw-finger-cap">--</span></span></div>
                        </div>
                        <i class="fas fa-fingerprint text-purple-400 opacity-50 text-2xl"></i>
                    </div>
                    <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50 flex justify-between items-center">
                        <div>
                            <h4 class="text-slate-400 text-sm mb-1">Attendance Logs on Device</h4>
                            <div class="font-mono text-xl"><span id="hw-logs">0</span> <span class="text-xs text-slate-500">/ <span id="hw-log-cap">--</span></span></div>
                        </div>
                        <i class="fas fa-clipboard-list text-green-400 opacity-50 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="chart-container-card">
                <div class="section-header">
                    <h2>Attendance Trend</h2>
                    <span style="font-size: 0.8rem; color: var(--text-muted)">Last 7 Days</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <div class="recent-logs">
                <div class="section-header">
                    <h2>Recent Activity</h2>
                    <button class="btn btn-secondary btn-sm" onclick="app.showPage('logs')">View All</button>
                </div>
                <div class="table-container">
                    <table id="recent-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>ID</th>
                                <th>Time</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data injection -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manage Users Page -->
        
@endsection




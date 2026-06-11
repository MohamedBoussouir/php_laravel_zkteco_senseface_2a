@extends('layouts.app')
@section('content')
<div id="settings-page" class="page">
            <div class="settings-grid">
                <!-- Left Column -->
                <div class="settings-col">
                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fas fa-network-wired"></i> ADMS Network Settings</h2>
                        </div>
                        <p class="text-slate-400 mb-3" style="font-size: 0.85rem;">Set the port the application uses to listen for device connections. Note: You must update Windows Firewall manually if you change this.</p>
                        <div class="form-group mb-2">
                            <label style="font-size: 0.85rem;">Server Port (Default: 80)</label>
                            <input type="number" id="setting-adms-port" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 8px; border-radius: 8px; width: 100%; margin-bottom: 8px; outline: none; font-size: 0.9rem;">
                        </div>
                        <button class="btn btn-primary btn-full mb-2" onclick="app.applyAdmsPort()">
                            <i class="fas fa-save"></i> Apply Port
                        </button>
                        <div id="adms-port-status" class="mt-2 text-sm" style="text-align: center;"></div>
                    </div>

                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fas fa-clock"></i> Device Time Settings</h2>
                        </div>
                        <p class="text-slate-400 mb-3" style="font-size: 0.85rem;">Automatic Sync (Sync with Computer Clock):</p>
                        <button class="btn btn-primary btn-full mb-4" onclick="app.syncAllClocks()">
                            <i class="fas fa-sync-alt"></i> Sync Automatically
                        </button>
                        
                        <p class="text-slate-400 mb-2" style="font-size: 0.85rem;">Manual Time Adjustment:</p>
                        <div class="form-group mb-2">
                            <input type="datetime-local" id="manual-datetime-input" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 8px; border-radius: 8px; width: 100%; margin-bottom: 8px; outline: none; font-size: 0.9rem;">
                        </div>
                        <button class="btn btn-outline btn-full mb-4" onclick="app.setManualTime()">
                            <i class="fas fa-edit"></i> Set Time Manually
                        </button>
                        
                        <p class="text-slate-400 mb-2" style="font-size: 0.85rem; margin-top: 15px;">Device Maintenance:</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <button class="btn btn-outline btn-full" style="border-color: #fbbf24; color: #fbbf24; padding: 10px 5px; font-size: 0.85rem;" onclick="app.rebootAllDevices()">
                                <i class="fas fa-power-off"></i> Reboot Devices
                            </button>
                            <button class="btn btn-outline btn-full" style="border-color: #ef4444; color: #ef4444; padding: 10px 5px; font-size: 0.85rem;" onclick="app.clearDeviceLogs()">
                                <i class="fas fa-trash-alt"></i> Clear ZK Logs
                            </button>
                        </div>
                        <div id="sync-time-status" class="mt-4 text-sm" style="text-align: center;"></div>
                    </div>

                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fas fa-magic"></i> Attendance Logging Mode</h2>
                        </div>
                        <p class="text-slate-400 mb-4" style="font-size: 0.85rem">Choose how attendance punch states (Check-In / Check-Out) are recorded and displayed.</p>
                        
                        <div class="form-group mb-4">
                            <label style="font-size: 0.8rem; color: var(--text-muted)">Logging Strategy</label>
                            <select id="setting-calc-mode" class="form-control" onchange="app.updateStrategyExplanation()" style="width: 100%; background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; margin-top: 5px; font-size: 0.9rem; outline: none; cursor: pointer;">
                                <option value="calculated" style="background: #1e293b; color: #f8fafc;">Smart Calculation (First-In, Last-Out) 🤖</option>
                                <option value="device" style="background: #1e293b; color: #f8fafc;">Device Keypad Selection (Manual F1/F2) 👤</option>
                            </select>
                        </div>

                        <div id="strategy-explanation" class="p-3 mb-4 rounded-lg bg-slate-900/60 border border-slate-700/50 text-xs text-slate-400 leading-relaxed" style="min-height: 80px;">
                            <strong>🤖 Smart Calculation Mode (Recommended):</strong><br>
                            The app dynamically calculates attendance status based on daily punches. The first punch of the day is marked as <strong>Check-In (Arrivée)</strong> and the last punch as <strong>Check-Out (Départ)</strong>.<br>
                            <span class="text-green-400 mt-1 block"><i class="fas fa-shield-alt"></i> The device screen remains clean and simple without any confusing status words.</span>
                        </div>

                        <button class="btn btn-primary btn-full" onclick="app.saveStrategyMode()">
                            <i class="fas fa-check-circle"></i> Apply Strategy
                        </button>
                        <div id="program-auto-status" class="mt-3 text-sm text-center" style="color: var(--text-muted);"></div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="settings-col">
                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fas fa-language"></i> Application Language</h2>
                        </div>
                        <p class="text-slate-400 mb-4" style="font-size: 0.85rem">Choose the display language for the application interface.</p>
                        
                        <div class="form-group mb-4">
                            <label style="font-size: 0.8rem; color: var(--text-muted)">Display Language</label>
                            <select id="setting-app-lang" class="form-control" onchange="app.changeLanguage(this.value)" style="width: 100%; background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; margin-top: 5px; font-size: 0.9rem; outline: none; cursor: pointer;">
                                <option value="en" style="background: #1e293b; color: #f8fafc;">🇬🇧 English</option>
                                <option value="fr" style="background: #1e293b; color: #f8fafc;">🇫🇷 Français</option>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fas fa-clock"></i> Default Shift Times</h2>
                        </div>
                        <p class="text-slate-400 mb-4" style="font-size: 0.85rem">Set the global default working hours for the company. These are used when no specific shift is assigned to an employee.</p>
                        
                        <div class="form-group mb-3">
                            <label style="font-size: 0.8rem; color: var(--text-muted)">Default Start Time</label>
                            <input type="time" id="setting-default-start" style="width: 100%; background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; margin-top: 5px; font-size: 0.9rem; outline: none;">
                        </div>
                        
                        <div class="form-group mb-4">
                            <label style="font-size: 0.8rem; color: var(--text-muted)">Default End Time</label>
                            <input type="time" id="setting-default-end" style="width: 100%; background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; margin-top: 5px; font-size: 0.9rem; outline: none;">
                        </div>
                        
                        <button class="btn btn-primary btn-full" onclick="app.saveDefaultShiftSettings()">
                            <i class="fas fa-save"></i> Save Default Shift
                        </button>
                        <div id="default-shift-status" class="mt-3 text-sm text-center" style="color: var(--text-muted);"></div>
                    </div>

                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fas fa-info-circle"></i> System Info</h2>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between" style="display: flex; justify-content: space-between; margin-bottom: 10px;"><span class="text-slate-500">App Version:</span> <span class="font-mono">1.0.0</span></div>
                            <div class="flex justify-between" style="display: flex; justify-content: space-between; margin-bottom: 10px;"><span class="text-slate-500">Database:</span> <span class="font-mono text-blue-400">SQLite3</span></div>
                            <div class="flex justify-between" style="display: flex; justify-content: space-between; flex-direction: column; gap: 5px;"><span class="text-slate-500">Storage Path:</span> <code class="bg-slate-900 p-2 rounded border border-slate-800 text-xs font-mono text-purple-400" style="word-break: break-all;">./attendance.db</code></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Playground / Debug Center -->
        
@endsection

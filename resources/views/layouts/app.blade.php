<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HS ZK Attendance - Laravel Edition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <i class="fas fa-fingerprint"></i>
            <span>HS ATTENDANCE</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('users') }}" class="nav-item {{ request()->routeIs('users') ? 'active' : '' }}">
                <i class="fas fa-user-group"></i> Manage Users
            </a>
            <a href="{{ route('schedules') }}" class="nav-item {{ request()->routeIs('schedules') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Shifts & Schedules
            </a>
            <a href="{{ route('logs') }}" class="nav-item {{ request()->routeIs('logs') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Attendance Logs
            </a>
            <a href="{{ route('devices') }}" class="nav-item {{ request()->routeIs('devices') ? 'active' : '' }}">
                <i class="fas fa-microchip"></i> Manage Devices
            </a>
            <a href="{{ route('reports') }}" class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Late Reports
            </a>
            <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <button class="btn btn-red btn-full" id="emergency-stop-btn">
                <i class="fas fa-stop-circle"></i> Emergency Stop
            </button>
            <div style="margin-top: 15px; font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                <div id="active-device-status" class="status-indicator online" style="width:8px; height:8px; border-radius:50%"></div>
                <span id="global-status">System Ready</span>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main>
        <header>
            <div>
                <h1 id="page-title">Dashboard</h1>
                <p id="page-subtitle">Welcome back, manager.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" id="sync-all-btn">
                    <i class="fas fa-sync-alt"></i> Sync All Devices
                </button>
            </div>
        </header>

        
        @yield('content')
    </main>

    <!-- Credentials Modal -->
    <div id="credentials-modal" class="modal">
        <div class="modal-content" style="width: 450px;">
            <div class="section-header">
                <h2><i class="fas fa-key text-amber-500"></i> Manage Credentials</h2>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="cred-password" placeholder="e.g. 12345" style="flex: 1;">
                    <button class="btn btn-icon btn-red" onclick="document.getElementById('cred-password').value=''" title="Clear Password"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="form-group">
                <label>Card Code (RFID)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="cred-card" placeholder="e.g. 55667788" style="flex: 1;">
                    <button class="btn btn-icon btn-red" onclick="document.getElementById('cred-card').value=''" title="Clear Card"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <input type="hidden" id="cred-uid">
            <input type="hidden" id="cred-userid">
            <input type="hidden" id="cred-devid">
            <div style="display: flex; gap: 15px; margin-top: 25px;">
                <button class="btn btn-secondary flex-1" onclick="document.getElementById('credentials-modal').classList.remove('active')">Cancel</button>
                <button class="btn btn-primary flex-1" onclick="app.saveCredentials()">Save Credentials</button>
            </div>
        </div>
    </div>

    <!-- Enrollment Modal -->
    <div id="enroll-modal" class="modal">
        <div class="modal-content" style="width: 500px;">
            <div class="section-header">
                <h2>Fingerprint Enrollment</h2>
            </div>
            <p class="text-slate-400 mb-6">Select a finger index to begin registration on the device sensor.</p>
            <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 25px;">
                <button class="btn btn-secondary finger-btn" data-idx="0">F0</button>
                <button class="btn btn-secondary finger-btn" data-idx="1">F1</button>
                <button class="btn btn-secondary finger-btn" data-idx="2">F2</button>
                <button class="btn btn-secondary finger-btn" data-idx="3">F3</button>
                <button class="btn btn-secondary finger-btn" data-idx="4">F4</button>
                <button class="btn btn-secondary finger-btn" data-idx="5">F5</button>
                <button class="btn btn-secondary finger-btn" data-idx="6">F6</button>
                <button class="btn btn-secondary finger-btn" data-idx="7">F7</button>
                <button class="btn btn-secondary finger-btn" data-idx="8">F8</button>
                <button class="btn btn-secondary finger-btn" data-idx="9">F9</button>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button class="btn btn-secondary flex-1" id="cancel-enroll-btn">Cancel</button>
                <button class="btn btn-red flex-1" id="delete-all-fingers-btn"><i class="fas fa-eraser"></i> Delete All</button>
            </div>
        </div>
    </div>

    <!-- Device Selection Modal -->
    <div id="device-selection-modal" class="modal" style="display: none; align-items: center; justify-content: center; z-index: 9999;">
        <div class="modal-content" style="width: 400px; background: #101223; border: 1px solid var(--glass-border); padding: 30px; border-radius: 20px; box-shadow: var(--shadow-lg);">
            <h2 id="device-selection-title" style="font-weight: 600; font-size: 1.4rem; margin-bottom: 20px; text-align: center;">Select Target Device</h2>
            <div class="form-group mb-4">
                <label style="font-size: 0.9rem; color: var(--text-muted); display: block; margin-bottom: 8px;">Target Device</label>
                <select id="device-selection-select" style="width: 100%;"></select>
            </div>
            <div class="flex" style="gap: 10px; margin-top: 25px;">
                <button class="btn btn-secondary flex-1" onclick="app.hideModal('device-selection-modal')">Cancel</button>
                <button class="btn btn-primary flex-1" id="device-selection-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <!-- License Activation Modal -->
    <div id="activation-modal" class="modal" style="display: none; align-items: center; justify-content: center; z-index: 9999;">
        <div class="modal-content" style="width: 550px; text-align: center; background: #101223; border: 1px solid var(--glass-border); padding: 40px; border-radius: 28px; box-shadow: var(--shadow-lg);">
            <div class="icon-box" style="margin: 0 auto 20px auto; background: rgba(99, 102, 241, 0.1); color: #6366f1; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem;">
                <i class="fas fa-key"></i>
            </div>
            <h2 style="font-weight: 700; font-size: 1.6rem; margin-bottom: 10px;">Activate Application</h2>
            <p id="activation-error-msg" style="font-size: 0.9rem; color: #ef4444; font-weight: 600; margin-bottom: 15px;"></p>
            <p class="text-slate-400 mb-6" style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Copy your Device Machine ID and send it to the developer to receive your activation key.</p>
            
            <div class="form-group mb-4" style="text-align: left; margin-bottom: 20px;">
                <label style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 8px;">Your Device Machine ID</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="activation-mid" readonly style="font-family: monospace; background: rgba(0, 0, 0, 0.3); color: var(--text-main); font-size: 0.85rem; border: 1px solid var(--border-color); padding: 12px; border-radius: 12px; flex: 1; outline: none;">
                    <button class="btn btn-secondary" onclick="app.copyMachineId()" style="padding: 0 15px; border-radius: 12px; cursor: pointer; transition: all 0.3s;"><i class="fas fa-copy"></i></button>
                </div>
            </div>


            <div class="form-group mb-6" style="text-align: left; margin-bottom: 25px;">
                <label style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 8px;">Paste Activation Key</label>
                <textarea id="activation-key" placeholder="Enter your license activation key here..." rows="3" style="width: 100%; font-family: monospace; background: rgba(0, 0, 0, 0.3); color: var(--text-main); font-size: 0.85rem; border: 1px solid var(--border-color); padding: 12px; border-radius: 12px; outline: none; resize: none;"></textarea>
            </div>

            <button class="btn btn-primary btn-full" onclick="app.submitActivationKey()" style="width: 100%; padding: 14px 20px; font-size: 1rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-check-circle"></i> Activate System
            </button>
        </div>
    </div>

    <script src="renderer.js"></script>

    <!-- WebCam Photo Modal -->
    <div id="webcam-modal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Take Face Photo</h3>
                <button class="btn btn-icon" onclick="app.closeWebcamModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p class="text-sm text-slate-400 mb-3">Look directly at the camera. Ensure good lighting.</p>
                <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; background: #000; border-radius: 8px; overflow: hidden; aspect-ratio: 4/3;">
                    <video id="webcam-video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                    <canvas id="webcam-canvas" style="display: none;"></canvas>
                    <img id="webcam-preview" style="display: none; width: 100%; height: 100%; object-fit: cover;" />
                </div>
                <div id="webcam-status" class="mt-2 text-sm text-slate-300"></div>
                <div class="mt-3" style="display: flex; gap: 15px; justify-content: center; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                        <input type="checkbox" id="upload-as-profile" checked>
                        <span class="text-sm">Profile Photo (بروفايل)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                        <input type="checkbox" id="upload-as-face" checked>
                        <span class="text-sm">Biometric Face (بصمة وجه)</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 10px; flex-wrap: wrap;">
                <input type="file" id="local-photo-input" accept="image/*" style="display: none;" onchange="app.handleLocalPhotoSelection(event)">
                <button id="btn-local-photo" class="btn btn-secondary" onclick="document.getElementById('local-photo-input').click()">
                    <i class="fas fa-folder-open"></i> Choose from PC
                </button>
                <button id="btn-webcam-capture" class="btn btn-primary" onclick="app.captureWebcamPhoto()">
                    <i class="fas fa-camera"></i> Capture
                </button>
                <button id="btn-webcam-retake" class="btn btn-outline" style="display: none;" onclick="app.retakeWebcamPhoto()">
                    <i class="fas fa-redo"></i> Retake
                </button>
                <button id="btn-webcam-upload" class="btn btn-success" style="display: none;" onclick="app.uploadWebcamPhoto()">
                    <i class="fas fa-cloud-upload-alt"></i> Upload to Device
                </button>
            </div>
        </div>
    </div>

    <!-- Photo Preview Modal -->
    <div id="photo-preview-modal" class="modal" style="z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.85);" onclick="this.classList.remove('active'); this.style.display='none';">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none; max-width: 500px; text-align: center; position: relative;">
            <button class="btn btn-icon" style="position: absolute; top: -40px; right: 0; background: rgba(255,255,255,0.2); color: white;" onclick="document.getElementById('photo-preview-modal').classList.remove('active'); document.getElementById('photo-preview-modal').style.display='none';">
                <i class="fas fa-times"></i>
            </button>
            <img id="photo-preview-img" src="" style="max-width: 100%; max-height: 80vh; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); object-fit: contain;">
        </div>
    </div>
</body>
</html>




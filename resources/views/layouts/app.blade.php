<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZKTeco Command Center</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --sidebar-bg: #1e293b;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.5);
            --success: #10b981;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }

        .brand {
            padding: 25px 20px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand i {
            color: var(--accent);
            font-size: 1.5rem;
        }

        .nav-menu {
            padding: 20px 0;
            flex-grow: 1;
        }

        .nav-item {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-item:hover, .nav-item.active {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--accent);
            border-right: 4px solid var(--accent);
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            max-width: calc(100vw - 260px);
            box-sizing: border-box;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        }
        
        .page-title {
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 700;
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <i class="fas fa-fingerprint"></i> SenseFace ADMS
        </div>
        <div class="nav-menu">
            <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> {{ __('Dashboard') }}
            </a>
            <a href="/devices" class="nav-item {{ request()->is('devices') ? 'active' : '' }}">
                <i class="fas fa-satellite-dish"></i> {{ __('SIP Devices') }}
            </a>
            <a href="/employees" class="nav-item {{ request()->is('employees*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> {{ __('Employees Directory') }}
            </a>
            <a href="/schedules" class="nav-item {{ request()->is('schedules*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> {{ __('Schedules & Shifts') }}
            </a>
            <a href="/attendance" class="nav-item {{ request()->is('attendance*') ? 'active' : '' }}">
                <i class="fas fa-list-check"></i> {{ __('Attendance Logs') }}
            </a>
            
            <div style="margin-top: 20px; margin-bottom: 10px; padding-left: 20px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 1px;">
                {{ __('Reports') }}
            </div>
            <a href="/reports/absence" class="nav-item {{ request()->is('reports/absence*') ? 'active' : '' }}">
                <i class="fas fa-user-xmark"></i> {{ __('Absence Report') }}
            </a>
            
            <div style="margin-top: 20px; margin-bottom: 10px; padding-left: 20px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 1px;">
                {{ __('System') }}
            </div>
            <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> {{ __('Settings') }}
            </a>
            <a href="/settings/sip" class="nav-item {{ request()->is('settings/sip') ? 'active' : '' }}">
                <i class="fas fa-phone-alt"></i> {{ __('SIP / Intercom Settings') }}
            </a>

            @if(Auth::guard('admin')->check())
                <div style="margin-top: auto; padding: 20px;">
                    <div style="margin-bottom: 15px;">
                        <span style="color: var(--text-muted); font-size: 12px;">{{ __('Language') }}</span>
                        <div style="display: flex; gap: 5px; margin-top: 5px;">
                            <a href="/lang/en" style="padding: 5px 10px; background: {{ session('locale', 'en') == 'en' ? 'var(--accent)' : 'rgba(255,255,255,0.1)' }}; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;">EN</a>
                            <a href="/lang/ar" style="padding: 5px 10px; background: {{ session('locale') == 'ar' ? 'var(--accent)' : 'rgba(255,255,255,0.1)' }}; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;">AR</a>
                            <a href="/lang/fr" style="padding: 5px 10px; background: {{ session('locale') == 'fr' ? 'var(--accent)' : 'rgba(255,255,255,0.1)' }}; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;">FR</a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-item" style="width: 100%; border: none; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 8px; cursor: pointer; justify-content: flex-start;">
                            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div class="main-content" dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}">
        @yield('content')
    </div>

    <!-- SIP Video Intercom Modal -->
    <script>
        window.SIP_CONFIG = {
            uri: "sip:100@192.168.0.107",
            password: "dashboardpass",
            wsServer: "ws://192.168.0.107:8088/ws",
            snapshotUrl: "http://192.168.0.105/cgi-bin/snapshot.cgi"
        };
    </script>
    <div id="sip-call-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
        <div style="background: var(--card-bg); padding: 30px; border-radius: 16px; border: 1px solid var(--accent); text-align: center; max-width: 400px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <div style="width: 80px; height: 80px; background: rgba(59, 130, 246, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: var(--accent); font-size: 2rem;">
                <i class="fas fa-phone-volume fa-shake"></i>
            </div>
            <h2 style="color: white; margin-bottom: 10px;">Incoming Intercom Call</h2>
            <p style="color: var(--text-muted); margin-bottom: 15px;">ZKTeco SenseFace Device</p>
            <p id="sip-call-timer" style="color: var(--accent); font-weight: bold; font-size: 1.5rem; margin-bottom: 20px; display: none;">00:00</p>
            
            <iframe id="sip-remote-iframe" src="http://localhost:8889/zkteco/" allow="autoplay; fullscreen" style="display: none; width: 100%; max-width: 400px; height: 300px; border-radius: 8px; margin-bottom: 15px; border: none; background: #000;"></iframe>
            
            <audio id="sip-remote-audio" autoplay></audio>
            <audio id="sip-ringtone" loop preload="auto" src="https://assets.mixkit.co/active_storage/sfx/2870/2870-preview.mp3"></audio>
            
            <div style="display: flex; justify-content: center; gap: 15px;">
                <button id="sip-accept-btn" style="padding: 12px 20px; border-radius: 8px; border: none; background: var(--success); color: white; cursor: pointer; font-weight: bold; font-size: 1rem; flex: 1;"><i class="fas fa-phone"></i> Answer</button>
                <button id="sip-reject-btn" style="padding: 12px 20px; border-radius: 8px; border: none; background: #ef4444; color: white; cursor: pointer; font-weight: bold; font-size: 1rem; flex: 1;"><i class="fas fa-phone-slash"></i> Reject</button>
                <button id="sip-unlock-btn" style="display: none; padding: 12px 20px; border-radius: 8px; border: none; background: var(--accent); color: white; cursor: pointer; font-weight: bold; font-size: 1rem; flex: 1;"><i class="fas fa-unlock"></i> Open Door</button>
                <button id="sip-end-btn" style="display: none; padding: 12px 20px; border-radius: 8px; border: none; background: #64748b; color: white; cursor: pointer; font-weight: bold; font-size: 1rem; flex: 1;"><i class="fas fa-phone-slash"></i> End Call</button>
            </div>
        </div>
    </div>

    @stack('scripts')
    <!-- Build app.js -> contains sip-client logic -->
    @vite(['resources/js/app.js'])

    <!-- Live Notifications Script -->
    <script>
        let lastAttendanceId = null;
        
        function checkLatestAttendance() {
            fetch('/api/latest-attendance')
                .then(response => response.json())
                .then(data => {
                    if (!data) return;
                    
                    if (lastAttendanceId === null) {
                        lastAttendanceId = data.id; // Initialize on first load
                        return;
                    }
                    
                    if (data.id > lastAttendanceId) {
                        lastAttendanceId = data.id;
                        showLiveNotification(data);
                        
                        // If we are on the attendance page, reload the page to show new data
                        if (window.location.pathname.includes('/attendance')) {
                            setTimeout(() => { window.location.reload(); }, 2000);
                        }
                    }
                })
                .catch(error => console.error('Live Notification Error:', error));
        }

        function showLiveNotification(data) {
            const userName = data.user ? data.user.name : (data.user_id || 'Unknown');
            const time = new Date(data.timestamp).toLocaleTimeString();
            
            const toast = document.createElement('div');
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.background = 'var(--accent)';
            toast.style.color = 'white';
            toast.style.padding = '15px 25px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.5)';
            toast.style.zIndex = '10000';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '15px';
            toast.style.transition = 'opacity 0.5s ease-in-out';
            
            toast.innerHTML = `
                <div style="font-size: 24px;"><i class="fas fa-fingerprint"></i></div>
                <div>
                    <div style="font-weight: bold; margin-bottom: 5px;">{{ __('New attendance recorded:') }}</div>
                    <div>${userName} {{ __('at') }} ${time}</div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Poll every 5 seconds
        setInterval(checkLatestAttendance, 5000);
    </script>
</body>
</html>

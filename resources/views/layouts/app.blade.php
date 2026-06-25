<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZKTeco Command Center</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b1120; /* Darker, richer navy */
            --sidebar-bg: rgba(15, 23, 42, 0.6);
            --card-bg: rgba(30, 41, 59, 0.4);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-glow: rgba(59, 130, 246, 0.15);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.5);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #f43f5e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
        }

        .sidebar.collapsed { width: 70px; }
        .sidebar.collapsed .brand-text, 
        .sidebar.collapsed .nav-text, 
        .sidebar.collapsed .nav-section-title { display: none; }
        .sidebar.collapsed .brand { justify-content: center; padding: 25px 0; }
        .sidebar.collapsed .nav-item { justify-content: center; padding: 15px 0; }

        .brand {
            padding: 25px 20px;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            letter-spacing: -0.5px;
        }

        .brand i.main-icon {
            color: var(--accent);
            font-size: 1.5rem;
        }
        
        .sidebar-toggle {
            position: absolute;
            right: -15px;
            top: 25px;
            width: 30px;
            height: 30px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            border: 2px solid var(--bg-color);
            z-index: 101;
            transition: transform 0.3s;
        }
        .sidebar.collapsed .sidebar-toggle { transform: rotate(180deg); }

        .nav-menu {
            padding: 20px 0;
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .nav-menu::-webkit-scrollbar { width: 4px; }
        .nav-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .nav-item {
            margin: 5px 15px;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            white-space: nowrap;
            border-radius: 12px;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
            transform: translateX(4px);
        }
        
        .nav-item.active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
            color: var(--accent);
            border: 1px solid rgba(59, 130, 246, 0.3);
            box-shadow: 0 4px 15px var(--glass-glow);
        }

        .sidebar.collapsed .nav-item {
            margin: 5px 10px;
            justify-content: center;
        }
        .sidebar.collapsed .nav-item:hover { transform: translateY(-2px); }

        /* RTL Support for borders */
        [dir="rtl"] .sidebar { border-right: none; border-left: 1px solid var(--glass-border); right: 0; left: auto; }
        [dir="rtl"] .sidebar-toggle { right: auto; left: -15px; }
        [dir="rtl"] .sidebar.collapsed .sidebar-toggle { transform: rotate(0deg); }
        [dir="rtl"] .main-content { margin-left: 0; margin-right: 260px; }
        [dir="rtl"] .main-content.expanded { margin-right: 70px; }
        [dir="rtl"] .nav-item:hover { transform: translateX(-4px); }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
            max-width: calc(100vw - 260px);
            box-sizing: border-box;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1), margin-right 0.4s cubic-bezier(0.4, 0, 0.2, 1), width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-image: 
                radial-gradient(circle at 15% 10%, rgba(59, 130, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 90%, rgba(139, 92, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.05) 0%, transparent 60%);
            background-attachment: fixed;
        }
        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
            max-width: calc(100vw - 70px);
        }
        
        .page-title {
            margin-bottom: 30px;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Glassmorphism Utilities */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4), 0 0 20px var(--glass-glow);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .glass-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s;
        }
        .glass-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: rgba(0, 0, 0, 0.3);
        }

        .glass-btn {
            background: linear-gradient(135deg, var(--accent), #6366f1);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .glass-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
            filter: brightness(1.1);
        }

        /* Global Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .empty-state i {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--text-muted), transparent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .empty-state p { font-size: 1.1rem; font-weight: 500; }

        /* Global Pagination */
        nav { display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 30px; }
        nav > div:first-child, nav p, nav .text-sm, nav > div > div:first-child { display: none !important; }
        nav > div:last-child { display: flex; justify-content: center; width: 100%; }
        nav > div > div:last-child { display: flex; justify-content: center; width: 100%; }
        .pagination, nav span.relative.z-0.inline-flex { 
            display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 8px; 
            background: transparent; border: none; overflow: visible;
        }
        .pagination li, nav span.relative.z-0.inline-flex > span, nav span.relative.z-0.inline-flex > a { display: inline-flex; border: none !important; }
        .pagination li a, .pagination li span, nav span.relative.z-0.inline-flex > span > span, nav span.relative.z-0.inline-flex > a { 
            width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 255, 255, 0.05); color: #cbd5e1; 
            text-decoration: none; font-size: 0.95rem; font-weight: 600; transition: all 0.3s; 
            display: flex; align-items: center; justify-content: center; border: 1px solid var(--glass-border);
        }
        .pagination li a:hover, nav span.relative.z-0.inline-flex > a:hover { 
            background: rgba(255, 255, 255, 0.1); color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .pagination li.active span, nav span.relative.z-0.inline-flex > span[aria-current="page"] > span { 
            background: linear-gradient(135deg, var(--accent), #6366f1); color: white; border: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

    </style>
    @stack('styles')
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="fas fa-fingerprint main-icon"></i>
            <span class="brand-text">SenseFace ADMS</span>
            <div class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-chevron-left" id="sidebar-toggle-icon"></i>
            </div>
        </div>
        <div class="nav-menu">
            <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}" title="{{ __('Dashboard') }}">
                <i class="fas fa-chart-pie"></i> <span class="nav-text">{{ __('Dashboard') }}</span>
            </a>
            <a href="/devices" class="nav-item {{ request()->is('devices') ? 'active' : '' }}" title="{{ __('SIP Devices') }}">
                <i class="fas fa-satellite-dish"></i> <span class="nav-text">{{ __('SIP Devices') }}</span>
            </a>
            <a href="/employees" class="nav-item {{ request()->is('employees*') ? 'active' : '' }}" title="{{ __('Employees Directory') }}">
                <i class="fas fa-users"></i> <span class="nav-text">{{ __('Employees Directory') }}</span>
            </a>
            <a href="/schedules" class="nav-item {{ request()->is('schedules*') ? 'active' : '' }}" title="{{ __('Schedules & Shifts') }}">
                <i class="fas fa-calendar-alt"></i> <span class="nav-text">{{ __('Schedules & Shifts') }}</span>
            </a>
            <a href="/attendance" class="nav-item {{ request()->is('attendance*') ? 'active' : '' }}" title="{{ __('Attendance Logs') }}">
                <i class="fas fa-list-check"></i> <span class="nav-text">{{ __('Attendance Logs') }}</span>
            </a>
            
            <div class="nav-section-title" style="margin-top: 20px; margin-bottom: 10px; padding-left: 20px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 1px;">
                {{ __('Reports') }}
            </div>
            <a href="/reports/absence" class="nav-item {{ request()->is('reports/absence*') ? 'active' : '' }}" title="{{ __('Absence Report') }}">
                <i class="fas fa-user-xmark"></i> <span class="nav-text">{{ __('Absence Report') }}</span>
            </a>
            
            <div class="nav-section-title" style="margin-top: 20px; margin-bottom: 10px; padding-left: 20px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 1px;">
                {{ __('System') }}
            </div>
            <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}" title="{{ __('Settings') }}">
                <i class="fas fa-cog"></i> <span class="nav-text">{{ __('Settings') }}</span>
            </a>
            <a href="/settings/sip" class="nav-item {{ request()->is('settings/sip') ? 'active' : '' }}" title="{{ __('SIP / Intercom Settings') }}">
                <i class="fas fa-phone-alt"></i> <span class="nav-text">{{ __('SIP / Intercom Settings') }}</span>
            </a>

            @if(Auth::guard('admin')->check())
                <div style="margin-top: auto; padding: 20px 15px;">
                    <div style="margin-bottom: 20px; padding: 0 10px;" class="nav-text">
                        <span style="color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">{{ __('Language') }}</span>
                        <div style="display: flex; gap: 8px; margin-top: 8px;">
                            <a href="/lang/en" style="flex: 1; text-align: center; padding: 6px 0; background: {{ session('locale', 'en') == 'en' ? 'var(--accent)' : 'rgba(255,255,255,0.05)' }}; color: white; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid {{ session('locale', 'en') == 'en' ? 'var(--accent)' : 'rgba(255,255,255,0.1)' }};">EN</a>
                            <a href="/lang/ar" style="flex: 1; text-align: center; padding: 6px 0; background: {{ session('locale') == 'ar' ? 'var(--accent)' : 'rgba(255,255,255,0.05)' }}; color: white; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid {{ session('locale') == 'ar' ? 'var(--accent)' : 'rgba(255,255,255,0.1)' }};">AR</a>
                            <a href="/lang/fr" style="flex: 1; text-align: center; padding: 6px 0; background: {{ session('locale') == 'fr' ? 'var(--accent)' : 'rgba(255,255,255,0.05)' }}; color: white; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid {{ session('locale') == 'fr' ? 'var(--accent)' : 'rgba(255,255,255,0.1)' }};">FR</a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="nav-item" style="width: 100%; border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.1); color: #f87171; border-radius: 12px; cursor: pointer; justify-content: flex-start; margin: 0; padding: 12px 15px;" title="{{ __('Logout') }}">
                            <i class="fas fa-sign-out-alt"></i> <span class="nav-text">{{ __('Logout') }}</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div class="main-content" id="main-content" dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}">
        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
        }
        
        // Load state
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('main-content').classList.add('expanded');
            }
        });
    </script>

    <!-- SIP Video Intercom Modal -->
    <script>
        window.SIP_CONFIG = {
            uri: "sip:100@192.168.0.107",
            password: "dashboardpass",
            wsServer: "ws://192.168.0.107:8088/ws",
            snapshotUrl: "http://192.168.0.105/cgi-bin/snapshot.cgi"
        };
    </script>
    <div id="sip-call-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
        <div class="glass-card" style="padding: 40px; text-align: center; max-width: 450px; width: 100%;">
            <div style="width: 80px; height: 80px; background: rgba(59, 130, 246, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: var(--accent); font-size: 2.2rem; box-shadow: 0 0 20px var(--accent-glow);">
                <i class="fas fa-phone-volume fa-shake"></i>
            </div>
            <h2 style="color: white; margin-bottom: 10px; font-weight: 800;">{{ __('Incoming Intercom Call') }}</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 1.1rem;">ZKTeco SenseFace Device</p>
            <p id="sip-call-timer" style="color: var(--accent); font-weight: bold; font-size: 1.8rem; margin-bottom: 25px; display: none;">00:00</p>
            
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

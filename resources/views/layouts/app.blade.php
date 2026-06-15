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
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="/devices" class="nav-item {{ request()->is('devices') ? 'active' : '' }}">
                <i class="fas fa-satellite-dish"></i> Devices
            </a>
            <a href="/employees" class="nav-item {{ request()->is('employees*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Employees
            </a>
            <a href="/schedules" class="nav-item {{ request()->is('schedules*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Schedules & Shifts
            </a>
            <a href="/roster" class="nav-item {{ request()->is('roster*') ? 'active' : '' }}">
                <i class="fas fa-calendar-days"></i> Monthly Roster
            </a>
            <a href="/attendance" class="nav-item {{ request()->is('attendance*') ? 'active' : '' }}">
                <i class="fas fa-list-check"></i> Attendance
            </a>
            
            <div style="margin-top: 20px; margin-bottom: 10px; padding-left: 20px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 1px;">
                Reports
            </div>
            <a href="/reports/absence" class="nav-item {{ request()->is('reports/absence*') ? 'active' : '' }}">
                <i class="fas fa-user-xmark"></i> Absence Report
            <div style="margin-top: 20px; margin-bottom: 10px; padding-left: 20px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 1px;">
                System
            </div>
            <a href="/settings" class="nav-item {{ request()->is('settings*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>


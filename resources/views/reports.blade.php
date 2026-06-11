@extends('layouts.app')
@section('content')
<div id="reports-page" class="page">
            <!-- Filter Panel -->
            <div class="card" style="margin-bottom: 25px;">
                <div class="section-header">
                    <h2><i class="fas fa-filter"></i> Attendance Report Filter</h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; align-items: flex-end;">
                    <div class="form-group">
                        <label>Select Period Mode</label>
                        <select id="report-mode" class="form-control" onchange="app.toggleReportPeriodInputs()" style="width: 100%; background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; font-size: 0.9rem; outline: none; cursor: pointer;">
                            <option value="day">Daily</option>
                            <option value="month">Monthly</option>
                            <option value="year">Yearly</option>
                            <option value="range">Date Range</option>
                        </select>
                    </div>

                    <!-- Daily Date Picker -->
                    <div class="form-group" id="report-date-group">
                        <label>Select Date:</label>
                        <div style="display:flex; gap:10px">
                            <input type="date" id="report-date" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; flex: 1; font-size: 0.9rem; outline: none;">
                            <button class="btn btn-secondary" id="report-today-btn" onclick="app.setReportToday()" style="padding: 0 15px;">Today</button>
                        </div>
                    </div>

                    <!-- Monthly Month Picker -->
                    <div class="form-group" id="report-month-group" style="display: none;">
                        <label>Select Month</label>
                        <input type="month" id="report-month" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; width: 100%; font-size: 0.9rem; outline: none;">
                    </div>

                    <!-- Yearly Year Picker -->
                    <div class="form-group" id="report-year-group" style="display: none;">
                        <label>Select Year</label>
                        <select id="report-year" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; width: 100%; font-size: 0.9rem; outline: none;">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                        </select>
                    </div>

                    <!-- Range Date Picker -->
                    <div class="form-group" id="report-range-group" style="display: none; grid-column: span 2;">
                        <label>Period (From Date - To Date)</label>
                        <div style="display:flex; gap:10px; align-items: center;">
                            <input type="date" id="report-start-date" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; flex: 1; font-size: 0.9rem; outline: none;">
                            <span style="color: var(--text-muted); font-size: 0.85rem;">to</span>
                            <input type="date" id="report-end-date" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; flex: 1; font-size: 0.9rem; outline: none;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Default Start Time</label>
                        <input type="time" id="report-start-time" value="08:00" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; width: 100%; font-size: 0.9rem; outline: none;">
                    </div>

                    <div class="form-group">
                        <label>Default End Time</label>
                        <input type="time" id="report-end-time" value="17:00" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; width: 100%; font-size: 0.9rem; outline: none;">
                    </div>

                    <div>
                        <button class="btn btn-primary btn-full" onclick="app.generateLateReport()" style="padding: 12px; font-weight: 600;">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Row Dashboard -->
            <div id="reports-kpi-row" class="kpi-grid" style="display: none; margin-bottom: 25px;">
                <!-- Dynamically Injected Metrics Cards -->
            </div>

            <!-- Report Results Table Container -->
            <div class="card">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <h2 id="report-table-title"><i class="fas fa-list-alt"></i> Attendance Summary</h2>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select id="report-status-filter" onchange="app.filterLateReport()" style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; outline: none; cursor: pointer; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='var(--border-color)'">
                            <option value="all">All Statuses</option>
                            <option value="Late">Late Only 🔴</option>
                            <option value="On Time">On Time Only 🟢</option>
                            <option value="Absent">Absent Only 📁</option>
                        </select>
                        <div class="search-box" style="position: relative; width: 220px;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                            <input type="text" id="report-search-input" oninput="app.filterLateReport()" placeholder="Search by name or ID..." style="background: var(--bg-slate-900); color: var(--text-main); border: 1px solid var(--border-color); padding: 8px 12px 8px 35px; border-radius: 8px; font-size: 0.85rem; width: 100%; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='var(--border-color)'">
                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table id="late-report-table">
                        <thead>
                            <!-- Dynamic Table Head -->
                        </thead>
                        <tbody>
                            <tr><td class="text-center p-4 text-slate-500">Please select period and generate report</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Late Report Pagination Controls -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 0 5px;">
                    <div style="color: var(--text-muted); font-size: 0.85rem;" id="reports-page-info">Showing 0 to 0 of 0 entries</div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;" onclick="app.prevReportsPage()">
                            <i class="fas fa-chevron-left"></i> Prev
                        </button>
                        <button class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;" onclick="app.nextReportsPage()">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Page -->
        
@endsection

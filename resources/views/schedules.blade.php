@extends('layouts.app')
@section('content')
<div id="schedules-page" class="page">
            <!-- Sliding tab selector at the top -->
            <div class="schedules-tabs">
                <button class="schedule-tab-btn active" data-tab="tab-define-shifts">
                    <i class="fas fa-clock" style="color: #818cf8;"></i> Define Shifts
                </button>
                <button class="schedule-tab-btn" data-tab="tab-work-patterns">
                    <i class="fas fa-project-diagram" style="color: #10b981;"></i> Work Patterns
                </button>
                <button class="schedule-tab-btn" data-tab="tab-daily-schedule">
                    <i class="fas fa-calendar-day" style="color: #a855f7;"></i> Exceptions / Overrides
                </button>
                <button class="schedule-tab-btn" data-tab="tab-bulk-schedule">
                    <i class="fas fa-layer-group" style="color: #f472b6;"></i> Bulk Exceptions
                </button>
            </div>

            <!-- Tab 1: Define Shifts (الورديات) -->
            <div id="tab-define-shifts" class="schedule-tab-content active">
                <div class="schedules-two-col">
                    <!-- Define New Shift Form -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="section-header" style="margin-bottom: 20px;">
                            <h2 style="font-size: 1.25rem;"><i class="fas fa-plus-circle text-indigo-400"></i> Define New Shift</h2>
                        </div>
                        <form id="add-shift-form" style="margin-bottom: 0;">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Shift Name</label>
                                <input type="text" id="shift-name" placeholder="e.g. Morning Shift / Afternoon Shift" required style="padding: 12px; font-size: 0.95rem;">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label>Start Time</label>
                                    <input type="time" id="shift-start" required style="padding: 12px; font-size: 0.95rem;">
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label>End Time</label>
                                    <input type="time" id="shift-end" required style="padding: 12px; font-size: 0.95rem;">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-full" style="padding: 12px; font-size: 0.95rem;">
                                <i class="fas fa-plus"></i> Save Shift
                            </button>
                        </form>
                    </div>

                    <!-- Defined Shifts List -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="section-header" style="margin-bottom: 20px;">
                            <h2 style="font-size: 1.25rem;"><i class="fas fa-list text-indigo-400"></i> Defined Shifts</h2>
                        </div>
                        <div class="info-alert-card" style="margin-bottom: 20px;">
                            <i class="fas fa-info-circle"></i>
                            <p>Here you can manage your work shifts. Deleting a shift will revert any employees currently assigned to it back to the default work schedule.</p>
                        </div>
                        <div>
                            <div id="shifts-list-container" style="display: flex; flex-direction: column; gap: 10px; max-height: 300px; overflow-y: auto; background: rgba(0, 0, 0, 0.15); border: 1px solid var(--glass-border); border-radius: 16px; padding: 15px;">
                                <!-- Dynamically injected shifts -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Work Patterns -->
            <div id="tab-work-patterns" class="schedule-tab-content">
                <div class="schedules-two-col">
                    <!-- Create Pattern Form -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="section-header" style="margin-bottom: 20px;">
                            <h2 style="font-size: 1.25rem;"><i class="fas fa-network-wired text-emerald-400"></i> Create Work Pattern</h2>
                        </div>
                        <form id="add-pattern-form" style="margin-bottom: 0;">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Pattern Name</label>
                                <input type="text" id="pattern-name" placeholder="e.g. Night Shift Guard, Management..." required style="padding: 12px; font-size: 0.95rem;">
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 25px;" id="pattern-days-container">
                                <!-- Days will be injected here: Sunday to Saturday -->
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-full" style="padding: 12px; font-size: 0.95rem;">
                                <i class="fas fa-plus"></i> Save Pattern
                            </button>
                        </form>
                    </div>

                    <!-- Defined Patterns List -->
                    <div class="card" style="margin-bottom: 0;">
                        <div class="section-header" style="margin-bottom: 20px;">
                            <h2 style="font-size: 1.25rem;"><i class="fas fa-list text-emerald-400"></i> Defined Patterns</h2>
                        </div>
                        <div class="info-alert-card" style="margin-bottom: 20px;">
                            <i class="fas fa-info-circle"></i>
                            <p>Work Patterns define the weekly routine for an employee. Once assigned to a user, they apply indefinitely.</p>
                        </div>
                        <div>
                            <div id="patterns-list-container" style="display: flex; flex-direction: column; gap: 10px; max-height: 500px; overflow-y: auto; background: rgba(0, 0, 0, 0.15); border: 1px solid var(--glass-border); border-radius: 16px; padding: 15px;">
                                <!-- Dynamically injected patterns -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Daily Schedule (Exceptions) -->
            <div id="tab-daily-schedule" class="schedule-tab-content">
                <div class="card" style="margin-bottom: 0;">
                    <div class="search-filter-bar">
                        <div>
                            <h2 style="font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-calendar-check text-purple-400"></i> Daily Assignments
                            </h2>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Assign work shifts or off days to employees on a day-by-day basis. Changes are saved automatically.</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; background: rgba(0, 0, 0, 0.2); border: 1px solid var(--glass-border); padding: 8px 15px; border-radius: 12px;">
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Select Date:</span>
                            <input type="date" id="scheduler-view-date" style="width: auto; padding: 6px 12px; border-radius: 8px; font-size: 0.9rem; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; cursor: pointer;">
                        </div>
                    </div>

                    <div class="table-container" style="max-height: 480px; overflow-y: auto; background: rgba(0, 0, 0, 0.15); border: 1px solid var(--glass-border); border-radius: 16px; padding: 15px;">
                        <table id="daily-scheduler-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="font-size: 0.8rem; padding: 12px; text-align: left;">Employee Name</th>
                                    <th style="font-size: 0.8rem; padding: 12px; width: 100px; text-align: center;">ID</th>
                                    <th style="font-size: 0.8rem; padding: 12px; text-align: right; width: 220px;">Assigned Shift</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamically injected employee list and quick shift selector dropdowns -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Bulk Assignment Form (الجدولة الجماعية) -->
            <div id="tab-bulk-schedule" class="schedule-tab-content">
                <div class="schedules-two-col" style="grid-template-columns: 1.4fr 1.1fr;">
                    <!-- Step 1: Targets & Dates -->
                    <div class="card" style="margin-bottom: 0; padding: 25px;">
                        <div class="section-header" style="margin-bottom: 20px;">
                            <h2 style="font-size: 1.25rem;"><i class="fas fa-users text-pink-400"></i> Step 1: Select Employees & Dates</h2>
                        </div>

                        <!-- Date Range Selection -->
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label style="font-size: 0.9rem; margin-bottom: 8px; font-weight: 600;">Date Range</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 5px;">Start Date</span>
                                    <input type="date" id="bulk-start-date" required style="padding: 10px; font-size: 0.85rem; cursor: pointer;">
                                </div>
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 5px;">End Date</span>
                                    <input type="date" id="bulk-end-date" required style="padding: 10px; font-size: 0.85rem; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <!-- Target Employee Checkboxes -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <label style="font-size: 0.9rem; margin-bottom: 0; font-weight: 600;">Target Employees</label>
                                <div style="display: flex; gap: 8px;">
                                    <button type="button" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; margin: 0;" onclick="app.selectAllBulkEmployees(true)">Select All</button>
                                    <button type="button" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; margin: 0;" onclick="app.selectAllBulkEmployees(false)">Clear Selection</button>
                                </div>
                            </div>
                            <div id="bulk-employees-list" class="employee-checkbox-list">
                                <!-- Dynamic checkbox list of employees -->
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Select Schedule & Apply -->
                    <div class="card" style="margin-bottom: 0; padding: 25px; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="section-header" style="margin-bottom: 20px;">
                                <h2 style="font-size: 1.25rem;"><i class="fas fa-cog text-pink-400"></i> Step 2: Assign & Apply</h2>
                            </div>

                            <div class="info-alert-card" style="margin-bottom: 25px;">
                                <i class="fas fa-magic"></i>
                                <p>Bulk schedule allows you to overwrite the shifts of multiple employees across a chosen period of time. This is perfect for setting weekends or assigning team shifts.</p>
                            </div>

                            <div class="form-group" style="margin-bottom: 30px;">
                                <label style="font-size: 0.9rem; margin-bottom: 8px; font-weight: 600;">Select Work Shift / Off Day</label>
                                <select id="bulk-shift-select" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--glass-border); color: white; padding: 12px; border-radius: 12px; font-size: 0.95rem; cursor: pointer; width: 100%;">
                                    <option value="none">Default (08:00 - 17:00) ⚙️</option>
                                    <option value="off">Off Day / Weekend 😴</option>
                                    <!-- Dynamic options injected -->
                                </select>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-full" onclick="app.applyBulkSchedule()" style="padding: 14px; font-size: 1rem; border-radius: 12px; margin-top: 20px; box-shadow: 0 4px 20px rgba(168, 85, 247, 0.25);">
                            <i class="fas fa-check"></i> Apply Bulk Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Logs Page -->
        
@endsection

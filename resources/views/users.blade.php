@extends('layouts.app')
@section('content')
<div id="users-page" class="page">
            <div class="card" style="margin-bottom: 30px;">
                <div class="section-header">
                    <h2><i class="fas fa-user-plus"></i> Add New Employee</h2>
                </div>
                <form id="add-user-form">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 25px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Full Name</label>
                            <input type="text" id="user-name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Hardware ID (UID)</label>
                            <input type="number" id="user-id" placeholder="101" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Target Device</label>
                            <select id="user-target-device" required></select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Role</label>
                            <select id="user-role">
                                <option value="0">Regular User</option>
                                <option value="14">Administrator</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Work Pattern</label>
                            <select id="user-work-pattern">
                                <option value="">None (Use Default)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Password <span style="font-size: 0.75rem; color: var(--text-muted);">(Optional)</span></label>
                            <input type="password" id="user-password" placeholder="e.g. 12345">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Card Code <span style="font-size: 0.75rem; color: var(--text-muted);">(Optional)</span></label>
                            <input type="text" id="user-card" placeholder="e.g. 55667788">
                        </div>
                    </div>
                    <div style="display:flex; gap:15px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" id="user-cancel-btn" style="display:none; padding: 10px 30px;" onclick="app.resetUserForm()">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="user-submit-btn" style="padding: 10px 30px;">Create User on Device</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                    <h2><i class="fas fa-users"></i> Employee Directory</h2>
                    <div style="display:flex; gap:10px; align-items: center;">
                        <div class="search-box" style="position: relative; width: 250px;">
                            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                            <input type="text" id="users-search" placeholder="Search employees..." style="background: rgba(0,0,0,0.2); color: var(--text-main); border: 1px solid var(--glass-border); padding: 10px 15px 10px 40px; border-radius: 12px; font-size: 0.9rem; width: 100%; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--accent-primary)'" onblur="this.style.borderColor='var(--glass-border)'" oninput="app.handleUsersSearch(this.value)">
                        </div>
                        <button class="btn btn-secondary" id="sync-all-users-btn" onclick="app.syncAllUsers()"><i class="fas fa-download"></i> Fetch All</button>
                        <button class="btn btn-secondary" id="sync-all-bio-btn" onclick="app.syncAllBio()"><i class="fas fa-fingerprint"></i> Sync Bio</button>
                    </div>
                </div>
                <div class="table-container">
                    <table id="users-table">
                        <thead>
                            <tr>
                                <th>Employee Info</th>
                                <th>Assignment</th>
                                <th>Credentials</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data injection -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Users Pagination Controls -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 0 5px;">
                    <div style="color: var(--text-muted); font-size: 0.85rem;" id="users-page-info">Showing 0 to 0 of 0 entries</div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;" onclick="app.prevUsersPage()">
                            <i class="fas fa-chevron-left"></i> Prev
                        </button>
                        <button class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;" onclick="app.nextUsersPage()">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shifts & Schedules Page -->
        
@endsection

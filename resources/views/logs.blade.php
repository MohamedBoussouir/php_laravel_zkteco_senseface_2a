@extends('layouts.app')
@section('content')
<div id="logs-page" class="page">
            <div class="card">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2><i class="fas fa-history"></i> Attendance Records</h2>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-red" id="clear-all-logs-btn" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 8px; margin: 0; background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">
                            <i class="fas fa-trash-alt mr-1"></i> Clear All Logs
                        </button>
                        <button class="btn btn-secondary" id="sync-logs-only-btn" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.85rem; padding: 6px 12px; border-radius: 8px; margin: 0;">
                            <i class="fas fa-bolt"></i> Fast Sync Logs
                        </button>
                    </div>
                </div>
                <div class="search-filter-bar" style="margin-bottom: 15px; display: flex; gap: 15px; align-items: center; background: rgba(0, 0, 0, 0.15); padding: 15px; border-radius: 12px; border: 1px solid var(--glass-border); flex-wrap: wrap;">
                    <div class="search-box" style="position: relative; flex: 1; min-width: 250px;">
                        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                        <input type="text" id="logs-search-input" placeholder="Search by name or ID..." style="background: rgba(0,0,0,0.2); color: var(--text-main); border: 1px solid var(--glass-border); padding: 10px 15px 10px 40px; border-radius: 10px; font-size: 0.9rem; width: 100%; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--accent-primary)'" onblur="this.style.borderColor='var(--glass-border)'">
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Date:</span>
                        <input type="date" id="logs-date-filter" style="background: rgba(0,0,0,0.2); color: var(--text-main); border: 1px solid var(--glass-border); padding: 8px 12px; border-radius: 10px; font-size: 0.9rem; outline: none; cursor: pointer;">
                        <button class="btn btn-secondary" id="clear-logs-filter-btn" style="padding: 8px 15px; border-radius: 10px; margin: 0;">
                            <i class="fas fa-times-circle"></i> Clear Filters
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table id="logs-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>User ID</th>
                                <th>Timestamp</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data injection -->
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div id="pagination-controls" style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 10px; background: rgba(0, 0, 0, 0.15); border-radius: 8px; border: 1px solid var(--glass-border);">
                    <div id="pagination-info" style="font-size: 0.85rem; color: var(--text-muted);">
                        Showing 0 to 0 of 0 entries
                    </div>
                    <div style="display: flex; gap: 5px; align-items: center;" id="pagination-buttons">
                        <button class="btn btn-secondary btn-sm" id="prev-page-btn" disabled>Previous</button>
                        <div id="page-numbers" style="display: flex; gap: 5px; align-items: center;"></div>
                        <button class="btn btn-secondary btn-sm" id="next-page-btn" disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Devices Page -->
        
@endsection

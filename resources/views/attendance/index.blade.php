@extends('layouts.app')

@push('styles')
<style>
    .card {
        padding: 25px;
        margin-bottom: 30px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .section-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
    }
    
    .search-filter-bar {
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        align-items: center;
        padding: 20px;
        flex-wrap: wrap;
    }
    
    .search-box {
        position: relative;
        flex: 1;
        min-width: 250px;
    }
    .search-box input {
        padding-left: 40px;
        width: 100%;
    }
    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .table-container {
        border-radius: 16px;
        overflow-x: auto;
    }
    table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 700px; }
    th, td { padding: 15px 20px; text-align: left; }
    th { background: rgba(0,0,0,0.3); color: var(--text-muted); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--glass-border); }
    td { color: var(--text-main); font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.02); transition: background 0.3s; }
    tr:hover td { background: rgba(255,255,255,0.03); }
    tr:last-child td { border-bottom: none; }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        display: inline-block;
        letter-spacing: 0.5px;
    }
    .badge-checkin { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
    .badge-checkout { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
    .badge-break { background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.4); }
    .badge-overtime { background: rgba(168, 85, 247, 0.2); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
    .badge-unknown { background: rgba(255, 255, 255, 0.1); color: var(--text-muted); border: 1px solid var(--border-color); }
    
    @media (max-width: 768px) {
        .section-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        .section-header > div { flex-wrap: wrap; }
        .search-filter-bar { flex-direction: column; align-items: stretch; }
        .search-box { width: 100%; }
        .table-container { border-radius: 8px; }
    }

    /* Global pagination from app.blade.php applies here */
    .dropdown-menu.show { display: block !important; }
    .dropdown-menu a:hover { background: rgba(255,255,255,0.05) !important; }
</style>
@endpush

@push('scripts')
<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    var menu = document.getElementById('exportMenu');
    if (menu && menu.classList.contains('show') && !event.target.closest('.dropdown-menu') && !event.target.closest('button[onclick*="exportMenu"]')) {
        menu.classList.remove('show');
    }
});
</script>
@endpush

@section('content')

<div class="card glass-card">
    <div class="section-header">
        <h2><i class="fas fa-history text-blue-400"></i> {{ __('Attendance Records') }}</h2>
        <div style="display: flex; gap: 10px;">
            <form action="/attendance/clear" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete all attendance records from the database and send a CLEAR LOG command to all devices? This action cannot be undone.') }}');" style="margin: 0;">
                @csrf
                <button type="submit" class="glass-btn" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); box-shadow: none;">
                    <i class="fas fa-trash-alt"></i> {{ __('Clear All Logs') }}
                </button>
            </form>
            <form action="/attendance/fetch" method="POST" onsubmit="return confirm('{{ __('This will send a command to all connected devices to fetch new logs. Proceed?') }}');" style="margin: 0;">
                @csrf
                <button type="submit" class="glass-btn" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); box-shadow: none;">
                    <i class="fas fa-bolt"></i> {{ __('Fast Sync Logs') }}
                </button>
            </form>
        </div>
    </div>
    
    <form id="autoSearchForm" action="/attendance" method="GET">
        <div class="search-filter-bar glass-card" style="position: relative; z-index: 999;">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input id="autoSearchInput" type="text" class="glass-input" name="search" value="{{ $search }}" placeholder="{{ __('Search by name or ID...') }}">
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">{{ __('From:') }}</span>
                <input type="date" name="start_date" class="glass-input" value="{{ $startDate }}">
                
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">{{ __('To:') }}</span>
                <input type="date" name="end_date" class="glass-input" value="{{ $endDate }}">
                
                <button type="submit" class="glass-btn">
                    <i class="fas fa-filter"></i> {{ __('Filter') }}
                </button>
                <a href="/attendance" class="glass-btn" style="background: transparent; box-shadow: none; border: 1px solid var(--glass-border);">
                    {{ __('Reset') }}
                </a>
                
                <div style="position: relative; z-index: 999;">
                    <button type="button" class="glass-btn" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); box-shadow: none;" onclick="document.getElementById('exportMenu').classList.toggle('show')">
                        <i class="fas fa-download"></i> {{ __('Export') }} <i class="fas fa-chevron-down" style="font-size: 0.8em; margin-left: 5px;"></i>
                    </button>
                    <div id="exportMenu" class="dropdown-menu" style="display: none; position: absolute; top: 100%; right: 0; background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 12px; margin-top: 5px; z-index: 100; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5); min-width: 180px; ">
                        <a href="{{ route('attendance.export.pdf', request()->all()) }}" target="_blank" style="display: block; padding: 12px 15px; color: white; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.2s;">
                            <i class="fas fa-file-pdf" style="color: #f43f5e; width: 20px;"></i> {{ __('Export PDF') }}
                        </a>
                        <a href="{{ route('attendance.export.excel', request()->all()) }}" style="display: block; padding: 12px 15px; color: white; text-decoration: none; transition: 0.2s;">
                            <i class="fas fa-file-excel" style="color: #10b981; width: 20px;"></i> {{ __('Export Excel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('User ID') }}</th>
                    <th>{{ __('Timestamp') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th style="text-align: right;">{{ __('Device ID') }}</th>
                    <th style="text-align: right; width: 60px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr>
                        <td>
                            <div style="font-weight: 600; font-size: 1rem; color: white;">{{ $att->user->name ?? $att->user_name ?? __('Unknown') }}</div>
                        </td>
                        <td>
                            <div style="color: var(--text-muted);">{{ $att->user_id }}</div>
                        </td>
                        <td>
                            <div style="color: white; font-weight: 500;">{{ date('Y-m-d', strtotime($att->timestamp)) }}</div>
                            <div style="color: var(--text-muted); font-size: 0.85rem;">{{ date('H:i:s', strtotime($att->timestamp)) }}</div>
                        </td>
                        <td>
                            @if(isset($att->calculated_status))
                                @if($att->calculated_status == 'Check-In')
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 10px; border-radius: 6px;"><i class="fas fa-arrow-right-to-bracket"></i> {{ __('Check-In (Arrivée)') }}</span>
                                @elseif($att->calculated_status == 'Check-Out')
                                    <span class="badge" style="background: rgba(244, 63, 94, 0.15); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); padding: 5px 10px; border-radius: 6px;"><i class="fas fa-arrow-right-from-bracket"></i> {{ __('Check-Out (Départ)') }}</span>
                                @else
                                    <span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #94a3b8; border: 1px solid rgba(100, 116, 139, 0.3); padding: 5px 10px; border-radius: 6px;"><i class="fas fa-clock"></i> {{ __('Intermediate Log') }}</span>
                                @endif
                            @else
                                @if($att->status == 0 || $att->status == '0' || $att->status == 'Check-In')
                                    <span class="badge badge-checkin"><i class="fas fa-arrow-right-to-bracket"></i> {{ __('Check-In (Arrivée)') }}</span>
                                @elseif($att->status == 1 || $att->status == '1' || $att->status == 'Check-Out')
                                    <span class="badge badge-checkout"><i class="fas fa-arrow-right-from-bracket"></i> {{ __('Check-Out (Départ)') }}</span>
                                @elseif($att->status == 2 || $att->status == '2')
                                    <span class="badge badge-break"><i class="fas fa-mug-hot"></i> {{ __('Break-Out') }}</span>
                                @elseif($att->status == 3 || $att->status == '3')
                                    <span class="badge badge-break"><i class="fas fa-mug-hot"></i> {{ __('Break-In') }}</span>
                                @elseif($att->status == 4 || $att->status == '4')
                                    <span class="badge badge-overtime"><i class="fas fa-clock"></i> {{ __('OT-In') }}</span>
                                @elseif($att->status == 5 || $att->status == '5')
                                    <span class="badge badge-overtime"><i class="fas fa-clock"></i> {{ __('OT-Out') }}</span>
                                @else
                                    <span class="badge badge-unknown">{{ $att->status }}</span>
                                @endif
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="color: var(--text-muted); font-family: monospace;">{{ $att->device_id ?? __('Unknown') }}</div>
                        </td>
                        <td style="text-align: right;">
                            <form action="/attendance/{{ $att->id }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this record from the database?');" style="margin: 0;">
                                @csrf
                                <button type="submit" class="glass-btn" style="padding: 6px 10px; background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); font-size: 0.85rem; box-shadow: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-history" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>{{ __('No attendance records found.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($attendances->hasPages())
        {{ $attendances->appends(request()->query())->links('pagination::bootstrap-5') }}
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('autoSearchInput');
        const searchForm = document.getElementById('autoSearchForm');
        let debounceTimer;

        if (searchInput) {
            const urlParams = new URLSearchParams(window.location.search);
            if (searchInput.value || urlParams.has('search')) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    searchForm.submit();
                }, 600);
            });
        }
    });
</script>
@endpush
@endsection

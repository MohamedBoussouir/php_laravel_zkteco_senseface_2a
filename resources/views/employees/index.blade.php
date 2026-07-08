@extends('layouts.app')

@push('styles')
<style>
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .list-container { overflow: hidden; display: flex; flex-direction: column; }
    .user-item { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); gap: 15px; }
    .user-item:hover { background: rgba(255,255,255,0.02); }
    .user-item:last-child { border-bottom: none; }
    
    .col-profile { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 200px; }
    .avatar { width: 42px; height: 42px; border-radius: 50%; border: 2px solid #3b82f6; overflow: hidden; flex-shrink: 0; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: rgba(255,255,255,0.3); }
    .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .user-name { font-weight: 600; font-size: 1rem; color: #fff; margin-bottom: 2px; }
    .user-id { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }
    
    .col-badges { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 200px; align-items: flex-start; }
    .badge-row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .badge-device { background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.2); }
    .badge-role-user { background: rgba(100, 116, 139, 0.15); color: #94a3b8; border: 1px solid rgba(100, 116, 139, 0.3); }
    .badge-role-admin { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
    .schedule-text { color: #fff; font-size: 0.85rem; font-weight: 600; }
    
    .col-indicators { display: flex; gap: 6px; flex: 1; min-width: 150px; }
    .indicator { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; }
    .ind-fp { background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.2); }
    .ind-face { background: rgba(168, 85, 247, 0.1); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.2); }
    .ind-pass { background: rgba(250, 204, 21, 0.1); color: #facc15; border: 1px solid rgba(250, 204, 21, 0.2); }
    .ind-card { background: rgba(52, 211, 153, 0.1); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.2); }
    
    .col-actions { display: flex; gap: 8px; flex: 1; justify-content: flex-end; min-width: 250px; flex-wrap: wrap; }
    .action-btn { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; outline: none; }
    .action-btn:hover { transform: translateY(-2px); }
    .btn-blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .btn-blue:hover { background: rgba(59, 130, 246, 0.3); }
    .btn-purple { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
    .btn-purple:hover { background: rgba(168, 85, 247, 0.3); }
    .btn-red { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .btn-red:hover { background: rgba(239, 68, 68, 0.3); }
    .btn-yellow { background: rgba(250, 204, 21, 0.15); color: #facc15; }
    .btn-yellow:hover { background: rgba(250, 204, 21, 0.3); }

    .btn-text { padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.9rem; }

    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }

    /* Global pagination from app.blade.php applies here */
    .pagination li.disabled span, nav span.relative.z-0.inline-flex > span[aria-disabled="true"] > span { 
        background: rgba(0, 0, 0, 0.2);
        opacity: 0.5; 
        cursor: not-allowed; 
    }
    
    .schedule-text {
        color: #93c5fd; /* blue-300 */
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 4px;
        background: rgba(59, 130, 246, 0.1); /* light blue bg */
        border: 1px solid transparent;
    }
    .schedule-text:hover {
        border-color: rgba(59, 130, 246, 0.8);
    }

    @media (max-width: 768px) {
        .user-item { flex-direction: column; align-items: flex-start; gap: 10px; }
        .col-profile, .col-badges, .col-indicators, .col-actions { min-width: 100%; width: 100%; justify-content: flex-start; }
        .col-actions { justify-content: flex-start; margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px; }
        .action-btn { width: 36px; height: 36px; } /* Slightly larger touch targets */
        .glass-card { padding: 20px !important; }
        #filterDropdown { width: 100%; right: 0; }
    }
</style>
@endpush

@section('content')

<div class="top-bar" style="flex-wrap: wrap; gap: 15px;">
    <h1 class="page-title"><i class="fas fa-users"></i> {{ __('Employees Directory') }}</h1>
    
    <div class="search-container" style="flex-grow: 1; max-width: 500px; min-width: 200px; position: relative; z-index: 999;">
        <form id="autoSearchForm" action="/employees" method="GET" style="display: flex; flex-direction: column; width: 100%; gap: 10px;">
            <div style="display: flex; width: 100%; gap: 5px;">
                <input id="autoSearchInput" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name or ID...') }}" class="glass-input" style="flex-grow: 1;">
                
                <button id="filterBtn" type="button" class="glass-btn" onclick="toggleFilter(event)" style="padding: 0 15px; height: 42px; border-radius: 10px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; box-shadow: none; font-size: 0.9rem;">
                    <i class="fas fa-filter"></i> {{ __('Filter') }}
                </button>

                @if(request()->except('page'))
                    <a href="/employees" class="glass-btn" style="background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 0 15px; height: 42px; box-shadow: none;"><i class="fas fa-times"></i></a>
                @endif
            </div>
            
            <div id="filterDropdown" style="display: {{ request()->hasAny(['filter_face', 'filter_fp', 'filter_photo', 'filter_card', 'filter_pass']) ? 'flex' : 'none' }}; flex-direction: column; gap: 12px; position: absolute; top: 48px; right: 0; background: #1e293b; padding: 15px 20px; border-radius: 8px; border: 1px solid var(--border-color); z-index: 50; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5); min-width: 220px;">
                <div style="font-size: 0.95rem; font-weight: 600; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">{{ __('Filter By Attributes') }}</div>
                <label style="color: #cbd5e1; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="filter_face" value="1" {{ request('filter_face') ? 'checked' : '' }} onchange="this.form.submit()"> <i class="fas fa-user-circle" style="color: #c084fc; width: 16px; text-align: center;"></i> {{ __('Face') }}
                </label>
                <label style="color: #cbd5e1; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="filter_fp" value="1" {{ request('filter_fp') ? 'checked' : '' }} onchange="this.form.submit()"> <i class="fas fa-fingerprint" style="color: #38bdf8; width: 16px; text-align: center;"></i> {{ __('Fingerprint') }}
                </label>
                <label style="color: #cbd5e1; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="filter_photo" value="1" {{ request('filter_photo') ? 'checked' : '' }} onchange="this.form.submit()"> <i class="fas fa-image" style="color: #a7f3d0; width: 16px; text-align: center;"></i> {{ __('Photo') }}
                </label>
                <label style="color: #cbd5e1; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="filter_card" value="1" {{ request('filter_card') ? 'checked' : '' }} onchange="this.form.submit()"> <i class="fas fa-id-card" style="color: #34d399; width: 16px; text-align: center;"></i> {{ __('Card') }}
                </label>
                <label style="color: #cbd5e1; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="filter_pass" value="1" {{ request('filter_pass') ? 'checked' : '' }} onchange="this.form.submit()"> <i class="fas fa-key" style="color: #facc15; width: 16px; text-align: center;"></i> {{ __('Password') }}
                </label>
            </div>
        </form>
    </div>

    <div style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%;">
        <form action="/employees/fetch-from-device" method="POST" style="flex: 1; min-width: 140px;">
            @csrf
            <button type="submit" class="btn-text" style="background: rgba(16, 185, 129, 0.2); color: #10b981; width: 100%; justify-content: center;"><i class="fas fa-download"></i> {{ __('Fetch') }}</button>
        </form>
        <a href="/employees/archived" class="btn-text" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); flex: 1; min-width: 140px; justify-content: center;"><i class="fas fa-trash-restore"></i> {{ __('Archived') }}</a>
        <a href="/employees/create" class="glass-btn" style="flex: 1; min-width: 140px; justify-content: center;"><i class="fas fa-user-plus"></i> {{ __('Add') }}</a>
    </div>
</div>

@if(session('success'))
    <div class="alert"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="list-container glass-card">
    @forelse($employees as $employee)
    <div class="user-item">
        
        <div class="col-profile">
            <div class="avatar">
                @if($employee->photo)
                    <img src="data:image/jpeg;base64,{{ $employee->photo }}" alt="Avatar">
                @else
                    <i class="fas fa-user-slash"></i>
                @endif
            </div>
            <div>
                <div class="user-name">{{ $employee->name ?? 'Unknown' }}</div>
                <div class="user-id">ID: {{ $employee->user_id }}</div>
            </div>
        </div>

        <div class="col-badges">
            <div class="badge-row">
                <span class="badge badge-device"><i class="fas fa-desktop"></i> {{ $employee->device ? 'Auto Device ' . $employee->device->sn : 'All Devices' }}</span>
            </div>
            <div class="badge-row">
                @if($employee->role == 14)
                    <span class="badge badge-role-admin"><i class="fas fa-shield-halved"></i> {{ __('Admin') }}</span>
                @else
                    <span class="badge badge-role-user"><i class="fas fa-user"></i> {{ __('User') }}</span>
                @endif
                <button type="button" class="schedule-text" style="background:transparent; border:none; cursor:pointer;" onclick="document.getElementById('wpForm{{ $employee->id }}').style.display='flex'">
                    <i class="fas fa-calendar-alt" style="margin-right:4px;"></i> {{ $employee->monthlyPattern ? $employee->monthlyPattern->name : ($employee->workPattern ? $employee->workPattern->name : 'Default Schedule') }}
                </button>
            </div>
        </div>

        <div class="col-indicators">
            @if($employee->fingerprints)
                @php
                    $fpData = json_decode($employee->fingerprints, true);
                    $fpCount = 0;
                    if (is_array($fpData)) {
                        if (isset($fpData['fids'])) $fpCount = count($fpData['fids']);
                        elseif (isset($fpData['count'])) $fpCount = $fpData['count'];
                        else $fpCount = count($fpData);
                    }
                @endphp
                @if($fpCount > 0)
                <div class="indicator ind-fp" title="Fingerprints Enrolled"><i class="fas fa-fingerprint"></i> <span style="margin-left: 2px;">{{ $fpCount }}</span></div>
                @endif
            @endif
            @if($employee->has_face)
                <div class="indicator ind-face" title="Face Enrolled"><i class="fas fa-user-circle"></i></div>
            @endif
            @if($employee->password)
                <div class="indicator ind-pass" title="Password Enrolled"><i class="fas fa-key"></i></div>
            @endif
            @if($employee->card_number)
                <div class="indicator ind-card" title="Card Enrolled"><i class="fas fa-id-card"></i></div>
            @endif
        </div>

        <div class="col-actions">
            <button type="button" class="action-btn btn-blue" title="{{ __('Manage Fingerprints') }}" onclick="document.getElementById('fpForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-fingerprint"></i></button>
            
            <button type="button" class="action-btn btn-purple" title="{{ __('Upload Photo') }}" onclick="document.getElementById('photoForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-camera"></i></button>
            
            @if($employee->photo)
            <form action="/employees/{{ $employee->id }}/delete-photo" method="POST" onsubmit="return confirm('{{ __('Delete profile picture?') }}');" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn btn-red" title="{{ __('Delete Photo') }}"><i class="fas fa-image"></i></button>
            </form>
            @endif
            
            @if($employee->has_face)
            <form action="/employees/{{ $employee->id }}/delete-face" method="POST" onsubmit="return confirm('{{ __('Delete face?') }}');" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn btn-red" title="{{ __('Delete Face') }}"><i class="fas fa-user-astronaut"></i></button>
            </form>
            @endif

            <button type="button" class="action-btn btn-yellow" title="{{ __('Set Password & Card') }}" onclick="document.getElementById('credForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-key"></i></button>
            
            <button type="button" class="action-btn btn-blue" title="{{ __('Edit User') }}" onclick="document.getElementById('editForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-edit"></i></button>
            
            <form action="/employees/{{ $employee->id }}/delete" method="POST" onsubmit="return confirm('{{ __('Delete completely?') }}');" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn btn-red" title="{{ __('Delete User') }}"><i class="fas fa-trash"></i></button>
            </form>
        </div>
        
    </div>

    <!-- Fingerprint Modal -->
    <div id="fpForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8);  z-index:9999; align-items:center; justify-content:center;">
        <div class="glass-card" style="padding:30px; width: 400px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-fingerprint text-blue-400"></i> Manage Fingerprints for {{ $employee->name }}</h3>
            
            @php
                $enrolledFids = [];
                if ($employee->fingerprints) {
                    $fpData = json_decode($employee->fingerprints, true);
                    if (is_array($fpData) && isset($fpData['fids'])) {
                        $enrolledFids = $fpData['fids'];
                    }
                }
            @endphp
            
            <form action="/employees/{{ $employee->id }}/enroll-fingerprint" method="POST" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                @csrf
                <div style="margin: 15px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Select Finger to Enroll</label>
                    <select name="finger_index" class="glass-input" style="width:100%;">
                        @php
                            $fingerNames = [
                                0 => 'Right Thumb', 1 => 'Right Index', 2 => 'Right Middle', 3 => 'Right Ring', 4 => 'Right Little',
                                5 => 'Left Thumb', 6 => 'Left Index', 7 => 'Left Middle', 8 => 'Left Ring', 9 => 'Left Little'
                            ];
                        @endphp
                        @foreach($fingerNames as $index => $name)
                            <option value="{{ $index }}" {{ in_array($index, $enrolledFids) ? 'disabled' : '' }}>
                                {{ $index }} - {{ $name }} {{ in_array($index, $enrolledFids) ? '(Already Enrolled)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="glass-btn" style="width: 100%; justify-content: center;"><i class="fas fa-plus"></i> Start Enrollment on Device</button>
            </form>

            @if(count($enrolledFids) > 0)
            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:10px; color:white; font-size:0.9rem;">Enrolled Fingerprints:</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($enrolledFids as $fid)
                    <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(0,0,0,0.2); padding:10px; border-radius:6px; border:1px solid var(--border-color);">
                        <span style="color:white;"><i class="fas fa-fingerprint text-blue-400"></i> Finger Index: {{ $fid }}</span>
                        <form action="/employees/{{ $employee->id }}/delete-fingerprint/{{ $fid }}" method="POST" onsubmit="return confirm('Delete this specific fingerprint?');" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn-text" style="padding: 4px 10px; font-size: 0.8rem; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <form action="/employees/{{ $employee->id }}/delete-fingerprint" method="POST" onsubmit="return confirm('Clear ALL fingerprints for this user?');">
                @csrf
                <button type="submit" class="btn-text" style="width: 100%; justify-content: center; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);"><i class="fas fa-trash"></i> Delete All Fingerprints</button>
            </form>
            
            <div style="display:flex; justify-content:center; margin-top: 20px;">
                <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color); width:100%;" onclick="document.getElementById('fpForm{{ $employee->id }}').style.display='none'">Close</button>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div id="photoForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8);  z-index:9999; align-items:center; justify-content:center;">
        <div class="glass-card" style="padding:30px; width: 450px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-camera text-purple-400"></i> Upload Photo for {{ $employee->name }}</h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button type="button" class="btn-text" style="flex: 1; background: rgba(255,255,255,0.1); border: 1px solid var(--border-color); color: white;" onclick="document.getElementById('fileUploadSection{{ $employee->id }}').style.display='block'; document.getElementById('cameraSection{{ $employee->id }}').style.display='none'; if(window.currentStream{{ $employee->id }}) window.currentStream{{ $employee->id }}.getTracks().forEach(t=>t.stop());"><i class="fas fa-folder-open"></i> Choose File</button>
                <button type="button" class="glass-btn" style="flex: 1;" onclick="openCamera('{{ $employee->id }}')"><i class="fas fa-video"></i> Open Camera</button>
            </div>

            <div id="fileUploadSection{{ $employee->id }}">
                <form action="/employees/{{ $employee->id }}/photo" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin: 20px 0;">
                        <input type="file" name="photo" accept="image/jpeg,image/png" required class="glass-input" style="width:100%;">
                        <small style="color:var(--text-muted); display:block; margin-top:5px;">Max 200KB. JPEG recommended.</small>
                    </div>
                    
                    <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px;">
                        <label style="color: white; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="upload_as_profile" value="1" checked style="width: 16px; height: 16px;">
                            <i class="fas fa-id-badge text-blue-400"></i> Set as Profile Avatar
                        </label>
                        <label style="color: white; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="upload_as_face" value="1" style="width: 16px; height: 16px;">
                            <i class="fas fa-user-astronaut text-purple-400"></i> Set as Face Recognition Template
                        </label>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="closePhotoModal('{{ $employee->id }}')">Cancel</button>
                        <button type="submit" class="glass-btn">Upload & Sync</button>
                    </div>
                </form>
            </div>

            <div id="cameraSection{{ $employee->id }}" style="display:none; text-align: center;">
                <video id="video{{ $employee->id }}" autoplay playsinline style="width: 100%; border-radius: 8px; border: 2px solid var(--border-color); margin-bottom: 15px; background: #000; height: 250px;"></video>
                <canvas id="canvas{{ $employee->id }}" style="display:none;"></canvas>
                
                <form action="/employees/{{ $employee->id }}/photo-base64" method="POST" id="cameraForm{{ $employee->id }}">
                    @csrf
                    <input type="hidden" name="photo_base64" id="photoData{{ $employee->id }}">
                    
                    <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px; text-align: left;">
                        <label style="color: white; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="upload_as_profile" value="1" checked style="width: 16px; height: 16px;">
                            <i class="fas fa-id-badge text-blue-400"></i> Set as Profile Avatar
                        </label>
                        <label style="color: white; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="upload_as_face" value="1" style="width: 16px; height: 16px;">
                            <i class="fas fa-user-astronaut text-purple-400"></i> Set as Face Recognition Template
                        </label>
                    </div>

                    <div style="display:flex; justify-content:space-between; gap:10px;">
                        <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="closePhotoModal('{{ $employee->id }}')">Cancel</button>
                        <button type="button" class="glass-btn" onclick="capturePhoto('{{ $employee->id }}')"><i class="fas fa-camera"></i> Capture & Sync</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Credentials Modal -->
    <div id="credForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8);  z-index:9999; align-items:center; justify-content:center;">
        <div class="glass-card" style="padding:30px; width: 400px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-key text-yellow-400"></i> Credentials for {{ $employee->name }}</h3>
            <form action="/employees/{{ $employee->id }}/credentials" method="POST">
                @csrf
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Password</label>
                    <input type="text" name="password" value="{{ $employee->password }}" class="glass-input" style="width:100%;">
                </div>
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Card Number</label>
                    <input type="text" name="card_number" value="{{ $employee->card_number }}" class="glass-input" style="width:100%;">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-text" style="background:var(--red-color); color:white; border:none; margin-right: auto;" onclick="this.form.password.value=''; this.form.card_number.value='';">Clear Fields</button>
                    <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('credForm{{ $employee->id }}').style.display='none'">Cancel</button>
                    <button type="submit" class="glass-btn">Save & Sync</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Work Pattern Modal -->
    <div id="wpForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8);  z-index:9999; align-items:center; justify-content:center;">
        <div class="glass-card" style="padding:30px; width: 400px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-calendar-alt text-blue-400"></i> Set Schedule for {{ $employee->name }}</h3>
            <form action="/employees/{{ $employee->id }}/work-pattern" method="POST">
                @csrf
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Select Work Pattern</label>
                    <select name="pattern_id" class="glass-input" style="width:100%;">
                        <option value="">Default Schedule</option>
                        <optgroup label="Weekly Patterns" style="background: var(--sidebar-bg); color: white;">
                            @foreach($workPatterns as $wp)
                                <option value="weekly_{{ $wp->id }}" {{ $employee->work_pattern_id == $wp->id ? 'selected' : '' }}>{{ $wp->name }}</option>
                            @endforeach
                        </optgroup>
                        @if(isset($monthlyPatterns) && $monthlyPatterns->count() > 0)
                        <optgroup label="Monthly Patterns" style="background: var(--sidebar-bg); color: white;">
                            @foreach($monthlyPatterns as $mp)
                                <option value="monthly_{{ $mp->id }}" {{ $employee->monthly_pattern_id == $mp->id ? 'selected' : '' }}>{{ $mp->name }}</option>
                            @endforeach
                        </optgroup>
                        @endif
                    </select>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('wpForm{{ $employee->id }}').style.display='none'">Cancel</button>
                    <button type="submit" class="glass-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8);  z-index:9999; align-items:center; justify-content:center;">
        <div class="glass-card" style="padding:30px; width: 450px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-edit text-blue-400"></i> Edit Employee</h3>
            <form action="/employees/{{ $employee->id }}/update" method="POST">
                @csrf
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Full Name</label>
                    <input type="text" name="name" value="{{ $employee->name }}" required class="glass-input" style="width:100%;">
                </div>
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Privilege (Role)</label>
                    <select name="role" class="glass-input" style="width:100%;">
                        <option value="0" {{ $employee->role == 0 ? 'selected' : '' }}>Normal User</option>
                        <option value="14" {{ $employee->role == 14 ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Assign to Device</label>
                    <select name="device_id" class="glass-input" style="width:100%;">
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ $employee->device_id == $device->id ? 'selected' : '' }}>{{ $device->sn }}</option>
                        @endforeach
                    </select>
                </div>
                
                <input type="hidden" name="password" value="{{ $employee->password }}">
                <input type="hidden" name="card_number" value="{{ $employee->card_number }}">

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('editForm{{ $employee->id }}').style.display='none'">Cancel</button>
                    <button type="submit" class="glass-btn">Update & Sync</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <p>{{ __('No employees found.') }}</p>
    </div>
    @endforelse
</div>

<div style="margin-top: 20px; display: flex; justify-content: center;">
    {{ $employees->links('pagination::bootstrap-5') }}
</div>

@endsection

@push('scripts')
<script>
    async function openCamera(empId) {
        document.getElementById('fileUploadSection' + empId).style.display = 'none';
        document.getElementById('cameraSection' + empId).style.display = 'block';
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            const video = document.getElementById('video' + empId);
            video.srcObject = stream;
            window['currentStream' + empId] = stream;
        } catch (err) {
            alert("Could not access camera: " + err.message);
        }
    }

    function capturePhoto(empId) {
        const video = document.getElementById('video' + empId);
        const canvas = document.getElementById('canvas' + empId);
        
        // Capture at 400x400 to keep it lightweight for ZKTeco
        canvas.width = 400;
        canvas.height = 400;
        
        const ctx = canvas.getContext('2d');
        // Draw the center square of the video
        const size = Math.min(video.videoWidth, video.videoHeight);
        const startX = (video.videoWidth - size) / 2;
        const startY = (video.videoHeight - size) / 2;
        ctx.drawImage(video, startX, startY, size, size, 0, 0, 400, 400);
        
        const dataURL = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('photoData' + empId).value = dataURL;
        
        // Stop stream
        if(window['currentStream' + empId]) {
            window['currentStream' + empId].getTracks().forEach(t => t.stop());
        }
        
        document.getElementById('cameraForm' + empId).submit();
    }

    function closePhotoModal(empId) {
        if(window['currentStream' + empId]) {
            window['currentStream' + empId].getTracks().forEach(t => t.stop());
        }
        document.getElementById('photoForm' + empId).style.display = 'none';
    }

    // Auto-submit search form on typing (Debounce)
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('autoSearchInput');
        const searchForm = document.getElementById('autoSearchForm');
        let debounceTimer;

        if (searchInput) {
            // Focus if there is text OR if a search was just performed (URL has search parameter)
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
                }, 600); // 600ms delay to avoid submitting on every single keystroke
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterDropdown');
            if (dropdown && dropdown.style.display === 'flex') {
                if (!dropdown.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            }
        });
    });

    function toggleFilter(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('filterDropdown');
        if(dropdown) {
            dropdown.style.display = dropdown.style.display === 'none' ? 'flex' : 'none';
        }
    }
</script>
@endpush

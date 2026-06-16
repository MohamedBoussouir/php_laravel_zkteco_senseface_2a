@extends('layouts.app')

@push('styles')
<style>
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .list-container { background: var(--sidebar-bg); border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; display: flex; flex-direction: column; }
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
    .btn-primary { background: var(--accent); color: white; }
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }

    /* Pagination Styles - Connected Block Style */
    nav { display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 20px; }
    
    /* Hide Laravel's mobile pagination and 'Showing...' text */
    nav > div:first-child, nav p, nav .text-sm, nav > div > div:first-child { display: none !important; }
    
    /* Show only the desktop pagination container */
    nav > div:last-child { display: flex; justify-content: center; width: 100%; }
    nav > div > div:last-child { display: flex; justify-content: center; width: 100%; }
    
    .pagination, nav span.relative.z-0.inline-flex { 
        display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 0; 
        background: transparent; 
        border-radius: 6px; 
        border: 1px solid var(--border-color); 
        overflow: hidden;
    }
    
    .pagination li, nav span.relative.z-0.inline-flex > span, nav span.relative.z-0.inline-flex > a { 
        display: inline-flex; 
        border-right: 1px solid var(--border-color);
    }
    
    /* Remove right border from last item */
    .pagination li:last-child, nav span.relative.z-0.inline-flex > *:last-child {
        border-right: none;
    }
    
    .pagination li a, .pagination li span, nav span.relative.z-0.inline-flex > span > span, nav span.relative.z-0.inline-flex > a { 
        padding: 8px 16px; 
        background: rgba(255, 255, 255, 0.03); 
        color: #cbd5e1; 
        text-decoration: none; 
        font-size: 0.95rem; 
        font-weight: 500; 
        transition: background 0.2s; 
        display: flex; align-items: center; justify-content: center; min-width: 40px; 
        border: none; border-radius: 0;
    }
    
    .pagination li a:hover, nav span.relative.z-0.inline-flex > a:hover { 
        background: rgba(255, 255, 255, 0.08); 
        color: white; 
        text-decoration: none; 
    }
    
    .pagination li.active span, nav span.relative.z-0.inline-flex > span[aria-current="page"] > span { 
        background: #3b82f6; 
        color: white; 
        border: none;
        font-weight: bold;
    }
    
    .pagination li.disabled span, nav span.relative.z-0.inline-flex > span[aria-disabled="true"] > span { 
        background: rgba(0, 0, 0, 0.2);
        opacity: 0.5; 
        cursor: not-allowed; 
    }
</style>
@endpush

@section('content')

<div class="top-bar">
    <h1 class="page-title"><i class="fas fa-users"></i> Employees Directory</h1>
    <div style="display: flex; gap: 10px;">
        <form action="/employees/fetch-from-device" method="POST">
            @csrf
            <button type="submit" class="btn-text" style="background: rgba(16, 185, 129, 0.2); color: #10b981;"><i class="fas fa-download"></i> Fetch from Device</button>
        </form>
        <a href="/employees/archived" class="btn-text" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);"><i class="fas fa-trash-restore"></i> Archived Employees</a>
        <a href="/employees/create" class="btn-text btn-primary"><i class="fas fa-user-plus"></i> Add Employee</a>
    </div>
</div>

@if(session('success'))
    <div class="alert"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="list-container">
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
                    <span class="badge badge-role-admin"><i class="fas fa-shield-halved"></i> Admin</span>
                @else
                    <span class="badge badge-role-user"><i class="fas fa-user"></i> User</span>
                @endif
                <button type="button" class="schedule-text" style="background:transparent; border:none; cursor:pointer;" onclick="document.getElementById('wpForm{{ $employee->id }}').style.display='flex'">
                    <i class="fas fa-calendar-alt" style="margin-right:4px;"></i> {{ $employee->workPattern ? $employee->workPattern->name : 'Default Schedule' }}
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
            <button type="button" class="action-btn btn-blue" title="Manage Fingerprints" onclick="document.getElementById('fpForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-fingerprint"></i></button>
            
            <button type="button" class="action-btn btn-purple" title="Upload Photo" onclick="document.getElementById('photoForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-camera"></i></button>
            
            @if($employee->photo)
            <form action="/employees/{{ $employee->id }}/delete-photo" method="POST" onsubmit="return confirm('Delete profile picture?');" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn btn-red" title="Delete Photo"><i class="fas fa-image"></i></button>
            </form>
            @endif
            
            @if($employee->has_face)
            <form action="/employees/{{ $employee->id }}/delete-face" method="POST" onsubmit="return confirm('Delete face?');" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn btn-red" title="Delete Face"><i class="fas fa-user-astronaut"></i></button>
            </form>
            @endif

            <button type="button" class="action-btn btn-yellow" title="Set Password & Card" onclick="document.getElementById('credForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-key"></i></button>
            
            <button type="button" class="action-btn btn-blue" title="Edit User" onclick="document.getElementById('editForm{{ $employee->id }}').style.display='flex'"><i class="fas fa-edit"></i></button>
            
            <form action="/employees/{{ $employee->id }}/delete" method="POST" onsubmit="return confirm('Delete completely?');" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn btn-red" title="Delete User"><i class="fas fa-trash"></i></button>
            </form>
        </div>
        
    </div>

    <!-- Fingerprint Modal -->
    <div id="fpForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--sidebar-bg); padding:30px; border-radius:12px; border:1px solid var(--border-color); width: 400px; max-width: 90%;">
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
                    <select name="finger_index" style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
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
                <button type="submit" class="btn-text btn-primary" style="width: 100%; justify-content: center;"><i class="fas fa-plus"></i> Start Enrollment on Device</button>
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
                <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('fpForm{{ $employee->id }}').style.display='none'">Close</button>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div id="photoForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--sidebar-bg); padding:30px; border-radius:12px; border:1px solid var(--border-color); width: 450px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-camera text-purple-400"></i> Upload Photo for {{ $employee->name }}</h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button type="button" class="btn-text" style="flex: 1; background: rgba(255,255,255,0.1); border: 1px solid var(--border-color); color: white;" onclick="document.getElementById('fileUploadSection{{ $employee->id }}').style.display='block'; document.getElementById('cameraSection{{ $employee->id }}').style.display='none'; if(window.currentStream{{ $employee->id }}) window.currentStream{{ $employee->id }}.getTracks().forEach(t=>t.stop());"><i class="fas fa-folder-open"></i> Choose File</button>
                <button type="button" class="btn-text btn-primary" style="flex: 1;" onclick="openCamera('{{ $employee->id }}')"><i class="fas fa-video"></i> Open Camera</button>
            </div>

            <div id="fileUploadSection{{ $employee->id }}">
                <form action="/employees/{{ $employee->id }}/photo" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin: 20px 0;">
                        <input type="file" name="photo" accept="image/jpeg,image/png" required style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px;">
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
                        <button type="submit" class="btn-text btn-primary">Upload & Sync</button>
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
                        <button type="button" class="btn-text btn-primary" onclick="capturePhoto('{{ $employee->id }}')"><i class="fas fa-camera"></i> Capture & Sync</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Credentials Modal -->
    <div id="credForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--sidebar-bg); padding:30px; border-radius:12px; border:1px solid var(--border-color); width: 400px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-key text-yellow-400"></i> Credentials for {{ $employee->name }}</h3>
            <form action="/employees/{{ $employee->id }}/credentials" method="POST">
                @csrf
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Password</label>
                    <input type="text" name="password" value="{{ $employee->password }}" style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
                </div>
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Card Number</label>
                    <input type="text" name="card_number" value="{{ $employee->card_number }}" style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-text" style="background:var(--red-color); color:white; border:none; margin-right: auto;" onclick="this.form.password.value=''; this.form.card_number.value='';">Clear Fields</button>
                    <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('credForm{{ $employee->id }}').style.display='none'">Cancel</button>
                    <button type="submit" class="btn-text btn-primary">Save & Sync</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Work Pattern Modal -->
    <div id="wpForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--sidebar-bg); padding:30px; border-radius:12px; border:1px solid var(--border-color); width: 400px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-calendar-alt text-blue-400"></i> Set Schedule for {{ $employee->name }}</h3>
            <form action="/employees/{{ $employee->id }}/work-pattern" method="POST">
                @csrf
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Select Work Pattern</label>
                    <select name="work_pattern_id" style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
                        <option value="">Default Schedule</option>
                        @foreach($workPatterns as $wp)
                            <option value="{{ $wp->id }}" {{ $employee->work_pattern_id == $wp->id ? 'selected' : '' }}>{{ $wp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('wpForm{{ $employee->id }}').style.display='none'">Cancel</button>
                    <button type="submit" class="btn-text btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editForm{{ $employee->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--sidebar-bg); padding:30px; border-radius:12px; border:1px solid var(--border-color); width: 450px; max-width: 90%;">
            <h3 style="margin-top:0; color:white;"><i class="fas fa-edit text-blue-400"></i> Edit Employee</h3>
            <form action="/employees/{{ $employee->id }}/update" method="POST">
                @csrf
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Full Name</label>
                    <input type="text" name="name" value="{{ $employee->name }}" required style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
                </div>
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Privilege (Role)</label>
                    <select name="role" style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
                        <option value="0" {{ $employee->role == 0 ? 'selected' : '' }}>Normal User</option>
                        <option value="14" {{ $employee->role == 14 ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <div style="margin: 20px 0;">
                    <label style="display:block; margin-bottom:5px; color:var(--text-muted); font-size:0.9rem;">Assign to Device</label>
                    <select name="device_id" style="width:100%; padding: 10px; background: rgba(0,0,0,0.2); color:white; border-radius: 6px; border: 1px solid var(--border-color);">
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ $employee->device_id == $device->id ? 'selected' : '' }}>{{ $device->sn }}</option>
                        @endforeach
                    </select>
                </div>
                
                <input type="hidden" name="password" value="{{ $employee->password }}">
                <input type="hidden" name="card_number" value="{{ $employee->card_number }}">

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn-text" style="background:transparent; color:white; border:1px solid var(--border-color);" onclick="document.getElementById('editForm{{ $employee->id }}').style.display='none'">Cancel</button>
                    <button type="submit" class="btn-text btn-primary">Update & Sync</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div style="padding: 50px; text-align: center; color: var(--text-muted);">
        <i class="fas fa-user-slash" style="font-size: 3rem; opacity: 0.5; margin-bottom: 15px;"></i>
        <div>No employees found. Click "Add Employee" to create one.</div>
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
</script>
@endpush

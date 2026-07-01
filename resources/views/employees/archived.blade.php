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
    
    .col-actions { display: flex; gap: 8px; flex: 1; justify-content: flex-end; min-width: 250px; flex-wrap: wrap; }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }

    @media (max-width: 768px) {
        .user-item { flex-direction: column; align-items: flex-start; gap: 10px; }
        .col-profile, .col-badges, .col-actions { min-width: 100%; width: 100%; justify-content: flex-start; }
        .col-actions { justify-content: flex-start; margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px; }
    }
</style>
@endpush

@section('content')

<div class="header-container top-bar">
    <div class="title-section">
        <h2><i class="fas fa-archive"></i> Archived Employees</h2>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="/employees" class="glass-btn"><i class="fas fa-arrow-left"></i> Back to Employees</a>
    </div>
</div>

@if(session('success'))
    <div class="alert"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="glass-card" style="padding: 0; overflow: hidden;">
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
            </div>
        </div>

        <div class="col-actions">
            <form action="/employees/{{ $employee->id }}/restore" method="POST" onsubmit="return confirm('Are you sure you want to restore this employee?');" style="display:inline;">
                @csrf
                <button type="submit" class="glass-btn" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); box-shadow: none;" title="Restore">
                    <i class="fas fa-trash-restore"></i> Restore
                </button>
            </form>
            <form action="/employees/{{ $employee->id }}/force-delete" method="POST" onsubmit="return confirm('Warning: This will permanently delete the employee and all their attendance records. This cannot be undone. Are you sure?');" style="display:inline;">
                @csrf
                <button type="submit" class="glass-btn" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); box-shadow: none;" title="Permanent Delete">
                    <i class="fas fa-ban"></i> Delete Permanently
                </button>
            </form>
        </div>
        
    </div>
    @empty
    <div style="padding: 50px; text-align: center; color: var(--text-muted);">
        <i class="fas fa-archive" style="font-size: 3rem; opacity: 0.5; margin-bottom: 15px;"></i>
        <div>No archived employees found.</div>
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

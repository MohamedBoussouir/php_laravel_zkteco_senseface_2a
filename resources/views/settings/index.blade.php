@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <i class="fas fa-cog"></i> Settings
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.2); color: #10b981; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.4);">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card" style="padding: 25px; margin-bottom: 30px;">
        <h4 style="margin-bottom: 20px; color: var(--text-main); font-weight: 600;">{{ __('Device Time Settings') }}</h4>
        <p style="color: var(--text-muted); margin-bottom: 25px; font-size: 0.95rem;">{{ __('Configure the date and time on your connected devices. You can choose to automatically sync with the server time or set a manual date and time.') }}</p>

        <form action="/settings/time" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Select Device') }}</label>
                <select name="device_id" class="glass-input" style="width: 100%;" required>
                    <option value="all">{{ __('All Approved Devices') }}</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name ?? 'Device '.$device->sn }} (SN: {{ $device->sn }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Sync Type') }}</label>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="sync_type" value="auto" checked onchange="toggleManualTime(this.value)">
                        {{ __('Automatic (Sync with server time)') }}
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="sync_type" value="manual" onchange="toggleManualTime(this.value)">
                        {{ __('Manual Time') }}
                    </label>
                </div>
            </div>

            <div id="manual_time_container" style="margin-bottom: 25px; display: none;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Manual Date & Time') }}</label>
                <input type="datetime-local" name="manual_time" id="manual_time_input" class="glass-input" style="width: 100%; color-scheme: dark;">
                @error('manual_time')
                    <div style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="glass-btn">
                <i class="fas fa-clock"></i> {{ __('Update Device Time') }}
            </button>
        </form>
    </div>

    <!-- Attendance Logging Mode Card -->
    <div class="glass-card" style="padding: 25px; margin-bottom: 30px;">
        <h4 style="margin-bottom: 5px; color: var(--text-main); font-weight: 600;"><i class="fas fa-pencil-alt"></i> {{ __('Attendance Logging Mode') }}</h4>
        <p style="color: var(--text-muted); margin-bottom: 25px; font-size: 0.95rem;">{{ __('Choose how attendance punch states (Check-In / Check-Out) are recorded and displayed.') }}</p>

        <form action="/settings/logging-mode" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Logging Strategy') }}</label>
                <select name="calc_mode" id="setting-calc-mode" class="glass-input" style="width: 100%;" onchange="updateStrategyExplanation()">
                    <option value="calculated" {{ $calcMode === 'calculated' ? 'selected' : '' }}>{{ __('Smart Calculation (First-In, Last-Out)') }}</option>
                    <option value="device" {{ $calcMode === 'device' ? 'selected' : '' }}>{{ __('Device Keypad Selection (Manual F1/F2)') }}</option>
                </select>
            </div>

            <div id="strategy-explanation" style="margin-bottom: 25px; font-size: 0.9rem; color: var(--text-main); background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px;">
                <!-- Filled by JS -->
            </div>

            <button type="submit" class="glass-btn" style="width: 100%; justify-content: center; background: linear-gradient(135deg, #a855f7, #7e22ce);">
                <i class="fas fa-check-circle"></i> {{ __('Apply Strategy') }}
            </button>
        </form>
    </div>

    <!-- Device Screensaver Card -->
    <div class="glass-card" style="padding: 25px; margin-bottom: 30px;">
        <h4 style="margin-bottom: 5px; color: var(--text-main); font-weight: 600;"><i class="fas fa-image"></i> {{ __('Device Screensaver (Ad Picture)') }}</h4>
        <p style="color: var(--text-muted); margin-bottom: 25px; font-size: 0.95rem;">{{ __('Upload an image to be used as an advertisement/screensaver when the device is idle. Recommended image size: 1024x600 pixels. Max 2MB.') }}</p>

        <form action="/settings/wallpaper" method="POST" enctype="multipart/form-data" id="wallpaper-form">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Select Device') }}</label>
                <select name="device_id" class="glass-input" style="width: 100%;" required>
                    <option value="all">{{ __('All Approved Devices') }}</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name ?? 'Device '.$device->sn }} (SN: {{ $device->sn }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Upload Image') }}</label>
                <input type="file" name="wallpaper" id="wallpaper-input" accept="image/jpeg,image/png" required class="glass-input" style="width: 100%; cursor: pointer;">
            </div>

            <div id="wallpaper-preview-container" style="margin-bottom: 20px; display: none;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted);">{{ __('Preview') }}</label>
                <div style="background: rgba(0,0,0,0.2); border-radius: 8px; padding: 10px; text-align: center;">
                    <img id="wallpaper-preview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 6px; border: 1px solid var(--border-color);">
                </div>
            </div>

            <button type="submit" id="wallpaper-submit-btn" class="glass-btn" style="width: 100%; justify-content: center;">
                <i class="fas fa-upload"></i> {{ __('Upload Screensaver to Device') }}
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleManualTime(val) {
        const container = document.getElementById('manual_time_container');
        const input = document.getElementById('manual_time_input');
        
        if (val === 'manual') {
            container.style.display = 'block';
            input.required = true;
            
            if (!input.value) {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                input.value = now.toISOString().slice(0,16);
            }
        } else {
            container.style.display = 'none';
            input.required = false;
        }
    }

    function updateStrategyExplanation() {
        const selectEl = document.getElementById('setting-calc-mode');
        if (!selectEl) return;
        const mode = selectEl.value;
        const explanationEl = document.getElementById('strategy-explanation');
        if (!explanationEl) return;

        if (mode === 'calculated') {
            explanationEl.innerHTML = `
                <strong>{{ __('Smart Calculation Mode (Recommended):') }}</strong><br>
                {{ __('The app dynamically calculates attendance status based on daily punches. The first punch of the day is marked as') }} <strong>{{ __('Check-In (Arrivée)') }}</strong> {{ __('and the last punch as') }} <strong>{{ __('Check-Out (Depart)') }}</strong>.<br>
                <span style="color: #34d399; margin-top: 5px; display: block;"><i class="fas fa-shield-alt"></i> {{ __('The device screen remains clean and simple without any confusing status words.') }}</span>
            `;
        } else {
            explanationEl.innerHTML = `
                <strong>{{ __('Device Keypad Selection Mode (Manual):') }}</strong><br>
                {{ __('Employees must manually press the') }} <strong>F1</strong> {{ __('(Arrivee) or') }} <strong>F2</strong> {{ __('(Depart) keys on the device physical keypad before punching their finger.') }}<br>
                <span style="color: #fbbf24; margin-top: 5px; display: block;"><i class="fas fa-exclamation-triangle"></i> {{ __('Status buttons display on the screen. Employees must remember to select their status.') }}</span>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', updateStrategyExplanation);

    const wallpaperInput = document.getElementById('wallpaper-input');
    const wallpaperPreview = document.getElementById('wallpaper-preview');
    const previewContainer = document.getElementById('wallpaper-preview-container');
    const wallpaperForm = document.getElementById('wallpaper-form');
    const submitBtn = document.getElementById('wallpaper-submit-btn');

    if (wallpaperInput) {
        wallpaperInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    wallpaperPreview.src = ev.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }

    if (wallpaperForm) {
        wallpaperForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading... Please wait.';
        });
    }
</script>
@endpush
@endsection

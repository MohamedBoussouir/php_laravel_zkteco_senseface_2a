@extends('layouts.app')

@push('styles')
<style>
    .card { background-color: transparent; border: none; padding: 0; margin-bottom: 30px; box-shadow: none; }
    h2 { color: white; margin-bottom: 20px; font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
    
    .instruction-block { background: rgba(0,0,0,0.2); padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid var(--accent); border: 1px solid var(--glass-border); }
    .instruction-block p { margin-bottom: 15px; color: var(--text-muted); line-height: 1.6; }
    
    .setting-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .setting-row:last-child { border-bottom: none; }
    .setting-label { font-weight: 600; color: #e2e8f0; width: 250px; }
    .setting-value { font-family: monospace; background: rgba(0,0,0,0.4); padding: 6px 12px; border-radius: 8px; color: #34d399; letter-spacing: 1px; user-select: all; cursor: pointer; border: 1px solid rgba(52, 211, 153, 0.2); }
    
    .alert-warning { background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.3); padding: 15px; border-radius: 12px; color: #facc15; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start; }

</style>
@endpush

@section('content')
<div class="card glass-card" style="padding: 30px;">
    <h2><i class="fas fa-video text-blue-400"></i> {{ __('Video Intercom Configuration (SIP)') }}</h2>
    
    <div class="alert-warning">
        <i class="fas fa-exclamation-triangle" style="margin-top: 3px;"></i>
        <div>
            <strong>{{ __('Important Prerequisite') }}</strong><br>
            {{ __('For the WebRTC Intercom to work, the Asterisk Docker container must be running on this server. Ensure you have run') }} <code>docker-compose up -d</code> {{ __('in the project directory. Also, WebRTC requires HTTPS or localhost to access your microphone and camera.') }}
        </div>
    </div>

    <div class="instruction-block">
        <h3 style="color: white; margin-bottom: 15px; font-size: 1.1rem;">{{ __('ZKTeco Device Settings') }}</h3>
        <p>{{ __('Go to your ZKTeco SenseFace 2A menu:') }} <strong>Comm. -> SIP Server</strong> {{ __('and enter the exact following details:') }}</p>
        
        <div style="background: rgba(0,0,0,0.2); border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
            <div class="setting-row">
                <div class="setting-label">{{ __('Server IP Address') }}</div>
                <div class="setting-value">{{ $serverIp }}</div>
            </div>
            <div class="setting-row">
                <div class="setting-label">{{ __('Server Port') }}</div>
                <div class="setting-value">5060</div>
            </div>
            <div class="setting-row">
                <div class="setting-label">{{ __('SIP Extension / Username') }}</div>
                <div class="setting-value">101</div>
            </div>
            <div class="setting-row">
                <div class="setting-label">{{ __('SIP Password') }}</div>
                <div class="setting-value">zktecopass</div>
            </div>
            <div class="setting-row">
                <div class="setting-label">{{ __('Intercom / Doorbell Number') }}</div>
                <div class="setting-value">100</div>
            </div>
        </div>
        <p style="margin-top: 15px; font-size: 0.85rem;">{{ __('Note: The doorbell number is the extension that rings when the visitor presses the bell button. The dashboard is listening on extension 100.') }}</p>
    </div>
</div>
@endsection

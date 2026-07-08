@extends('layouts.app')

@push('styles')
<style>
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    .text-danger { color: #ef4444; font-size: 0.85rem; margin-top: 5px; display: block; }
    
    /* إصلاح لون الحقول عند التعبئة التلقائية (Autofill) في المتصفحات */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px var(--sidebar-bg) inset !important;
        -webkit-text-fill-color: white !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    /* إصلاح القوائم المنسدلة */
    option {
        background: var(--sidebar-bg);
        color: white;
    }
</style>
@endpush

@section('content')

<div class="top-bar" style="margin-bottom: 30px;">
    <h1 class="page-title"><a href="/employees" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left"></i></a> {{ __('Add New Employee') }}</h1>
</div>

<div class="glass-card" style="max-width: 600px; margin: 0 auto; padding: 30px;">
    <form action="/employees" method="POST">
        @csrf
        
        <div class="form-group">
            <label>{{ __('Employee ID (PIN on device)') }}</label>
            <input type="number" name="user_id" class="glass-input" style="width: 100%;" value="{{ old('user_id') }}" required>
            @error('user_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>{{ __('Full Name') }}</label>
            <input type="text" name="name" class="glass-input" style="width: 100%;" value="{{ old('name') }}" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>{{ __('Privilege (Role)') }}</label>
            <select name="role" class="glass-input" style="width: 100%;">
                <option value="0">{{ __('Normal User') }}</option>
                <option value="14">{{ __('Administrator') }}</option>
            </select>
        </div>

        <div class="form-group">
            <label>{{ __('Assign to Device') }}</label>
            <select name="device_id" class="glass-input" style="width: 100%;">
                @foreach($devices as $device)
                    <option value="{{ $device->id }}">{{ $device->sn }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>{{ __('Password (Optional)') }}</label>
            <input type="password" name="password" class="glass-input" style="width: 100%;">
            <small style="color: var(--text-muted);">{{ __('Leave empty if not using password login.') }}</small>
        </div>

        <div class="form-group">
            <label>{{ __('Card Number (Optional RFID)') }}</label>
            <input type="text" name="card_number" class="glass-input" style="width: 100%;" value="{{ old('card_number') }}">
        </div>

        <button type="submit" class="glass-btn" style="width: 100%; justify-content: center; margin-top: 10px;"><i class="fas fa-save"></i> {{ __('Save & Sync to Device') }}</button>
    </form>
</div>

@endsection

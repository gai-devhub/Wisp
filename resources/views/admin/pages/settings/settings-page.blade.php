@extends('admin.base-admin')
@php
    $adminSettings = auth()->check() ? (auth()->user()->settings ?? []) : [];
    $privacyEnabled = $adminSettings['privacy_blur_enabled'] ?? false;
@endphp

@section('admin-section', 'settings-page')

@section('content')
<div class="content-section active" id="settings-page">
    <div class="general-dashboard-wrapper">
        
        <!-- Header Section -->
        <div class="settings-header-box">
            <div>
                <h2 class="settings-title">
                    Global Page Settings
                </h2>
                <p class="settings-subtitle">Manage global preferences, themes, and encryption statuses.</p>
            </div>
        </div>

        <div class="general-layout-grid">
            
            <!-- Left Column -->
            <div class="flex-col-gap">
                
                {{-- Support Contact Settings --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Support Contact Settings</h4>
                            <p>Contact options displayed to blocked users.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.contactSettings') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="contact_email">Support Email</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $systemSettings['contact_email'] ?? '') }}" placeholder="admin@example.com" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="contact_phone">Support Phone</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $systemSettings['contact_phone'] ?? '') }}" placeholder="+1234567890" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="contact_whatsapp">WhatsApp Number</label>
                            <input type="text" name="contact_whatsapp" id="contact_whatsapp" value="{{ old('contact_whatsapp', $systemSettings['contact_whatsapp'] ?? '') }}" placeholder="+1234567890" class="form-control">
                        </div>
                        <button type="submit" class="admin-btn admin-btn-primary settings-action-btn"><i class="fas fa-save mr-2"></i> Save Contact Info</button>
                    </form>
                </div>

            </div>
            
            <!-- Right Column -->
            <div class="flex-col-gap">

                {{-- Privacy Mode --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon">
                            <i class="fas fa-eye-slash"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Privacy Mode</h4>
                            <p>Blur message previews in "My Messages" for public browsing.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.privacy_theme') }}" class="m-0">
                        @csrf
                        <input type="hidden" name="privacy_form_submitted" value="1">
                        <label class="check-card-label mb-6">
                            <input type="checkbox" name="privacy_blur_enabled" id="privacyBlur" {{ $privacyEnabled ? 'checked' : '' }} class="check-card-input">
                            <span class="check-card-text">Enable Blur UI</span>
                        </label>
                        <button type="submit" class="admin-btn admin-btn-primary settings-action-btn"><i class="fas fa-shield-alt mr-2"></i> Update Privacy</button>
                    </form>
                </div>

            </div>
        </div>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themePreviews = document.querySelectorAll('.theme-preview');
    const themeInput = document.getElementById('themeInput');
    
    themePreviews.forEach(preview => {
        preview.addEventListener('click', () => {
            themePreviews.forEach(p => p.classList.remove('active'));
            preview.classList.add('active');
            let theme = preview.dataset.theme;
            themeInput.value = theme;
        });
    });
});
</script>
@endsection

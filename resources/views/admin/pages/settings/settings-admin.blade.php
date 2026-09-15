@extends('admin.base-admin')

@section('admin-section', 'settings-user')

@section('content')
<div class="content-section active" id="settings-user">
    <div class="general-dashboard-wrapper">
        
        <!-- Header Section -->
        <div class="settings-header-box">
            <div>
                <h2 class="settings-title">
                    Admin Settings
                </h2>
                <p class="settings-subtitle">Manage your admin account details and security preferences.</p>
            </div>
        </div>

        <div class="general-layout-grid">
            
            <!-- Left Column: Account Info -->
            <div class="flex-col-gap">
                
                <!-- Account Information -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Account Information</h4>
                            <p>Update your profile details and credentials.</p>
                        </div>
                    </div>
                    
                    @php $adminUser = auth()->user(); @endphp
                    <form method="POST" action="{{ route('admin.account') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="adminUsername">Username</label>
                            <input type="text" class="form-control" id="adminUsername" name="username" value="{{ old('username', $adminUser->username ?? '') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="adminEmail">Email Address</label>
                            <input type="email" class="form-control" id="adminEmail" name="email" value="{{ old('email', $adminUser->email ?? '') }}">
                        </div>
                        
                        <div class="grid-col-2-gap mb-3">
                            <div class="form-group mb-0">
                                <label for="adminPassword">New Password</label>
                                <input type="password" class="form-control" id="adminPassword" name="password" placeholder="Leave blank to keep">
                            </div>
                            <div class="form-group mb-0">
                                <label for="adminPasswordConfirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="adminPasswordConfirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Profile Picture</label>
                            <div class="profile-picture-settings">
                                <div class="profile-picture-current">
                                    <img id="adminAvatarPreview" src="{{ route('profile.picture') }}" alt="Current profile" data-fallback-src="{{ asset('img/logo.png') }}" onerror="if(this.dataset.fallbackSrc) this.src=this.dataset.fallbackSrc">
                                </div>
                                <div class="file-upload">
                                    <div class="file-upload-label btn-outline-primary">
                                        <i class="fas fa-cloud-upload-alt"></i> Upload New Image
                                    </div>
                                    <input type="file" class="file-upload-input" name="profile_picture" accept="image/*" id="adminProfilePicture">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="admin-btn admin-btn-primary settings-action-btn mt-3"><i class="fas fa-save mr-2"></i> Update Account Details</button>
                    </form>
                </div>
            </div>
            
            <!-- Right Column: Security & Data -->
            <div class="flex-col-gap">
                
                <!-- Security / 2FA -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>2FA Security Lock</h4>
                            <p>Add an extra layer of protection to your dashboard.</p>
                        </div>
                    </div>
                    
                    <div class="passcode-status-box">
                        <div class="passcode-status-icon {{ !empty($adminUser->settings->login_passcode) ? 'active' : '' }}">
                            <i class="fas {{ !empty($adminUser->settings->login_passcode) ? 'fa-lock' : 'fa-lock-open' }}"></i>
                        </div>
                        <h5 class="storage-item-title">{{ !empty($adminUser->settings->login_passcode) ? 'Dashboard is Protected' : 'Dashboard is Unlocked' }}</h5>
                        <p class="settings-subtitle mb-4">A custom passcode ensures only you can access your private dashboard.</p>
                        
                        <div class="passcode-btn-row">
                            <button type="button" class="admin-btn admin-btn-primary" onclick="openPasscodeModal()">
                                <i class="fas fa-key mr-2"></i>
                                {{ !empty($adminUser->settings->login_passcode) ? 'Update Passcode' : 'Set a Passcode' }}
                            </button>

                            @if(!empty($adminUser->settings->login_passcode))
                                <form method="POST" action="{{ route('admin.settings.passcode') }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="login_passcode" value="">
                                    <input type="hidden" name="login_passcode_confirmation" value="">
                                    <button type="submit" class="admin-btn admin-btn-secondary">
                                        <i class="fas fa-lock-open mr-2"></i> Disable
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Passcode Modal Overlay -->
<div id="passcode-modal" class="passcode-modal-backdrop">
    <div class="passcode-modal-card">
        <button type="button" onclick="closePasscodeModal()" class="broadcast-modal-close modal-close-pos" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>

        <form id="passcode-form" method="POST" action="{{ route('admin.settings.passcode') }}">
            @csrf
            <!-- Step 1 -->
            <div id="passcode-step-1" class="text-center">
                <div class="passcode-icon-lg">
                    <i class="fas fa-lock"></i>
                </div>
                <h4 class="settings-title mb-2">Hello {{ explode(' ', $adminUser->name ?? $adminUser->username)[0] }},</h4>
                <p class="settings-subtitle mb-6">Please enter a secure passcode to lock your dashboard.</p>

                <input type="password" id="modal-passcode" name="login_passcode" class="form-control passcode-input-lg" placeholder="••••" minlength="4" required>
                
                <button type="button" class="admin-btn admin-btn-primary settings-action-btn" onclick="nextPasscodeStep()">Next <i class="fas fa-arrow-right ml-2"></i></button>
            </div>

            <!-- Step 2 -->
            <div id="passcode-step-2" class="text-center hidden">
                <div class="passcode-icon-lg">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 class="settings-title mb-2">Confirm Passcode</h4>
                <p class="settings-subtitle mb-6">Re-enter the passcode to verify it.</p>
                
                <input type="password" id="modal-passcode-confirm" name="login_passcode_confirmation" class="form-control passcode-input-lg" placeholder="••••" minlength="4" required>
                
                <div class="flex-gap-4">
                    <button type="button" class="admin-btn admin-btn-secondary flex-1" onclick="prevPasscodeStep()">Back</button>
                    <button type="submit" class="admin-btn admin-btn-primary flex-1"><i class="fas fa-key mr-2"></i> Update Passcode</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('adminProfilePicture').addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('adminAvatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });

    function openPasscodeModal() {
        document.getElementById('passcode-modal').style.display = 'flex';
        document.getElementById('modal-passcode').value = '';
        document.getElementById('modal-passcode-confirm').value = '';
        document.getElementById('passcode-step-1').style.display = 'block';
        document.getElementById('passcode-step-2').style.display = 'none';
        setTimeout(() => document.getElementById('modal-passcode').focus(), 100);
    }

    function closePasscodeModal() {
        document.getElementById('passcode-modal').style.display = 'none';
    }

    function nextPasscodeStep() {
        const p1 = document.getElementById('modal-passcode');
        if(p1.value.length >= 4) {
            document.getElementById('passcode-step-1').style.display = 'none';
            document.getElementById('passcode-step-2').style.display = 'block';
            document.getElementById('modal-passcode-confirm').focus();
        } else {
            p1.reportValidity();
        }
    }

    function prevPasscodeStep() {
        document.getElementById('passcode-step-1').style.display = 'block';
        document.getElementById('passcode-step-2').style.display = 'none';
        document.getElementById('modal-passcode-confirm').value = '';
        document.getElementById('modal-passcode').focus();
    }

    document.getElementById('modal-passcode').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            nextPasscodeStep();
        }
    });
</script>
@endsection

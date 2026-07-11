@extends('user.base-user')

@section('user-section', 'settings')

@section('content')
<div class="content-section active" id="settings">
    <!-- Header Section -->
    <div  class="settings-inline-1">
        <h2  class="settings-inline-2">
            <i class="fas fa-cog text-primary settings-inline-3" ></i> 
            Settings
        </h2>
    </div>
    <div class="card settings-inline-4" >
        <div class="card-body">
            <div class="settings-grid">
                <div class="setting-card">
                    <h4>Account Settings</h4>
                    <form id="account-settings-form" method="POST" action="{{ route('settings.account') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Profile Picture</label>
                            <div class="profile-picture-settings">
                                <div class="profile-picture-current">
                                    <img id="current-profile-picture" src="{{ route('profile.picture') }}" alt="Current profile" data-fallback-src="https://ui-avatars.com/api/?name={{ urlencode($user->username ?? 'User') }}&color=7F9CF5&background=EBF4FF" onerror="if(this.dataset.fallbackSrc) this.src=this.dataset.fallbackSrc">
                                    <span class="profile-picture-hint">Current</span>
                                </div>
                                <div class="file-upload">
                                    <div class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i> Choose an image
                                    </div>
                                    <input type="file" class="file-upload-input" name="profile_picture" accept="image/*" id="profile_picture_input">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Account</button>
                    </form>
                </div>

                <div class="setting-card">
                    <h4>Page Settings</h4>
                    <form id="page-settings-form" method="POST" action="{{ route('settings.page') }}">
                        @csrf
                        <div class="form-group">
                            <label for="page-expiry">Page Expiry (Hours)</label>
                            <input type="number" class="form-control" name="page_expiry" id="page-expiry" value="{{ old('page_expiry', $userSettings->page_expiry ?? 24) }}" min="1" max="8760" title="1-8760 (e.g. 24 = 1 day, 168 = 1 week)">
                        </div>
                        <div class="form-group">
                            <label for="auto-delete">Auto-delete Expired Pages</label>
                            <select class="form-control" id="auto-delete" name="auto_delete">
                                <option value="yes" {{ ($userSettings->auto_delete_expired ?? true) ? 'selected' : '' }}>Yes</option>
                                <option value="no" {{ ($userSettings->auto_delete_expired ?? true) ? '' : 'selected' }}>No</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>

                <div class="setting-card">
                    <h4>Notification Settings</h4>
                    <form id="notification-settings-form" method="POST" action="{{ route('settings.notifications') }}">
                        @csrf
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="page_view_alerts" value="1" {{ ($userSettings->page_view_alerts ?? true) ? 'checked' : '' }}>
                                Page view alerts
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="whatsapp_notifications" value="1" {{ ($userSettings->whatsapp_notifications ?? true) ? 'checked' : '' }}>
                                ShareMessages notifications
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Preferences</button>
                    </form>
                </div>
            </div></br>

            <div class="mt-4 settings-inline-5" >
                <form id="reset-settings-form" method="POST" action="{{ route('settings.reset') }}" onsubmit="return confirmFormSubmit(event, this, 'Are you sure you want to reset all settings to default values?', 'confirm');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Reset to Default Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

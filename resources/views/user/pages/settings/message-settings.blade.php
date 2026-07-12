@extends('user.base-user')

@section('user-section', 'message-settings')

@section('content')
    <div class="content-section active" id="message-settings">

        <!-- Header Section -->
        <div
             class="message-settings-inline-1">
            <h2
                 class="message-settings-inline-2">
                <i class="fas fa-shield-alt text-primary message-settings-inline-3" ></i>
                Message Settings & Lifecycle
            </h2>
            <div  class="message-settings-inline-4">
                <a href="{{ route('user.vault.page') }}"
                     class="message-settings-inline-5">
                    <i class="fas fa-lock text-warning"></i> <span>The Vault</span>
                </a>
                <a href="{{ route('user.trash.page') }}"
                     class="message-settings-inline-6">
                    <i class="fas fa-trash-restore"></i> <span>Recently Deleted</span>
                </a>
            </div>
        </div>

        <!-- Storage Breakdown Card -->
        <div class="card message-settings-inline-7"
            >
            <div class="card-header message-settings-inline-8" >
                <h3  class="message-settings-inline-9">
                    <i class="fas fa-hdd text-primary"></i> Media Storage Breakdown
                </h3>
            </div>
            <div class="card-body message-settings-inline-10" >
                @php
                    $imagesMB = round(($imagesSize ?? 0) / 1048576, 2);
                    $audioMB = round(($audioSize ?? 0) / 1048576, 2);
                    $totalMB = $imagesMB + $audioMB;
                    $limitMB = 1024;
                    $percentUsed = min(100, ($totalMB / $limitMB) * 100);
                @endphp

                <div  class="message-settings-inline-11">
                    <span  class="message-settings-inline-12">Storage Usage</span>
                    <span  class="message-settings-inline-13">
                        <span  class="message-settings-inline-14">{{ $totalMB }} MB</span> / {{ $limitMB }} MB
                    </span>
                </div>

                <!-- Progress Bar -->
                <div
                     class="message-settings-inline-15">
                    <div
                         class="message-settings-inline-16" style="width: {{ $percentUsed }}%;">
                    </div>
                </div>

                <!-- Media Types Grid -->
                <div  class="message-settings-inline-17">
                    <!-- Image Card -->
                    <div
                         class="message-settings-inline-18">
                        <div
                             class="message-settings-inline-19">
                            <i class="fas fa-image"></i>
                        </div>
                        <div>
                            <h5  class="message-settings-inline-20">Images
                            </h5>
                            <div  class="message-settings-inline-21">{{ $imagesMB }} MB
                                Used</div>
                        </div>
                    </div>

                    <!-- Audio Card -->
                    <div
                         class="message-settings-inline-22">
                        <div
                             class="message-settings-inline-23">
                            <i class="fas fa-music"></i>
                        </div>
                        <div>
                            <h5  class="message-settings-inline-24">Audio /
                                Music</h5>
                            <div  class="message-settings-inline-25">{{ $audioMB }} MB
                                Used</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Settings Grid -->
        <div  class="message-settings-inline-26">

            <!-- Notifications Box -->
            <div class="card message-settings-inline-27"
                >
                <div class="card-header message-settings-inline-28"
                    >
                    <h3
                         class="message-settings-inline-29">
                        <i class="fas fa-bell text-primary"></i> Notifications
                    </h3>
                </div>
                <div class="card-body message-settings-inline-30" >
                    <form method="POST" action="{{ route('settings.notifications') }}">
                        @csrf
                        <div class="form-group message-settings-inline-31" >
                            <label
                                 class="message-settings-inline-32">
                                <input type="checkbox" name="page_view_alerts" value="1" {{ ($userSettings->page_view_alerts ?? true) ? 'checked' : '' }}
                                    class="msg-setting-checkbox">
                                <span  class="message-settings-inline-33">Page view alerts</span>
                            </label>
                        </div>
                        <div class="form-group message-settings-inline-34" >
                            <label
                                 class="message-settings-inline-35">
                                <input type="checkbox" name="whatsapp_notifications" value="1" {{ ($userSettings->whatsapp_notifications ?? true) ? 'checked' : '' }}
                                    class="msg-setting-checkbox">
                                <span  class="message-settings-inline-36">ShareMessages notifications</span>
                            </label>
                        </div>
                        <button type="submit" class="btn message-settings-inline-37"
                            >Save
                            Preferences</button>
                    </form>
                </div>
            </div>

            <!-- Page Configuration Card (moved from General Settings) -->
            <div class="card message-settings-inline-38">
                <div class="card-header message-settings-inline-39">
                    <h3 class="message-settings-inline-40">
                        <i class="fas fa-cog text-primary"></i> Page Configuration
                    </h3>
                </div>
                <div class="card-body message-settings-inline-41">
                    <p style="color:#64748b;font-size:0.88rem;margin-bottom:16px;">Set default expiry rules and auto-delete behaviours for generated pages.</p>
                    <form method="POST" action="{{ route('user.settings.general') }}">
                        @csrf
                        <div class="form-group message-settings-inline-42">
                            <label class="message-settings-inline-43">Page Expiry Duration <span class="message-settings-inline-44">(Hours)</span></label>
                            <input type="number" class="form-control" name="page_expiry"
                                   value="{{ old('page_expiry', $userSettings->page_expiry ?? 24) }}"
                                   min="1" max="8760">
                        </div>
                        <div class="form-group message-settings-inline-42" style="margin-top:16px;">
                            <label class="message-settings-inline-43">Auto-Delete Expired Pages After</label>
                            <select class="form-control" name="auto_delete">
                                <option value="never"        {{ ($userSettings->auto_delete_expired ?? 'never') === 'never'        ? 'selected' : '' }}>Never (Keep them)</option>
                                <option value="immediately"  {{ ($userSettings->auto_delete_expired ?? 'never') === 'immediately'  ? 'selected' : '' }}>Immediately</option>
                                <option value="1_hour"       {{ ($userSettings->auto_delete_expired ?? 'never') === '1_hour'       ? 'selected' : '' }}>An hour after</option>
                                <option value="1_day"        {{ ($userSettings->auto_delete_expired ?? 'never') === '1_day'        ? 'selected' : '' }}>A day after</option>
                                <option value="1_week"       {{ ($userSettings->auto_delete_expired ?? 'never') === '1_week'       ? 'selected' : '' }}>A week after</option>
                                <option value="1_month"      {{ ($userSettings->auto_delete_expired ?? 'never') === '1_month'      ? 'selected' : '' }}>A month after</option>
                                <option value="2_months"     {{ ($userSettings->auto_delete_expired ?? 'never') === '2_months'     ? 'selected' : '' }}>2 months after</option>
                                <option value="6_months"     {{ ($userSettings->auto_delete_expired ?? 'never') === '6_months'     ? 'selected' : '' }}>6 months after</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:20px;">
                            <i class="fas fa-save"></i> Save Configuration
                        </button>
                    </form>
                </div>
            </div>

            <!-- Message Lifecycle Rules Box -->
            <div class="card message-settings-inline-38"
                >
                <div class="card-header message-settings-inline-39"
                    >
                    <h3
                         class="message-settings-inline-40">
                        <i class="fas fa-history text-secondary"></i> Message Lifecycle
                    </h3>
                </div>
                <div class="card-body message-settings-inline-41" >
                    <form method="POST" action="{{ route('settings.lifecycle.update') }}">
                        @csrf
                        <div class="form-group message-settings-inline-42" >
                            <label  class="message-settings-inline-43">Set Vault PIN <span
                                     class="message-settings-inline-44">(4-6
                                    digits)</span></label>
                            <input type="password" class="form-control message-settings-inline-45" name="vault_pin"
                                placeholder="Leave blank to keep current PIN"
                                >
                            <small
                                 class="message-settings-inline-46">Required
                                to access Locked Conversations.</small>
                        </div>
                        <div class="form-group message-settings-inline-47" >
                            <label  class="message-settings-inline-48">Auto-Archive After <span
                                     class="message-settings-inline-49">(Days)</span></label>
                            <input type="number" class="form-control" name="auto_archive_days"
                                value="{{ old('auto_archive_days', $userSettings->auto_archive_days ?? '') }}" min="1"
                                placeholder="e.g. 30" class="msg-setting-input">
                            <small
                                 class="message-settings-inline-50">Leave
                                blank to disable auto-archive.</small>
                        </div>

                        <div  class="message-settings-inline-51">
                            <button type="submit" class="btn message-settings-inline-52"
                                >Save
                                Rules</button>
                                <a href="{{ route('settings.lifecycle.run_archive') }}" class="btn message-settings-inline-53"
                                
                                onclick="event.preventDefault(); showTypedConfirmModal('Are you sure you want to manually run the archive process now?', () => window.location.href = this.href);">Run
                                Auto-Archive</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Reset Data -->
        <div  class="message-settings-inline-54">
            <form method="POST" action="{{ route('settings.reset') }}"
                onsubmit="return confirmFormSubmit(event, this, 'WARNING: Are you sure you want to reset all settings to default values?');">
                @csrf
                <button type="submit"
                     class="message-settings-inline-55">
                    <i class="fas fa-exclamation-triangle"></i> Reset to Default Settings
                </button>
            </form>
        </div>

    </div>
@endsection
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
                @if(session('vault_unlocked'))
                    <a href="{{ route('user.vault.page') }}" class="message-settings-inline-5">
                        <i class="fas fa-lock text-warning"></i> <span>The Vault</span>
                    </a>
                @else
                    <button type="button" class="message-settings-inline-5" onclick="openVaultAuthModal()" style="cursor: pointer; border: none;">
                        <i class="fas fa-lock text-warning"></i> <span>The Vault</span>
                    </button>
                @endif
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
                             class="message-settings-inline-19">
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
        <form method="POST" action="{{ route('settings.messages.update_all') }}">
            @csrf
            <div class="message-settings-inline-26">

                <!-- Notifications Box -->
                <div class="card message-settings-inline-27">
                    <div class="card-header message-settings-inline-28">
                        <h3 class="message-settings-inline-29">
                            <i class="fas fa-bell text-primary"></i> Notifications
                        </h3>
                    </div>
                    <div class="card-body message-settings-inline-30">
                        <div class="form-group message-settings-inline-31">
                            <label class="message-settings-inline-32">
                                <input type="checkbox" name="page_view_alerts" value="1" {{ ($userSettings->page_view_alerts ?? true) ? 'checked' : '' }}
                                    class="msg-setting-checkbox">
                                <span class="message-settings-inline-33">Page view alerts</span>
                            </label>
                        </div>
                        <div class="form-group message-settings-inline-34">
                            <label class="message-settings-inline-35">
                                <input type="checkbox" name="whatsapp_notifications" value="1" {{ ($userSettings->whatsapp_notifications ?? true) ? 'checked' : '' }}
                                    class="msg-setting-checkbox">
                                <span class="message-settings-inline-36">ShareMessages notifications</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Page Configuration Card -->
                <div class="card message-settings-inline-38">
                    <div class="card-header message-settings-inline-39">
                        <h3 class="message-settings-inline-40">
                            <i class="fas fa-cog text-primary"></i> Page Configuration
                        </h3>
                    </div>
                    <div class="card-body message-settings-inline-41">
                        <p style="color:#64748b;font-size:0.88rem;margin-bottom:16px;">Set default expiry rules and auto-delete behaviours for generated pages.</p>
                        <div class="form-group message-settings-inline-42">
                            <label class="message-settings-inline-43">Page Expiry Duration</label>
                            <select class="form-control" name="page_expiry">
                                @php $selectedExpiry = (int) old('page_expiry', $userSettings->page_expiry ?? 24); @endphp
                                <option value="1"  {{ $selectedExpiry === 1  ? 'selected' : '' }}>1 Hour</option>
                                <option value="6"  {{ $selectedExpiry === 6  ? 'selected' : '' }}>6 Hours</option>
                                <option value="12" {{ $selectedExpiry === 12 ? 'selected' : '' }}>12 Hours</option>
                                <option value="24" {{ $selectedExpiry === 24 ? 'selected' : '' }}>24 Hours</option>
                                <option value="48" {{ $selectedExpiry === 48 ? 'selected' : '' }}>48 Hours</option>
                                <option value="72" {{ $selectedExpiry === 72 ? 'selected' : '' }}>72 Hours</option>
                            </select>
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
                    </div>
                </div>

                <!-- Message Lifecycle Rules Box -->
                <div class="card message-settings-inline-38">
                    <div class="card-header message-settings-inline-39">
                        <h3 class="message-settings-inline-40">
                            <i class="fas fa-history text-secondary"></i> Message Lifecycle
                        </h3>
                    </div>
                    <div class="card-body message-settings-inline-41">
                        <div class="form-group message-settings-inline-42">
                            <label class="message-settings-inline-43">Set Vault PIN <span class="message-settings-inline-44">(4-6 digits)</span></label>
                            <input type="text" class="form-control message-settings-inline-45" name="vault_pin"
                                value="{{ $userSettings->masked_vault_pin ?? '' }}"
                                placeholder="Leave blank to keep current PIN"
                                onfocus="this.type='password'; if(this.value.includes('*')) { this.value=''; }"
                                onblur="if(this.value==='') { this.value='{{ $userSettings->masked_vault_pin ?? '' }}'; this.type='text'; }"
                                >
                            <small class="message-settings-inline-46">Required to access Locked Conversations.</small>
                        </div>
                        <div class="form-group message-settings-inline-47">
                            <label class="message-settings-inline-48">Auto-Archive After</label>
                            @php
                                $selectedAutoArchive = old('auto_archive_days', $userSettings->auto_archive_days ?? '');
                            @endphp
                            <select class="form-control" name="auto_archive_days">
                                <option value="" {{ ($selectedAutoArchive === '' || $selectedAutoArchive === null) ? 'selected' : '' }}>Never (Disable auto-archive)</option>
                                <option value="30" {{ (string)$selectedAutoArchive === '30' ? 'selected' : '' }}>1 Month</option>
                                <option value="180" {{ (string)$selectedAutoArchive === '180' ? 'selected' : '' }}>6 Months</option>
                                <option value="365" {{ (string)$selectedAutoArchive === '365' ? 'selected' : '' }}>1 Year</option>
                                @if($selectedAutoArchive && !in_array((string)$selectedAutoArchive, ['', '30', '180', '365']))
                                    <option value="{{ $selectedAutoArchive }}" selected>{{ $selectedAutoArchive }} Days</option>
                                @endif
                            </select>
                            <small class="message-settings-inline-50">Threshold before messages are auto-archived.</small>
                        </div>

                        <div class="message-settings-inline-51" style="margin-top: 16px;">
                            <a href="{{ route('settings.lifecycle.run_archive') }}" class="btn message-settings-inline-53"
                                style="width: 100%; text-align: center; justify-content: center; display: inline-flex;"
                                onclick="event.preventDefault(); showTypedConfirmModal('Are you sure you want to manually run the archive process now?', () => window.location.href = this.href);">
                                <i class="fas fa-archive" style="margin-right: 6px;"></i> Run Auto-Archive
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Save Button at the far right corner on top of Reset -->
            <div style="margin-top: 28px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 0.95rem; border-radius: 12px; font-weight: 600;">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> Save All Settings
                </button>
            </div>
        </form>

        <!-- Reset Data -->
        <div class="message-settings-inline-54" style="margin-top: 12px;">
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

    <!-- Vault Auth Floating Popup Modal -->
    <div id="vaultAuthModal" class="custom-modal" onclick="if(event.target===this) closeVaultAuthModal();" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.65); z-index: 9990; justify-content: center; align-items: center; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
        <div class="custom-modal-content" style="background: #ffffff; max-width: 420px; width: 90%; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; position: relative; animation: vaultModalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
            <!-- Header -->
            <div style="padding: 28px 24px 18px 24px; text-align: center; border-bottom: 1px solid #f1f5f9; position: relative; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                <div style="width: 56px; height: 56px; background: rgba(245, 158, 11, 0.12); color: #d97706; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 24px; margin: 0 auto 12px; box-shadow: inset 0 0 0 1px rgba(245, 158, 11, 0.2);">
                    <i class="fas fa-lock"></i>
                </div>
                <h3 style="color: #0f172a; font-size: 1.25rem; font-weight: 700; margin: 0 0 6px 0;">The Vault</h3>
                <p style="margin: 0; font-size: 0.875rem; color: #64748b; line-height: 1.4;">Enter your Vault PIN to access locked conversations.</p>
                <button type="button" onclick="closeVaultAuthModal()" style="position: absolute; top: 16px; right: 16px; background: rgba(241, 245, 249, 0.8); border: none; font-size: 1.2rem; color: #64748b; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">&times;</button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('user.vault.auth') }}">
                @csrf
                <div style="padding: 24px;">
                    @if(session('error'))
                        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 14px; border-radius: 10px; font-size: 0.875rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-exclamation-circle"></i> <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    <div style="margin-bottom: 24px;">
                        <input type="password" class="form-control" name="pin" placeholder="••••" required autofocus autocomplete="off" style="font-size: 1.5rem; letter-spacing: 8px; padding: 12px; border-radius: 12px; text-align: center; border: 1.5px solid #cbd5e1; width: 100%; box-sizing: border-box; background: #f8fafc; transition: all 0.2s ease;" onfocus="this.style.borderColor='#2563eb'; this.style.background='#ffffff';" onblur="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" onclick="closeVaultAuthModal()" style="flex: 1; padding: 12px; border-radius: 12px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; cursor: pointer; font-weight: 600; font-size: 0.95rem; transition: background 0.2s ease;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                            <i class="fas fa-unlock"></i> Unlock
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes vaultModalPop {
            0% { opacity: 0; transform: scale(0.92) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>

    <script>
        function openVaultAuthModal() {
            var modal = document.getElementById('vaultAuthModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeVaultAuthModal() {
            var modal = document.getElementById('vaultAuthModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            openVaultAuthModal();
        });
        @endif
    </script>
@endsection
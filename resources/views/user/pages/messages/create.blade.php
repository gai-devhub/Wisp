@extends('user.base-user')

@section('user-section', 'create')

@section('content')
    <div class="content-section active" id="create">
        <form id="create-message-form" method="POST" action="{{ route('messages.store') }}"
            data-default-action="{{ route('messages.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="">
            <input type="hidden" name="_edit_message_id" id="edit-message-id" value="">
            <!-- Header Section -->
            <div class="messages-header-toolbar">
                <h2 class="messages-header-title">
                    <i class="fas fa-plus-circle text-primary messages-header-icon"></i>
                    Create Message
                </h2>
                @if(!($isPremium ?? false))
                    @php $msgCount = auth()->user()->freeMessageCount(); $remaining = max(0, 5 - $msgCount); @endphp
                    <span class="free-msg-counter {{ $remaining <= 1 ? 'free-msg-danger' : ($remaining <= 2 ? 'free-msg-warning' : '') }}">
                        <i class="fas fa-envelope"></i>
                        {{ $remaining }} free message{{ $remaining !== 1 ? 's' : '' }} remaining
                        &mdash; <a href="{{ route('user.billing.index') }}">Upgrade</a>
                    </span>
                @endif
            </div>
            <div class="card messages-form-card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="message-type">Message Type</label>
                        <input type="text" class="form-control" id="message-type" name="message_type" required maxlength="30" pattern="[A-Za-z\s]+" title="Letters and spaces only" value="{{ old('message_type') }}" placeholder="e.g. Birthday Message">
                    </div>
                    <div class="form-group">
                        <label for="page-title">Page Title</label>
                        <input type="text" class="form-control" id="page-title" name="page_title"
                            placeholder="Enter preferred view title" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="recipient-full-name">Recipient's Full name</label>
                        <input type="text" class="form-control" id="recipient-full-name" name="recipient_full_name"
                            placeholder="Enter recipient's full name" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name">Recipient's Special Name</label>
                        <input type="text" class="form-control" id="recipient-name" name="recipient_name"
                            placeholder="Enter recipient's special name" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="greeting">Greeting</label>
                        <input type="text" class="form-control" id="greeting" name="greeting" placeholder="Eg. Hello there"
                            value="Hello there" required>
                    </div>
                    <div class="form-group">
                        <label for="wish-message">Message Content</label>
                        <textarea class="form-control" id="wish-message" name="wish_message" rows="6"
                            placeholder="Write your heartfelt message here..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="Last-note">Last Notes</label>
                        <input type="text" class="form-control" id="Last-note" name="last_note"
                            placeholder="eg. Happy birthday, love you" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="receiving-date">Receiving Date</label>
                        <input type="date" class="form-control" id="receiving-date" name="receiving_date" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="sender-name">Your Name</label>
                        <input type="text" class="form-control" id="sender-name" name="sender_name"
                            placeholder="Enter your name"
                            value="{{ old('sender_name', $user->name ?? $user->username ?? '') }}" required>
                    </div>

                    <div class="form-group vault-section-container">
                        <div class="custom-control custom-checkbox vault-checkbox-flex">
                            <input type="checkbox" class="custom-control-input vault-checkbox-input" id="is_vaulted" name="is_vaulted" value="1"
                                onchange="toggleVaultPin(this)">
                            <label class="custom-control-label vault-checkbox-label" for="is_vaulted">Vault this message for security</label>
                        </div>
                        <div id="vault-pin-container" class="vault-pin-wrap">
                            <label for="specific_vault_pin">Specific Vault PIN</label>
                            <input type="password" class="form-control vault-pin-input" id="specific_vault_pin" name="specific_vault_pin"
                                placeholder="Enter a secure PIN"></br>
                            <small class="text-muted">This PIN will be required by the Recipient to view the message publicly.</small>
                        </div>
                    </div>

                    <button type="submit" name="action" value="draft" class="btn btn-primary">Save Draft</button>
                </div>
            </div>
        </form>
    </div>

@push('scripts')
    <script>
        function toggleVaultPin(checkbox) {
            const container = document.getElementById('vault-pin-container');
            const input = document.getElementById('specific_vault_pin');
            if (checkbox.checked) {
                container.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var aiTitle = sessionStorage.getItem('ai_generated_title');
            var aiRecipient = sessionStorage.getItem('ai_generated_recipient');
            var aiBody = sessionStorage.getItem('ai_generated_body');
            
            if (aiTitle || aiRecipient || aiBody) {
                if (aiTitle) {
                    var titleEl = document.getElementById('page-title');
                    if(titleEl) titleEl.value = aiTitle;
                }
                if (aiRecipient) {
                    var recFullEl = document.getElementById('recipient-full-name');
                    var recShortEl = document.getElementById('recipient-name');
                    if(recFullEl) recFullEl.value = aiRecipient;
                    if(recShortEl && !recShortEl.value) recShortEl.value = aiRecipient;
                }
                if (aiBody) {
                    var msgEl = document.getElementById('wish-message');
                    if(msgEl) msgEl.value = aiBody;
                }
                
                // Clear the storage so it doesn't auto-fill on subsequent visits
                sessionStorage.removeItem('ai_generated_title');
                sessionStorage.removeItem('ai_generated_recipient');
                sessionStorage.removeItem('ai_generated_body');
            }
        });
    </script>
@endpush

@push('styles')
    <style>
    .free-msg-counter {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f0fdf4; border: 1px solid #86efac;
        color: #166534; border-radius: 999px; padding: 4px 14px;
        font-size: 12px; font-weight: 600;
    }
    .free-msg-counter a { color: #15803d; font-weight: 700; }
    .free-msg-warning { background: #fffbeb; border-color: #fcd34d; color: #92400e; }
    .free-msg-warning a { color: #b45309; }
    .free-msg-danger { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }
    .free-msg-danger a { color: #b91c1c; }
    </style>

@endpush
@endsection

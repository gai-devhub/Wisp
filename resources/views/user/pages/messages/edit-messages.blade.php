@extends('user.base-user')

@section('user-section', 'edit')

@section('content')
    <div class="content-section active" id="edit">
        <form id="edit-message-form" method="POST" action="{{ route('messages.update', $message->id) }}">
            @csrf
            @method('PUT')

            <!-- Header Section -->
            <div class="messages-header-toolbar">
                <h2 class="messages-header-title">
                    <i class="fas fa-pen-to-square text-primary messages-header-icon"></i>
                    Edit Message
                </h2>
            </div>
            <div class="card messages-form-card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="message-type">Message Type</label>
                        <input type="text" class="form-control" id="message-type" name="message_type" required maxlength="30" pattern="[A-Za-z\s]+" title="Letters and spaces only" value="{{ old('message_type', $message->message_type ?? '') }}" placeholder="e.g. Birthday Message">
                    </div>
                    <div class="form-group">
                        <label for="page-title">Message Title</label>
                        <input type="text" class="form-control" id="page-title" name="page_title"
                            value="{{ old('page_title', $message->title) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="recipient-full-name">Recipient's Full name</label>
                        <input type="text" class="form-control" id="recipient-full-name" name="recipient_full_name"
                            value="{{ old('recipient_full_name', $message->recipient_name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name">Recipient's Special Name</label>
                        <input type="text" class="form-control" id="recipient-name" name="recipient_name"
                            value="{{ old('recipient_name', $message->recipient_special_name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="greeting">Greeting</label>
                        <input type="text" class="form-control" id="greeting" name="greeting"
                            value="{{ old('greeting', $message->greeting) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="wish-message">Message Content</label>
                        <textarea class="form-control" id="wish-message" name="wish_message" rows="6"
                            required>{{ old('wish_message', $message->message ?? $message->wish_message) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="Last-note">Last Notes</label>
                        <input type="text" class="form-control" id="Last-note" name="last_note"
                            value="{{ old('last_note', $message->last_note) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="receiving-date">Receiving Date</label>
                        <input type="date" class="form-control" id="receiving-date" name="receiving_date"
                            value="{{ old('receiving_date', $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('Y-m-d') : '') }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="sender-name">Your Name</label>
                        <input type="text" class="form-control" id="sender-name" name="sender_name"
                            value="{{ old('sender_name', $message->sender_name) }}" required>
                    </div>

                    <div class="form-group vault-section-container">
                        <div class="custom-control custom-checkbox vault-checkbox-flex">
                            <input type="checkbox" class="custom-control-input vault-checkbox-input" id="is_vaulted" name="is_vaulted" value="1"
                                onchange="toggleVaultPin(this)" {{ old('is_vaulted', $message->is_vaulted) ? 'checked' : '' }}>
                            <label class="custom-control-label vault-checkbox-label" for="is_vaulted">Vault this message for security</label>
                        </div>
                        <div id="vault-pin-container" class="vault-pin-wrap" style="display: {{ old('is_vaulted', $message->is_vaulted) ? 'block' : 'none' }};">
                            <label for="specific_vault_pin">Specific Vault PIN</label>
                            <input type="password" class="form-control vault-pin-input" id="specific_vault_pin" name="specific_vault_pin"
                                placeholder="{{ $message->is_vaulted ? 'Enter new PIN to change, or leave blank to keep existing' : 'Enter a secure PIN' }}"
                                class="edit-msg-max-w"></br>
                            <small class="text-muted">This PIN will be required by the recipient to view the message publicly.</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Message</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleVaultPin(checkbox) {
            const container = document.getElementById('vault-pin-container');
            const input = document.getElementById('specific_vault_pin');
            if (checkbox.checked) {
                container.style.display = 'block';
                @if(!$message->is_vaulted)
                    input.setAttribute('required', 'required');
                @endif
                    } else {
                container.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        }
    </script>
@endsection
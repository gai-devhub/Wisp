@extends('user.base-user')

@section('user-section', 'vault')

@section('content')
<div class="content-section active" id="vault">
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-lock text-warning messages-header-icon"></i> 
            Vaulted Conversations
        </h2>
        <div class="vault-header-actions">
            <a href="{{ route('user.settings.messages.page') }}" class="vault-lock-btn">
                <i class="fas fa-lock text-muted"></i> Lock
            </a>
        </div>
    </div>

    <div class="card vault-messages-card">
        <div class="card-body p-0">
            @if(($messages ?? collect())->isEmpty())
                <div class="empty-table-cell" style="padding: 60px 20px;">
                    <div class="empty-table-icon-wrap">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h4 class="empty-table-title" style="font-size: 0.95rem;">Your vault is currently empty.</h4>
                    <p class="empty-table-desc" style="font-size: 0.85rem;">Locked messages will appear here.</p>
                </div>
            @else
                <div class="table-responsive-wrap">
                    <table class="messages-table responsive-table messages-table--scroll">
                        <thead>
                            <tr>
                                <th>Recipient</th>
                                <th>Title</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $msg)
                            <tr>
                                <td data-label="Recipient">{{ $msg->recipient_name }}</td>
                                <td data-label="Title">{{ $msg->title }}</td>
                                <td data-label="Date Added">{{ $msg->created_at->format('M d, Y') }}</td>
                                <td data-label="Actions" class="actions-cell">
                                    <div class="vault-actions-flex">
                                        <form action="{{ route('user.lifecycle.toggle_vault', $msg->id) }}" method="POST" class="vault-action-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning vault-action-btn">
                                                <i class="fas fa-unlock"></i> Unvault
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-info manage-pin-btn vault-action-btn" 
                                            data-id="{{ $msg->id }}" 
                                            data-pin="{{ $msg->specific_vault_pin ? Crypt::decryptString($msg->specific_vault_pin) : '' }}" 
                                            data-bs-toggle="modal" data-bs-target="#managePinModal">
                                            <i class="fas fa-key"></i> PIN
                                        </button>
                                        <a href="{{ route('user.edit-messages.page', $msg->id) }}" class="btn btn-sm btn-action btn-view vault-action-btn">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Manage PIN Modal -->
<div class="modal fade" id="managePinModal" tabindex="-1" aria-labelledby="managePinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content vault-modal-content">
            <form id="manage-pin-form" method="POST" action="">
                @csrf
                <div class="modal-header vault-modal-header">
                    <h5 class="modal-title vault-modal-title" id="managePinModalLabel">Manage Specific Vault PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body vault-modal-body">
                    <div class="form-group mb-0">
                        <label for="modal_specific_vault_pin" class="vault-modal-label">Specific Vault PIN</label>
                        <input type="text" class="form-control vault-modal-input" id="modal_specific_vault_pin" name="specific_vault_pin" placeholder="Enter a secure PIN">
                        <small class="text-muted mt-2 d-block">Leave blank to use your general vault PIN. Setting this will require this specific PIN to view the message.</small>
                    </div>
                </div>
                <div class="modal-footer vault-modal-footer">
                    <button type="button" class="btn btn-light vault-modal-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary vault-modal-btn">Save PIN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const managePinBtns = document.querySelectorAll('.manage-pin-btn');
    const managePinForm = document.getElementById('manage-pin-form');
    const pinInput = document.getElementById('modal_specific_vault_pin');

    managePinBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const pin = this.getAttribute('data-pin');
            
            managePinForm.action = `/messages/${id}/vault-pin`;
            pinInput.value = pin;
        });
    });
});
</script>
@endsection

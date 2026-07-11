@extends('user.base-user')

@section('user-section', 'trash')

@section('content')
<div class="content-section active" id="trash">
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-trash-alt text-danger messages-header-icon"></i>
            Trash (Recently Deleted)
        </h2>
    </div>

    <div class="card trash-card">
        <div class="card-body p-0">
            @if($messages->isEmpty())
                <div class="empty-table-cell" style="padding: 60px 20px;">
                    <div class="empty-table-icon-wrap" style="background: rgba(239, 68, 68, 0.08); color: var(--danger);">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h4 class="empty-table-title" style="font-size: 0.95rem;">Trash is empty.</h4>
                    <p class="empty-table-desc" style="font-size: 0.85rem;">No recently deleted messages found.</p>
                </div>
            @else
                <div class="table-responsive-wrap">
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Recipient</th>
                                <th>Deleted</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $msg)
                            <tr>
                                <td data-label="Title">
                                    {{ \Illuminate\Support\Str::limit($msg->title, 15, '...') }}
                                </td>
                                <td data-label="Recipient" class="blur-sensitive">{{ $msg->recipient_name }}</td>
                                <td data-label="Deleted">
                                    @php
                                        $diff = $msg->deleted_at->diff(now());
                                        if ($diff->d > 0) {
                                            $timeStr = $diff->d . 'd ago';
                                        } elseif ($diff->h > 0) {
                                            $timeStr = $diff->h . 'h ago';
                                        } elseif ($diff->i > 0) {
                                            $timeStr = $diff->i . 'm ago';
                                        } else {
                                            $timeStr = 'Just now';
                                        }
                                    @endphp
                                    <span class="status-badge" style="background: rgba(239,68,68,0.1); color: #ef4444; text-transform: uppercase;">
                                        {{ $timeStr }}
                                    </span>
                                </td>
                                <td data-label="Actions" class="actions-cell text-end">
                                    <div class="message-actions-dropdown">
                                        <button type="button" class="message-actions-trigger" aria-expanded="false">
                                            <span class="d-none d-md-inline">Actions</span>
                                            <i class="fas fa-chevron-down d-none d-md-inline"></i>
                                            <i class="fas fa-ellipsis-v d-md-none"></i>
                                        </button>
                                        <div class="message-actions-menu trash-actions-menu" style="display:none;">

                                            {{-- Restore --}}
                                            <form action="{{ route('user.lifecycle.restore', $msg->id) }}" method="POST" class="trash-action-form">
                                                @csrf
                                                <button type="submit" class="trash-menu-item trash-menu-restore">
                                                    <span class="trash-menu-icon" style="background: rgba(16,185,129,0.12); color: #10b981;">
                                                        <i class="fas fa-undo"></i>
                                                    </span>
                                                    <span class="trash-menu-label">Restore</span>
                                                    <i class="fas fa-chevron-right trash-menu-arrow"></i>
                                                </button>
                                            </form>

                                            <div class="trash-menu-divider"></div>

                                            {{-- Delete Permanently --}}
                                            <form action="{{ route('user.lifecycle.force_delete', $msg->id) }}" method="POST" class="trash-action-form"
                                                  onsubmit="return confirmFormSubmit(event, this, 'WARNING: This will permanently delete this message and cannot be undone!', 'confirm');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="trash-menu-item trash-menu-delete">
                                                    <span class="trash-menu-icon" style="background: rgba(239,68,68,0.12); color: #ef4444;">
                                                        <i class="fas fa-trash"></i>
                                                    </span>
                                                    <span class="trash-menu-label">Delete Permanently</span>
                                                    <i class="fas fa-chevron-right trash-menu-arrow"></i>
                                                </button>
                                            </form>

                                        </div>
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

<style>
.trash-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    overflow: visible;
}

/* Neat table headers without wrapping */
.messages-table th {
    white-space: nowrap !important;
    word-break: keep-all !important;
}

/* Dropdown menu overrides for trash */
.trash-actions-menu {
    min-width: 220px;
    padding: 8px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13), 0 2px 8px rgba(0,0,0,0.07);
    border: 1px solid #f1f5f9;
}

.trash-action-form {
    display: block;
    margin: 0;
    padding: 0;
}

.trash-menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 10px 12px;
    border-radius: 10px;
    border: none;
    background: none;
    cursor: pointer;
    text-align: left;
    transition: background 0.15s;
    font-size: 0.92rem;
    font-weight: 500;
    color: #1e293b;
}
.trash-menu-item:hover { background: #f8fafc; }
.trash-menu-delete { color: #ef4444; }
.trash-menu-delete:hover { background: rgba(239,68,68,0.06); }

.trash-menu-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.trash-menu-label { flex: 1; }

.trash-menu-arrow {
    font-size: 0.7rem;
    color: #94a3b8;
}
.trash-menu-delete .trash-menu-arrow { color: #ef4444; }

.trash-menu-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 4px 0;
}
</style>

<script>
// Trash dropdown — reuse the same open/close pattern as my-messages
function closeTrashDropdowns(exceptDropdown) {
    document.querySelectorAll('.message-actions-dropdown.is-open').forEach(function(openDropdown) {
        if (exceptDropdown && openDropdown === exceptDropdown) return;
        openDropdown.classList.remove('is-open');
        var trigger = openDropdown.querySelector('.message-actions-trigger');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        var menu = openDropdown.querySelector('.message-actions-menu');
        if (menu) menu.style.display = 'none';
    });
}

document.querySelectorAll('#trash .message-actions-trigger').forEach(function(trigger) {
    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var dropdown = trigger.closest('.message-actions-dropdown');
        if (!dropdown) return;
        var isOpen = dropdown.classList.contains('is-open');
        closeTrashDropdowns(dropdown);
        dropdown.classList.toggle('is-open', !isOpen);
        trigger.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        var menu = dropdown.querySelector('.message-actions-menu');
        if (menu) {
            menu.style.display = !isOpen ? 'block' : 'none';
            if (!isOpen) {
                var rect = trigger.getBoundingClientRect();
                menu.style.position = 'fixed';
                menu.style.top = (rect.bottom + 6) + 'px';
                var menuWidth = 220;
                var left = rect.right - menuWidth;
                if (left < 8) left = 8;
                menu.style.left = left + 'px';
                menu.style.zIndex = 9999;
            }
        }
    });
});

window.addEventListener('scroll', function(e) {
    if (e.target.closest && e.target.closest('.message-actions-menu')) return;
    closeTrashDropdowns();
}, true);

document.addEventListener('click', function(e) {
    if (e.target.closest && e.target.closest('.message-actions-menu')) {
        closeTrashDropdowns();
        return;
    }
    if (e.target.closest && e.target.closest('.message-actions-trigger')) return;
    closeTrashDropdowns();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeTrashDropdowns();
});
</script>
@endsection

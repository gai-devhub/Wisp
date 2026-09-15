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
                                        <div class="message-actions-menu">

                                            {{-- Restore --}}
                                            <form action="{{ route('user.lifecycle.restore', $msg->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="message-action-item" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f8fafc; color: #E8674A; display: flex; align-items: center; justify-content: center;"><i class="fas fa-undo"></i></div>
                                                        <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Restore</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                </button>
                                            </form>

                                            <div style="height: 1px; background: #f1f5f9; margin: 4px 0;"></div>

                                            {{-- Delete Permanently --}}
                                            <form action="{{ route('user.lifecycle.force_delete', $msg->id) }}" method="POST" class="m-0"
                                                  onsubmit="return confirmFormSubmit(event, this, 'WARNING: This will permanently delete this message and cannot be undone!', 'confirm');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="message-action-item message-action-delete" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center;"><i class="fas fa-trash"></i></div>
                                                        <span style="font-weight: 500; color: #ef4444; font-size: 0.95rem;">Delete Permanently</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #fca5a5; font-size: 0.8em;"></i>
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




<script>
function closeTrashDropdowns(exceptDropdown) {
    document.querySelectorAll('#trash .message-actions-dropdown.is-open').forEach(function(openDropdown) {
        if (exceptDropdown && openDropdown === exceptDropdown) return;
        openDropdown.classList.remove('is-open');
        var openTrigger = openDropdown.querySelector('.message-actions-trigger');
        if (openTrigger) openTrigger.setAttribute('aria-expanded', 'false');
        var openMenu = openDropdown.querySelector('.message-actions-menu');
        if (openMenu) openMenu.style.display = 'none';
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
                menu.style.top = (rect.bottom + 8) + 'px';
                menu.style.left = 'auto';
                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.zIndex = '9999';
                menu.style.margin = '0';
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

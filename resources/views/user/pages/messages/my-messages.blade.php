@extends('user.base-user')

@section('user-section', 'my-messages')

@section('content')
    <style>
        .msg-title-desk { display: inline; }
        .msg-title-mob { display: none; }
        @media (max-width: 768px) {
            .msg-title-desk { display: none !important; }
            .msg-title-mob { display: inline !important; }
            .hide-on-mobile { display: none !important; }
            
            /* Compact dropdown menu on mobile */
            .message-action-item { padding: 8px 12px !important; }
            .message-action-item > div { gap: 8px !important; }
            .message-action-item > div > div { width: 26px !important; height: 26px !important; }
            .message-action-item > div > div i { font-size: 0.8rem !important; }
            .message-action-item > div > span { font-size: 0.85rem !important; }
        }
    </style>
    <div class="content-section active" id="my-messages">

        <div class="my-messages-page-toolbar messages-page-toolbar">
            <h2 class="messages-page-title">
                <i class="fas fa-envelopes-bulk text-primary messages-page-icon"></i>
                My Messages
            </h2>
            {{-- Desktop: full text button --}}
            <a href="{{ route('user.create.page') }}" class="my-messages-create-btn">
                <i class="fas fa-plus"></i> Create New Message
            </a>
            {{-- Mobile only: circular icon FAB --}}
            <a href="{{ route('user.create.page') }}" class="my-messages-create-fab" title="Create New Message">
                <i class="fas fa-plus"></i>
            </a>
        </div>
        <div class="card messages-card-container">
            <div class="card-body overflow-auto my-messages-body messages-card-body">
            @if(isset($messages) && $messages->isEmpty())
                <div class="empty-table-cell" style="padding: 60px 20px;">
                    <div class="empty-table-icon-wrap">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="empty-table-title" style="font-size: 0.95rem;">No Messages Found</h4>
                    <p class="empty-table-desc" style="font-size: 0.85rem;">Create your first message to begin sharing.</p>
                </div>
            @else
                <div class="table-responsive-wrap">
                    <table class="messages-table" id="messagesTable">
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Recipient</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="hide-on-mobile">Created</th>
                                <th scope="col" class="hide-on-mobile">Views</th>
                                <th scope="col" class="hide-on-mobile">Date viewed</th>
                                <th scope="col" class="hide-on-mobile">Msg-status</th>
                                <th scope="col" class="hide-on-mobile">Link</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($messages ?? []) as $msg)
                                @php /** @var \App\Models\WishMessages $msg */ @endphp
                                <tr>
                                    <td data-label="Title" class="blur-sensitive">
                                        <div class="msg-title-desk">{{ $msg->title }}</div>
                                        <div class="msg-title-mob">{{ \Illuminate\Support\Str::limit($msg->title, 10, '...') }}</div>
                                    </td>
                                    <td data-label="Recipient" class="blur-sensitive">{{ $msg->recipient_name }}</td>
                                    <td data-label="Status"><span
                                            class="status-badge status-{{ $msg->is_published ? 'saved' : 'draft' }}">{{ $msg->is_published ? 'Saved' : 'Draft' }}</span>
                                    </td>
                                    <td data-label="Created" class="hide-on-mobile">{{ $msg->created_at->format('M j, Y') }}</td>
                                    <td data-label="Views" class="hide-on-mobile">{{ $msg->view_count ?? 0 }}</td>
                                    <td data-label="Date viewed" class="hide-on-mobile">
                                        {{ $msg->views->max('viewed_at') ? \Carbon\Carbon::parse($msg->views->max('viewed_at'))->format('M j, Y') : '-' }}
                                    </td>
                                    <td data-label="Msg-status" class="hide-on-mobile">{{ str_replace('_', ' ', $msg->status) }}</td>
                                    <td data-label="Link" class="link-cell hide-on-mobile">@if($msg->generated_link)<a
                                        href="{{ $msg->generated_link }}" target="_blank"
                                    rel="noopener">{{ Str::limit($msg->generated_link, 25) }}</a>@else<span
                                            class="text-muted">-</span>@endif</td>
                                    <td data-label="Actions" class="actions-cell">
                                        @php
                                            $msgTpl = ($messageTemplates ?? collect())->get($msg->id);
                                            $msgTplName = $msgTpl ? $msgTpl->template_name : 'view-1';
                                            $msgTplIndex = is_array($templateList ?? null) ? array_search($msgTplName, $templateList) : 0;
                                            if ($msgTplIndex === false)
                                                $msgTplIndex = 0;
                                        @endphp
                                        <div class="message-actions-dropdown">
                                            <button type="button" class="message-actions-trigger" aria-expanded="false">
                                                <span class="d-none d-md-inline">Take Action</span>
                                                <i class="fas fa-chevron-down d-none d-md-inline"></i>
                                                <i class="fas fa-ellipsis-v d-md-none"></i>
                                            </button>
                                            <div class="message-actions-menu">
                                                <button type="button" class="message-action-item open-preview-modal"
                                                    data-message-id="{{ $msg->id }}" data-template-index="{{ $msgTplIndex }}"
                                                    data-template-name="{{ $msgTplName }}" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; color: #4f46e5; display: flex; align-items: center; justify-content: center;"><i class="fas fa-eye"></i></div>
                                                        <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Preview</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                </button>
                                                <a href="{{ route('user.edit-messages.page', $msg->id) }}"
                                                    class="nav-link message-action-item js-bypass" data-section="edit"
                                                    data-message-id="{{ $msg->id }}" style="display: flex; justify-content: space-between; align-items: center; width: 100%; text-decoration: none;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center;"><i class="fas fa-pen"></i></div>
                                                        <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Edit</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                </a>
                                                <a href="{{ route('user.template.page') }}?message_id={{ $msg->id }}"
                                                    class="message-action-item" style="display: flex; justify-content: space-between; align-items: center; width: 100%; text-decoration: none;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #ffe4e6; color: #e11d48; display: flex; align-items: center; justify-content: center;"><i class="fas fa-columns"></i></div>
                                                        <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Template</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                </a>
                                                <a href="{{ route('user.media.page') }}?message_id={{ $msg->id }}"
                                                    class="message-action-item" style="display: flex; justify-content: space-between; align-items: center; width: 100%; text-decoration: none;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center;"><i class="fas fa-image"></i></div>
                                                        <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Add Media</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                </a>
                                                @php
                                                    $hasActiveLink = isset($msg->generatedLinks) 
                                                        ? $msg->generatedLinks->where('is_active', true)->isNotEmpty() 
                                                        : $msg->generatedLinks()->where('is_active', true)->exists();
                                                @endphp
                                                @if(!$hasActiveLink)
                                                <form method="POST" action="{{ route('links.generate') }}" class="m-0" style="width: 100%;">
                                                    @csrf
                                                    <input type="hidden" name="wish_message_id" value="{{ $msg->id }}">
                                                    <button type="submit" class="message-action-item" style="display: flex; justify-content: space-between; align-items: center; width: 100%; border: none; background: transparent; text-align: left;">
                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #ffedd5; color: #ea580c; display: flex; align-items: center; justify-content: center;"><i class="fas fa-link"></i></div>
                                                            <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Generate Link</span>
                                                        </div>
                                                        <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                <a href="{{ route('user.share-messages.page') }}?message_id={{ $msg->id }}"
                                                    class="message-action-item share-message-trigger" data-message-id="{{ $msg->id }}"
                                                    data-title="{{ $msg->title }}" data-recipient="{{ $msg->recipient_name }}"
                                                    data-phone="{{ $msg->recipient_phone }}" data-link="{{ $msg->generated_link }}"
                                                    style="display: flex; justify-content: space-between; align-items: center; width: 100%; text-decoration: none;">
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;"><i class="fas fa-paper-plane"></i></div>
                                                        <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">Share Message</span>
                                                    </div>
                                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('user.lifecycle.toggle_vault', $msg->id) }}"
                                                    class="m-0">
                                                    @csrf
                                                    <button type="submit" class="message-action-item" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #fef3c7; color: #f59e0b; display: flex; align-items: center; justify-content: center;"><i class="fas fa-lock"></i></div>
                                                            <span style="font-weight: 500; color: #1e293b; font-size: 0.95rem;">{{ $msg->is_vaulted ? 'Unvault' : 'Vault' }}</span>
                                                        </div>
                                                        <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 0.8em;"></i>
                                                    </button>
                                                </form>
                                                <div style="height: 1px; background: #f1f5f9; margin: 4px 0;"></div>
                                                <form method="POST" action="{{ route('messages.destroy', $msg->id) }}"
                                                    class="delete-message-form m-0"
                                                    onsubmit="return confirmFormSubmit(event, this, 'Delete this message? It will be moved to the Trash.', 'confirm');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="message-action-item message-action-delete" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                        <div style="display: flex; align-items: center; gap: 12px;">
                                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center;"><i class="fas fa-trash"></i></div>
                                                            <span style="font-weight: 500; color: #ef4444; font-size: 0.95rem;">Delete</span>
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
                @if(isset($messages) && $messages->hasPages())
                    <div class="custom-pagination">
                        <div class="pagination-btns">
                            <button type="button" class="pagination-btn" aria-label="Previous" @if($messages->onFirstPage())
                            disabled @endif
                                onclick="@if(!$messages->onFirstPage()) location.href='{{ $messages->previousPageUrl() }}'; @endif"><i
                                    class="fas fa-chevron-left"></i> Previous</button>
                            <button type="button" class="pagination-btn" aria-label="Next" @if(!$messages->hasMorePages())
                            disabled @endif
                                onclick="@if($messages->hasMorePages()) location.href='{{ $messages->nextPageUrl() }}'; @endif">Next
                                <i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
</div>

@push('scripts')
<script>
// My Messages: Edit – go to Create section and fill form with that message's data
function closeMessageDropdowns(exceptDropdown) {
    document.querySelectorAll('.message-actions-dropdown.is-open').forEach(function (openDropdown) {
        if (exceptDropdown && openDropdown === exceptDropdown) return;
        openDropdown.classList.remove('is-open');
        var openTrigger = openDropdown.querySelector('.message-actions-trigger');
        if (openTrigger) openTrigger.setAttribute('aria-expanded', 'false');
        var openMenu = openDropdown.querySelector('.message-actions-menu');
        if (openMenu) openMenu.style.display = 'none';
    });
}

document.querySelectorAll('.message-actions-trigger').forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var dropdown = trigger.closest('.message-actions-dropdown');
        if (!dropdown) return;
        var isOpen = dropdown.classList.contains('is-open');
        closeMessageDropdowns(dropdown);
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

window.addEventListener('scroll', function (e) {
    // Close dropdowns if scrolling happens outside the menu itself
    if (e.target.closest && e.target.closest('.message-actions-menu')) return;
    closeMessageDropdowns();
}, true);

document.addEventListener('click', function (e) {
    // If clicking a menu item inside the dropdown, close the dropdown first
    if (e.target.closest && e.target.closest('.message-actions-menu')) {
        closeMessageDropdowns();
        return;
    }
    // If clicking the trigger, let the trigger handler manage it
    if (e.target.closest && e.target.closest('.message-actions-trigger')) return;
    // Click outside everything — close all
    closeMessageDropdowns();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeMessageDropdowns();
});

// (btn-edit legacy code removed)

</script>
@endpush
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    //
});
</script>
@endpush

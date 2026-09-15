@extends('user.base-user')

@section('user-section', 'links')

@section('content')
<div class="content-section active" id="links">
    @if(session('generated_link'))
        <div class="alert alert-success links-alert">
            <strong>Link generated:</strong>
            <div class="generated-link links-alert-link">
                <input type="text" class="form-control" id="newly-generated-link" value="{{ session('generated_link') }}" readonly>
                <button type="button" class="btn btn-primary copy-link-btn" data-url="{{ session('generated_link') }}">Copy link</button>
            </div>
        </div>
    @endif
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-link text-primary messages-header-icon"></i> 
            Generate Links
        </h2>
    </div>
    <div class="col">
        <div>
            @forelse(($generatedLinks ?? []) as $link)
                @php
                    $msg = $link->wishMessage;
                    $isActive = (bool) $link->is_active;
                    $viewCount = $msg ? \App\Models\MessageViews::where('wish_message_id', $msg->id)->select('ip_address')->groupBy('ip_address')->get()->count() : ($link->view_count ?? 0);
                @endphp
                <div class="generated-link-card" id="link-card-{{ $link->id }}" onclick="toggleCardCollapse({{ $link->id }}, event)" style="background: #ffffff; border-radius: 20px; border: 1px solid rgba(226, 232, 240, 0.8); padding: 22px 24px; margin-bottom: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02); transition: all 0.2s ease; cursor: pointer;">
                    <!-- Top Header Row -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px;">
                        <div style="display: flex; align-items: center; gap: 16px; min-width: 0;">
                            <div style="min-width: 0;">
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3;">
                                    {{ $msg ? $msg->title : 'Message' }}
                                </h4>
                                <div style="font-size: 0.9rem; color: #64748b; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $msg ? ($msg->recipient_special_name ?? $msg->recipient_name) : '' }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                            <span class="status-badge-{{ $link->id }}" style="display: {{ $isActive ? 'none' : 'inline-block' }}; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
                                Disabled
                            </span>
                            <button type="button" class="btn-toggle-status" onclick="event.stopPropagation(); toggleLinkStatus({{ $link->id }}, this);" data-id="{{ $link->id }}" data-active="{{ $isActive ? '1' : '0' }}" title="{{ $isActive ? 'Disable link' : 'Enable link' }}" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; color: #334155; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;">
                                <i class="fas {{ $isActive ? 'fa-eye-slash' : 'fa-eye' }}" id="eye-icon-{{ $link->id }}" style="font-size: 1rem;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible Details Body (Closed by default) -->
                    <div id="link-details-{{ $link->id }}" class="link-details-body" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                        <!-- Views Count -->
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #64748b; margin-bottom: 14px; font-weight: 500;">
                            <i class="far fa-eye" style="font-size: 1rem; color: #64748b;"></i>
                            <span style="font-weight: 600; color: #475569;">{{ $viewCount }} views</span>
                        </div>

                        <!-- Copy Link Input Box -->
                        <div style="display: flex; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);" onclick="event.stopPropagation();">
                            <input type="text" class="form-control" value="{{ $link->generated_url }}" readonly style="border: none; background: transparent; padding: 12px 16px; font-size: 0.9rem; color: #334155; flex: 1; outline: none; font-family: inherit;" onclick="event.stopPropagation();">
                            <button type="button" class="btn copy-link-btn copy-btn-{{ $link->id }}" data-url="{{ $link->generated_url }}" onclick="event.stopPropagation();" style="border: none; background: {{ $isActive ? '#E8674A' : '#64748b' }}; color: #ffffff; padding: 0 24px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: background 0.2s ease; display: flex; align-items: center; justify-content: center;">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body">
                        <div class="empty-table-cell" style="padding: 60px 20px;">
                            <div class="empty-table-icon-wrap">
                                <i class="fas fa-link"></i>
                            </div>
                            <h4 class="empty-table-title" style="font-size: 0.95rem;">No Generated Links</h4>
                            <p class="empty-table-desc" style="font-size: 0.85rem;">Select a saved message above and click Generate Link.</p>
                        </div>
                    </div>
                </div>
            @endforelse

            @if(isset($generatedLinks) && $generatedLinks->hasPages())
                <div class="custom-pagination" style="margin-top: 20px;">
                    <div class="pagination-btns">
                        <button type="button" class="pagination-btn" aria-label="Previous" @if($generatedLinks->onFirstPage()) disabled @endif onclick="@if(!$generatedLinks->onFirstPage()) location.href='{{ route('user.links.page', ['links_page' => $generatedLinks->currentPage() - 1]) }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                        <button type="button" class="pagination-btn" aria-label="Next" @if(!$generatedLinks->hasMorePages()) disabled @endif onclick="@if($generatedLinks->hasMorePages()) location.href='{{ route('user.links.page', ['links_page' => $generatedLinks->currentPage() + 1]) }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleCardCollapse(linkId, event) {
        if (event && event.target && (event.target.tagName === 'INPUT' || event.target.tagName === 'BUTTON' || event.target.closest('button'))) {
            return;
        }
        var body = document.getElementById('link-details-' + linkId);
        if (!body) return;

        if (body.style.display === 'none' || body.style.display === '') {
            body.style.display = 'block';
        } else {
            body.style.display = 'none';
        }
    }

    function toggleLinkStatus(linkId, btnElement) {
        var icon = document.getElementById('eye-icon-' + linkId);
        var badge = document.querySelector('.status-badge-' + linkId);
        var copyBtn = document.querySelector('.copy-btn-' + linkId);

        btnElement.disabled = true;
        fetch('/links/' + linkId + '/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            btnElement.disabled = false;
            if (data.success) {
                var newActive = data.is_active;
                btnElement.setAttribute('data-active', newActive ? '1' : '0');
                if (newActive) {
                    if (icon) icon.className = 'fas fa-eye-slash';
                    btnElement.title = 'Disable link';
                    if (badge) badge.style.display = 'none';
                    if (copyBtn) copyBtn.style.background = '#E8674A';
                } else {
                    if (icon) icon.className = 'fas fa-eye';
                    btnElement.title = 'Enable link';
                    if (badge) badge.style.display = 'inline-block';
                    if (copyBtn) copyBtn.style.background = '#64748b';
                }
            } else {
                alert(data.message || 'Error updating link status.');
            }
        })
        .catch(function(err) {
            btnElement.disabled = false;
            console.error(err);
        });
    }
</script>
@endsection

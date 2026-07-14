@extends('user.base-user')

@section('user-section', 'template')

@section('content')
@php
    $selectedMsgId = request('message_id');
    $selectedMessage = null;
    if ($selectedMsgId) {
        $selectedMessage = collect($allMessagesForSelect ?? [])->firstWhere('id', $selectedMsgId);
    }
@endphp

<div class="content-section active" id="template">
    <!-- Header Section -->
    <div class="messages-header-toolbar" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: flex-start; gap: 0.5px;">
            <i class="fas fa-palette text-primary messages-header-icon" style="font-size: 1.4rem; margin-top: 4px;"></i>
            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: flex-start;">
                <h2 class="messages-header-title" style="margin: 0 0 2px 0; padding: 0; display: block;">Template Gallery</h2>
                @if($selectedMessage)
                    <div style="font-size: 0.85rem; color: #64748b; margin: 0; padding: 0;">
                        Selecting for: <strong style="font-weight: 800; color: #334155;">{{ $selectedMessage->title }}</strong>, {{ $selectedMessage->recipient_name }}!
                    </div>
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <!-- {{--
            @if(!($isPremium ?? false))
                <a href="{{ route('user.billing.index') }}" class="template-upgrade-pill">
                    <i class="fas fa-crown"></i> Upgrade for 200+ Templates
                </a>
            @endif
            --}} -->
        </div>
    </div>

    <!-- {{--
    @if(!($isPremium ?? false))
    <div class="template-free-notice">
        <i class="fas fa-info-circle"></i>
        <span>You're on the <strong>Free plan</strong>. You have access to <strong>View</strong>, <strong>Aurora</strong> &amp; <strong>Casual</strong> templates.
        <a href="{{ route('user.billing.index') }}">Upgrade to Premium</a> to unlock all 200+ templates.</span>
    </div>
    <style>
    .template-free-notice {
        background: linear-gradient(90deg,#fdf4ff,#f0f4ff);
        border: 1px solid #f5d0fe; padding: 12px 18px; border-radius: 12px;
        margin-bottom: 24px; display: flex; align-items: center; gap: 12px; color: #701a75; font-size: 0.95rem;
    }
    .template-free-notice i { font-size: 1.1rem; color: #d946ef; }
    .template-free-notice a { color: #d946ef; font-weight: 600; text-decoration: none; margin-left: 4px; }
    .template-free-notice a:hover { text-decoration: underline; }
    </style>
    @endif
    --}} -->
    <style>
    .template-upgrade-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(90deg,#c084fc,#818cf8);
        color: #fff; border-radius: 999px; padding: 6px 16px;
        font-size: 13px; font-weight: 700; text-decoration: none;
        box-shadow: 0 2px 12px rgba(192,132,252,.3); transition: opacity 0.2s;
    }
    .template-upgrade-pill:hover { opacity: 0.9; color: #fff; }
    .template-card-locked {
        position: relative;
        opacity: 0.9;
    }
    .template-card-locked .template-lock-overlay {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        align-items: center; justify-content: center; background: rgba(15,10,40,0.45);
        border-radius: 12px; z-index: 5; color: #fff;
        font-size: 11px; font-weight: 700; gap: 4px;
        pointer-events: all;
        text-decoration: none;
    }
    .template-card-locked .template-lock-overlay i { font-size: 20px; color: #c084fc; }
    .template-card-locked .template-lock-overlay span { color: #e9d5ff; }
    </style>

    <div class="card template-gallery-card">
        <div class="card-body">
            @if(!$selectedMessage)
            <div class="template-message-select-wrap">
                <label for="template-page-message-id" class="template-select-label">Select Message</label>
                <select class="form-control template-message-select" id="template-page-message-id" name="template_page_message_id">
                    <option value="">-- Select a message --</option>
                    @foreach(($allMessagesForSelect ?? []) as $msg)
                        <option value="{{ $msg->id }}">{{ $msg->title }} - {{ $msg->recipient_name }}</option>
                    @endforeach
                </select>
                <p class="template-select-hint" id="template-select-hint">Select a message above, then choose a template below.</p>
            </div>
            @else
                <input type="hidden" id="template-page-message-id" value="{{ $selectedMessage->id }}">
            @endif
            
            <div class="template-grid-wrapper" id="template-grid-wrapper" {!! $selectedMessage ? '' : 'style="display: none;"' !!}>

            @php
                $premiumUser = $isPremium ?? false;
                // Tabs available to free users
                $freeTabs = ['view', 'aurora', 'casual'];
            @endphp

            <div class="template-tabs" role="tablist" style="display: flex; flex-wrap: nowrap; overflow-x: auto; white-space: nowrap; padding-bottom: 8px; gap: 8px; scrollbar-width: none;">
                <button type="button" class="template-tab active" data-filter="all" role="tab" aria-selected="true">All</button>
                @foreach($themes as $theme => $count)
                    <button type="button" class="template-tab" data-filter="{{ $theme }}">{{ ucfirst($theme) }}</button>
                @endforeach
                <!-- @if($premiumUser)
                    <button type="button" class="template-tab" data-filter="confetti" role="tab" aria-selected="false">Confetti</button>
                    <button type="button" class="template-tab" data-filter="minimal" role="tab" aria-selected="false">Minimal</button>
                    <button type="button" class="template-tab" data-filter="garden" role="tab" aria-selected="false">Garden</button>
                    <button type="button" class="template-tab" data-filter="glitter" role="tab" aria-selected="false">Glitter</button>
                    <button type="button" class="template-tab" data-filter="romance" role="tab" aria-selected="false">Romance</button>
                @else
                    {{-- Show locked tabs for premium themes --}}
                    @foreach(['confetti','minimal','garden','glitter','romance'] as $lockedTab)
                        <button type="button" class="template-tab template-tab-locked" data-filter="{{ $lockedTab }}" role="tab" aria-selected="false">
                            {{ ucfirst($lockedTab) }} <i class="fas fa-lock" style="font-size:10px;margin-left:4px;"></i>
                        </button>
                    @endforeach
                @endif -->
            </div>

            <div class="template-grid" id="template-grid">
                @php
                // Free users can see these two themes
                $freeThemes = ['view', 'aurora', 'casual']; 
                $premiumUser = true; // $isPremium ?? false; // Make all templates available
                $counter = 0;
                $globalTemplateIndex = 0;
                @endphp

                @foreach($themes as $theme => $count)
                    @php $isPremiumTheme = !in_array($theme, $freeThemes); @endphp
                    @for($n = 1; $n <= $count; $n++)
                        @php $counter++; $templateIndex = $globalTemplateIndex; $globalTemplateIndex++; @endphp
                        <div class="template-card-container" style="display: flex; flex-direction: column; height: 100%;">
                            <div class="template-card {{ (!$premiumUser && $isPremiumTheme) ? 'template-card-locked' : '' }}"
                                 data-theme="{{ $theme }}"
                                 data-template-num="{{ $n }}"
                                 data-template-index="{{ $templateIndex }}"
                                 role="{{ (!$premiumUser && $isPremiumTheme) ? 'presentation' : 'button' }}"
                                 tabindex="{{ (!$premiumUser && $isPremiumTheme) ? '-1' : '0' }}">
                                @if(!$premiumUser && $isPremiumTheme)
                                    <div class="template-card-placeholder" style="background: #f8fafc; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                                        <i class="fas fa-lock" style="font-size: 28px; margin-bottom: 12px; color: #cbd5e1;"></i>
                                        <span style="font-size: 13px; font-weight: 600; text-align: center; padding: 0 10px;">Requires<br>Premium Access</span>
                                    </div>
                                    <a href="{{ route('user.billing.index') }}" class="template-lock-overlay" title="Upgrade to unlock" style="background: rgba(15,10,40,0.1);">
                                    </a>
                                @else
                                    <div class="template-card-placeholder">
                                        <div class="template-radio-btn" style="position: absolute; top: 10px; right: 10px; width: 26px; height: 26px; border: 2px solid #cbd5e1; border-radius: 50%; z-index: 10; background: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; transition: all 0.2s;"></div>
                                        <iframe class="template-iframe" src="{{ route('templates.gallery.preview', $theme . '-' . $n) }}" scrolling="no" tabindex="-1"></iframe>
                                    </div>
                                @endif
                                <span class="template-card-label">#{{ str_pad($counter, 3, '0', STR_PAD_LEFT) }}</span>
                                <span class="template-card-title">{{ ucfirst($theme) }} - {{ $n }}</span>
                            </div>
                        </div>
                    @endfor
                @endforeach
            </div>
            </div>
        </div>
    </div>
    
    <div id="header-save-template-btn-wrapper" style="margin-top: 20px; display: none; justify-content: flex-end;">
        <button type="button" class="btn btn-primary" id="header-save-template-btn" style="padding: 12px 32px; font-weight: 700; border-radius: 8px; font-size: 16px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); width: max-content;">
            Save Selected Template
        </button>
    </div>
</div>

<style>
.template-tab-locked {
    opacity: 0.5;
    cursor: default;
    text-decoration: none;
    color: inherit;
}
.template-tab-locked:hover { opacity: 0.65; }
</style>
@push('scripts')
<script>
// Template page: enable/disable template grid based on message selection
var templateMessageSelect = document.getElementById('template-page-message-id');
var templateGridWrapper = document.getElementById('template-grid-wrapper');
function updateTemplateGridState() {
    if (!templateGridWrapper || !templateMessageSelect) return;
    if (templateMessageSelect.value) {
        templateGridWrapper.classList.remove('disabled-until-message');
    } else {
        templateGridWrapper.classList.add('disabled-until-message');
    }
}
if (templateMessageSelect) {
    templateMessageSelect.addEventListener('change', updateTemplateGridState);
}
updateTemplateGridState();

// Template tab: filter gallery by theme
var templateTabs = document.querySelectorAll('.template-tab');
var templateCards = document.querySelectorAll('.template-card');
templateTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
        var filter = tab.getAttribute('data-filter');
        templateTabs.forEach(function (t) { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');
        templateCards.forEach(function (card) {
            var theme = card.getAttribute('data-theme');
            if (filter === 'all' || theme === filter) {
                card.classList.remove('hidden-by-filter');
            } else {
                card.classList.add('hidden-by-filter');
            }
        });
    });
});

// Template card click behavior
templateCards.forEach(function (card) {
    card.addEventListener('click', function (e) {
        var messageSelect = document.getElementById('template-page-message-id');
        var messageId = (messageSelect && messageSelect.value) ? messageSelect.value : (card.getAttribute('data-message-id') || '1');
        
        if (!messageId) {
            alert('Please select your message first from the dropdown above.');
            return;
        }
        
        var templateIndex = parseInt(card.getAttribute('data-template-index'), 10) || 0;
        currentPreviewMessageId = String(messageId);
        currentTemplateIndex = templateIndex;
        
        // Check if we have a selected message (either via hidden input or dropdown)
        var isMessageSelected = messageSelect && messageSelect.value !== '';
        
        // Check if the user clicked the radio button
        var isRadioClick = e.target.closest('.template-radio-btn');
        
        if (isMessageSelected && isRadioClick) {
            // Selection Mode (Radio button clicked)
            templateCards.forEach(function (c) {
                c.classList.remove('selected-card');
                c.style.borderColor = '';
                c.style.boxShadow = '';
                var rb = c.querySelector('.template-radio-btn');
                if (rb) {
                    rb.innerHTML = '';
                    rb.style.borderColor = '#cbd5e1';
                    rb.style.background = 'rgba(255,255,255,0.5)';
                }
            });
            
            card.classList.add('selected-card');
            card.style.borderColor = '#3b82f6';
            card.style.boxShadow = '0 4px 14px rgba(59, 130, 246, 0.2)';
            
            var rb = card.querySelector('.template-radio-btn');
            if (rb) {
                rb.innerHTML = '<i class="fas fa-check-circle" style="color: #3b82f6; font-size: 28px; background: #fff; border-radius: 50%; line-height: 1;"></i>';
                rb.style.borderColor = 'transparent';
                rb.style.background = 'transparent';
            }
            
            var headerSave = document.getElementById('header-save-template-btn');
            var headerSaveWrapper = document.getElementById('header-save-template-btn-wrapper');
            if (headerSave) {
                if (headerSaveWrapper) headerSaveWrapper.style.display = 'flex';
                headerSave.style.display = 'inline-block';
                headerSave.setAttribute('data-template-index', templateIndex);
            }
            
            return; // Do NOT open preview modal when just checking the radio button
        }
        
        // Preview Mode
        loadIframes(messageId);
        var previewPopup = document.getElementById('preview-popup');
        if(previewPopup) {
            previewPopup.classList.add('active');
            document.body.style.overflow = 'hidden';
            goToTemplate(templateIndex);
            setPreviewSingleMessageMode(false);
            updatePreviewSaveButton();
        }
    });
});
</script>
@endpush
@endsection

@extends('user.base-user')

@section('user-section', 'help-support')

@section('content')
<div class="content-section active" id="help-support">

    <style>
        .feedback-wrapper {
            max-width: 720px;
            margin: 0 auto;
            padding: 0 0 40px 0;
        }
        .feedback-header-card {
            background: linear-gradient(135deg, var(--primary, #6c63ff) 0%, var(--secondary, #4f46e5) 100%);
            border-radius: 16px;
            padding: 36px 32px 28px 32px;
            color: #fff;
            margin-bottom: 28px;
            box-shadow: 0 8px 32px rgba(108, 99, 255, 0.2);
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        .feedback-header-icon {
            font-size: 2.6rem;
            opacity: 0.9;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .feedback-header-text h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0 0 6px 0;
            color: #fff;
        }
        .feedback-header-text p {
            font-size: 0.97rem;
            opacity: 0.88;
            margin: 0;
            line-height: 1.5;
        }
        .feedback-form-card {
            background: var(--card-bg, #fff);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .feedback-form-card .card-body {
            padding: 32px;
        }
        .feedback-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted, #9ca3af);
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color, #f0f0f0);
        }
        .feedback-field {
            margin-bottom: 22px;
        }
        .feedback-field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text, #111827);
            margin-bottom: 7px;
        }
        .feedback-field label .required-star {
            color: #ef4444;
            margin-left: 3px;
        }
        .feedback-field label .optional-badge {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--text-muted, #9ca3af);
            background: var(--bg-subtle, #f9fafb);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 4px;
            padding: 1px 6px;
            margin-left: 8px;
        }
        .feedback-field input[type="text"],
        .feedback-field input[type="email"],
        .feedback-field textarea,
        .feedback-field select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border-color, #d1d5db);
            border-radius: 10px;
            background: var(--bg, #fff);
            color: var(--text, #111827);
            font-size: 0.92rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }
        .feedback-field input[type="text"]:focus,
        .feedback-field input[type="email"]:focus,
        .feedback-field textarea:focus,
        .feedback-field select:focus {
            border-color: var(--primary, #6c63ff);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
        }
        .feedback-field textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }
        /* Rating row */
        .radio-group, .rating-row {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }
        .rating-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .rating-option input[type="radio"] {
            accent-color: var(--primary, #6c63ff);
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .rating-option span {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted, #9ca3af);
        }
        /* Yes/No radios */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            cursor: pointer;
            padding: 10px 14px;
            border: 1.5px solid var(--border-color, #e5e7eb);
            border-radius: 10px;
            transition: border-color 0.2s, background 0.2s;
            margin-bottom: 0;
        }
        .radio-group label:has(input:checked) {
            border-color: var(--primary, #6c63ff);
            background: rgba(108,99,255,0.04);
        }
        .radio-group label input[type="radio"] {
            accent-color: var(--primary, #6c63ff);
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        /* File upload area */
        .file-upload-zone {
            border: 2px dashed var(--border-color, #d1d5db);
            border-radius: 12px;
            padding: 20px;
            background: var(--bg-subtle, #f9fafb);
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }
        .file-upload-zone:hover,
        .file-upload-zone.dragover {
            border-color: var(--primary, #6c63ff);
            background: rgba(108,99,255,0.04);
        }
        .file-upload-zone input[type="file"] {
            display: none;
        }
        .file-upload-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-align: center;
        }
        .file-upload-inner i {
            font-size: 1.8rem;
            color: var(--text-muted, #9ca3af);
        }
        .file-upload-inner .upload-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text, #374151);
        }
        .file-upload-inner .upload-limit {
            font-size: 0.78rem;
            color: var(--text-muted, #9ca3af);
        }
        .file-upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: var(--card-bg, #fff);
            border: 1.5px solid var(--border-color, #d1d5db);
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text, #374151);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            margin-top: 4px;
        }
        .file-upload-btn:hover {
            border-color: var(--primary, #6c63ff);
            color: var(--primary, #6c63ff);
        }
        .file-preview-list {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .file-preview-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: var(--card-bg, #fff);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .file-preview-item i {
            color: var(--primary, #6c63ff);
        }
        .file-preview-item .file-name {
            flex: 1;
            font-weight: 500;
            color: var(--text, #374151);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .file-preview-item .file-size {
            color: var(--text-muted, #9ca3af);
            font-size: 0.78rem;
            flex-shrink: 0;
        }
        .file-remove-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #ef4444;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.8rem;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        .file-remove-btn:hover { opacity: 1; }
        .upload-error {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 6px;
            display: none;
        }
        /* Divider */
        .feedback-divider {
            border: none;
            border-top: 1px solid var(--border-color, #f0f0f0);
            margin: 24px 0;
        }
        /* Info notice */
        .upload-info-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(59,130,246,0.07);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.82rem;
            color: var(--text, #374151);
            margin-bottom: 22px;
        }
        .upload-info-notice i { color: #3b82f6; margin-top: 1px; flex-shrink: 0; }
        /* Submit button */
        .feedback-submit-btn {
            width: 100%;
            padding: 13px 24px;
            background: linear-gradient(135deg, var(--primary, #6c63ff), var(--secondary, #4f46e5));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            box-shadow: 0 4px 16px rgba(108,99,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .feedback-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(108,99,255,0.4);
            opacity: 0.95;
        }
        .feedback-submit-btn:active { transform: translateY(0); }
        .feedback-note {
            font-size: 0.82rem;
            color: var(--text-muted, #9ca3af);
            text-align: center;
            margin-top: 14px;
        }
        .feedback-note a {
            color: var(--primary, #6c63ff);
            text-decoration: none;
            font-weight: 500;
        }
        .feedback-note a:hover { text-decoration: underline; }
        @media (max-width: 600px) {
            .feedback-header-card { flex-direction: column; gap: 12px; padding: 24px 20px; }
            .feedback-form-card .card-body { padding: 20px; }
        }
    </style>

    <div class="feedback-wrapper">

        {{-- Header Banner --}}
        <div class="feedback-header-card">
            <div class="feedback-header-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="feedback-header-text">
                <h1>Feedback</h1>
                <p>We'd love to hear from you! Share your ideas, report issues, or tell us what you'd like to see improved in WISP. Every response helps us build something better.</p>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="feedback-form-card">
            <div class="card-body">

                <form id="wispFeedbackForm" onsubmit="handleFeedbackSubmit(event)">

                    <p class="feedback-section-title">Your Information</p>

                    <div class="feedback-field">
                        <label for="fb_name">Username <span class="required-star">*</span></label>
                        <input type="text" id="fb_name" name="fb_name" placeholder="Enter your username" required value="{{ auth()->user()->username ?? auth()->user()->name ?? '' }}">
                    </div>

                    <div class="feedback-field">
                        <label for="fb_email">Email Address <span class="optional-badge">optional</span></label>
                        <input type="email" id="fb_email" name="fb_email" placeholder="your@email.com" value="{{ auth()->user()->email ?? '' }}">
                    </div>

                    <hr class="feedback-divider">
                    <p class="feedback-section-title">Your Feedback</p>

                    <div class="feedback-field">
                        <label for="fb_idea">What is your idea about WISP? <span class="required-star">*</span></label>
                        <textarea id="fb_idea" name="fb_idea" placeholder="Share your thoughts, ideas or feedback about WISP..." required></textarea>
                    </div>

                    <div class="feedback-field">
                        <label for="fb_change">What would you change to improve WISP? <span class="required-star">*</span></label>
                        <textarea id="fb_change" name="fb_change" placeholder="Describe any changes or improvements you'd like to see..."></textarea>
                    </div>

                    <div class="feedback-field">
                        <label>Overall, how would you rate WISP? <span class="required-star">*</span></label>
                        <div class="rating-row">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="rating-option">
                                <span>{{ $i }}</span>
                                <input type="radio" name="fb_rating" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} required>
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="feedback-field">
                        <label>Would you add or remove something from WISP to update it? <span class="required-star">*</span></label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="fb_addremove" value="Yes" required> Yes
                            </label>
                            <label>
                                <input type="radio" name="fb_addremove" value="No"> No
                            </label>
                        </div>
                    </div>

                    <div class="feedback-field">
                        <label for="fb_specify">Please specify any changes you would make to improve the app <span class="required-star">*</span></label>
                        <textarea id="fb_specify" name="fb_specify" placeholder="Be as detailed as you like..."></textarea>
                    </div>

                    <div class="feedback-field">
                        <label for="fb_comments">Any comments or suggestions to improve the app <span class="required-star">*</span></label>
                        <textarea id="fb_comments" name="fb_comments" placeholder="Any final comments or suggestions..."></textarea>
                    </div>

                    <hr class="feedback-divider">
                    <p class="feedback-section-title">Media Attachments</p>

                    <div class="upload-info-notice">
                        <i class="fas fa-info-circle"></i>
                        <span>Files are <strong>not saved</strong> on WISP's servers. After submitting, the Google Form will open in a new tab — please attach your selected files there. <strong>Images</strong> max 10 MB · <strong>Videos</strong> max 100 MB.</span>
                    </div>

                    {{-- Image Upload --}}
                    <div class="feedback-field">
                        <label>Images of any problem encountered <span class="optional-badge">optional</span></label>
                        <div class="file-upload-zone" id="imageDropZone" onclick="document.getElementById('fb_images').click()">
                            <input type="file" id="fb_images" name="fb_images[]" accept="image/*" multiple onchange="handleImageSelect(this)">
                            <div class="file-upload-inner">
                                <i class="fas fa-image"></i>
                                <span class="upload-title">Click or drag images here</span>
                                <span class="upload-limit">PNG, JPG, GIF, WebP — max 10 MB each</span>
                                <span class="file-upload-btn"><i class="fas fa-upload"></i> Add file</span>
                            </div>
                        </div>
                        <div class="upload-error" id="imageError">⚠ One or more files exceed the 10 MB limit and were removed.</div>
                        <div class="file-preview-list" id="imagePreview"></div>
                    </div>

                    {{-- Video Upload --}}
                    <div class="feedback-field">
                        <label>Videos of any problem encountered <span class="optional-badge">optional</span></label>
                        <div class="file-upload-zone" id="videoDropZone" onclick="document.getElementById('fb_videos').click()">
                            <input type="file" id="fb_videos" name="fb_videos[]" accept="video/*" multiple onchange="handleVideoSelect(this)">
                            <div class="file-upload-inner">
                                <i class="fas fa-video"></i>
                                <span class="upload-title">Click or drag videos here</span>
                                <span class="upload-limit">MP4, MOV, AVI, WebM — max 100 MB each</span>
                                <span class="file-upload-btn"><i class="fas fa-upload"></i> Add file</span>
                            </div>
                        </div>
                        <div class="upload-error" id="videoError">⚠ One or more files exceed the 100 MB limit and were removed.</div>
                        <div class="file-preview-list" id="videoPreview"></div>
                    </div>

                    <hr class="feedback-divider">

                    <button type="submit" class="feedback-submit-btn" id="feedbackSubmitBtn">
                        <i class="fas fa-paper-plane"></i>
                        Submit Feedback
                    </button>

                    <p class="feedback-note">
                        Your response is submitted via Google Forms — nothing is stored on WISP.<br>
                        Prefer to fill the form directly? <a href="https://forms.gle/JFFBuqQ7qb9suX8p6" target="_blank">Open Google Form <i class="fas fa-external-link-alt" style="font-size: 0.75em;"></i></a>
                    </p>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
const IMAGE_MAX = 10 * 1024 * 1024;   // 10 MB
const VIDEO_MAX = 100 * 1024 * 1024;  // 100 MB

let selectedImages = [];
let selectedVideos = [];

function formatSize(bytes) {
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function renderPreviews(list, containerId, type) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    list.forEach((file, idx) => {
        const icon = type === 'image' ? 'fa-file-image' : 'fa-file-video';
        const item = document.createElement('div');
        item.className = 'file-preview-item';
        item.innerHTML = `
            <i class="fas ${icon}"></i>
            <span class="file-name">${file.name}</span>
            <span class="file-size">${formatSize(file.size)}</span>
            <button type="button" class="file-remove-btn" onclick="removeFile('${type}', ${idx})" title="Remove">
                <i class="fas fa-times"></i>
            </button>`;
        container.appendChild(item);
    });
}

function removeFile(type, idx) {
    if (type === 'image') {
        selectedImages.splice(idx, 1);
        renderPreviews(selectedImages, 'imagePreview', 'image');
    } else {
        selectedVideos.splice(idx, 1);
        renderPreviews(selectedVideos, 'videoPreview', 'video');
    }
}

function handleImageSelect(input) {
    const errEl = document.getElementById('imageError');
    errEl.style.display = 'none';
    let hasError = false;
    Array.from(input.files).forEach(f => {
        if (f.size > IMAGE_MAX) { hasError = true; return; }
        if (!selectedImages.find(x => x.name === f.name && x.size === f.size)) {
            selectedImages.push(f);
        }
    });
    if (hasError) errEl.style.display = 'block';
    renderPreviews(selectedImages, 'imagePreview', 'image');
    input.value = '';
}

function handleVideoSelect(input) {
    const errEl = document.getElementById('videoError');
    errEl.style.display = 'none';
    let hasError = false;
    Array.from(input.files).forEach(f => {
        if (f.size > VIDEO_MAX) { hasError = true; return; }
        if (!selectedVideos.find(x => x.name === f.name && x.size === f.size)) {
            selectedVideos.push(f);
        }
    });
    if (hasError) errEl.style.display = 'block';
    renderPreviews(selectedVideos, 'videoPreview', 'video');
    input.value = '';
}

// Drag-and-drop support
['imageDropZone', 'videoDropZone'].forEach(id => {
    const zone = document.getElementById(id);
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('dragover');
        const isImage = id === 'imageDropZone';
        const fakeInput = { files: e.dataTransfer.files, value: '' };
        if (isImage) handleImageSelect(fakeInput);
        else handleVideoSelect(fakeInput);
    });
});

function handleFeedbackSubmit(e) {
    e.preventDefault();
    // Open Google Form in new tab — user attaches files there
    window.open('https://forms.gle/JFFBuqQ7qb9suX8p6', '_blank');

    const btn = document.getElementById('feedbackSubmitBtn');
    btn.innerHTML = '<i class="fas fa-check-circle"></i> Opening Google Form...';
    btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
    btn.disabled = true;
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Feedback';
        btn.style.background = '';
        btn.disabled = false;
    }, 4000);
}
</script>
@endsection

@extends('user.base-user')

@section('user-section', 'help-support')

@section('content')
<div class="content-section active" id="help-support">
    <style>
        .hs-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }

        .hs-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.2s ease;
        }

        .hs-back-btn:hover {
            color: var(--primary);
        }

        .feedback-wrapper {
            max-width: 100%;
        }

        .feedback-header-card {
            background: linear-gradient(135deg, #ffffff 0%, #fffdfa 50%, #faf5ee 100%) !important;
            border: 1px solid #f1e5d5 !important;
            border-radius: 20px !important;
            padding: 32px !important;
            color: #1e293b !important;
            margin-bottom: 28px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .feedback-header-icon {
            font-size: 2rem !important;
            color: #E8674A !important;
            width: 58px !important;
            height: 58px !important;
            border-radius: 16px !important;
            background: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .feedback-header-text h1 {
            font-size: 1.6rem !important;
            font-weight: 800 !important;
            margin: 0 0 6px 0 !important;
            color: #1e293b !important;
        }

        .feedback-header-text p {
            font-size: 0.95rem !important;
            color: #64748b !important;
            margin: 0 !important;
            line-height: 1.5 !important;
        }

        .feedback-form-card {
            background: #ffffff !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04) !important;
            overflow: hidden;
            margin-bottom: 24px;
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
            border-bottom: 1px solid var(--border, #f0f0f0);
        }

        .feedback-field {
            margin-bottom: 22px;
        }

        .feedback-field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
        }

        .feedback-field label .required-star {
            color: #ef4444;
            margin-left: 3px;
        }

        .feedback-field label .optional-badge {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--text-muted);
            background: var(--bg-subtle);
            border: 1px solid var(--border);
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
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            color: var(--text);
            font-size: 0.92rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .feedback-field input[type="text"]:focus,
        .feedback-field input[type="email"]:focus,
        .feedback-field textarea:focus,
        .feedback-field select:focus {
            border-color: #E8674A;
            box-shadow: 0 0 0 3px rgba(232, 103, 74, 0.12);
        }

        .feedback-field textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

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
            accent-color: #E8674A;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .rating-option span {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

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
            border: 1.5px solid var(--border);
            border-radius: 10px;
            transition: border-color 0.2s, background 0.2s;
            width: 100%;
        }

        .radio-group label:hover {
            border-color: #E8674A;
            background: #fff9f7;
        }

        .radio-group input[type="radio"] {
            accent-color: #E8674A;
            width: 18px;
            height: 18px;
        }

        .file-upload-zone {
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            background: var(--bg-subtle);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .file-upload-zone:hover, .file-upload-zone.dragover {
            border-color: #E8674A;
            background: #fff9f7;
        }

        .file-upload-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .file-upload-inner i {
            font-size: 2rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .upload-title {
            font-weight: 600;
            color: var(--text);
        }

        .upload-limit {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .file-upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
            box-shadow: var(--shadow);
            pointer-events: none;
        }

        .upload-info-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(232, 103, 74, 0.07);
            border-left: 4px solid #E8674A;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 22px;
            font-size: 0.88rem;
            color: var(--text);
            line-height: 1.5;
        }

        .upload-info-notice i {
            color: #E8674A;
            margin-top: 2px;
            font-size: 1.1rem;
        }

        .file-preview-list {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .file-preview-item {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }

        .file-preview-item i {
            color: #E8674A;
            font-size: 1.1rem;
        }

        .file-name {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-grow: 1;
        }

        .file-size {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .file-remove-btn {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 4px;
        }

        .file-remove-btn:hover {
            background: #fee2e2;
        }

        .upload-error {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 6px;
            display: none;
        }

        .feedback-divider {
            border: 0;
            border-top: 1px solid var(--border);
            margin: 30px 0;
        }

        .feedback-submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 50%;
            margin: 0 auto;
            padding: 14px 28px !important;
            background: linear-gradient(135deg, #E8674A, #F28C76) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 12px !important;
            font-size: 0.98rem !important;
            font-weight: 700 !important;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232, 103, 74, 0.35) !important;
            transition: all 0.2s ease !important;
        }

        .feedback-submit-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(232, 103, 74, 0.45) !important;
        }

        .feedback-submit-btn:active {
            transform: translateY(0);
        }

        .feedback-note {
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 16px;
            line-height: 1.5;
        }

        .feedback-note a {
            color: #E8674A;
            text-decoration: none;
            font-weight: 500;
        }

        .feedback-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .feedback-header-card {
                flex-direction: column;
                gap: 12px;
                padding: 24px 20px;
            }

            .feedback-form-card .card-body {
                padding: 20px;
            }
        }
    </style>

    <div class="hs-container">
        <a href="{{ route('user.help-support.page') }}" class="hs-back-btn">
            <i class="fas fa-arrow-left"></i> Back to Help Center
        </a>

        <div class="feedback-wrapper">
            <div class="feedback-header-card">
                <div class="feedback-header-icon">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="feedback-header-text">
                    <h1>Support &amp; Wisp Growth</h1>
                    <p>Share your ideas, suggest updates, and submit feedback directly to Wisp. Every response helps us build something better.</p>
                </div>
            </div>

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

                        <div class="feedback-field">
                            <label>Images of any problem encountered <span class="optional-badge">optional</span></label>
                            <div class="file-upload-zone" id="imageDropZone" onclick="document.getElementById('fb_images').click()">
                                <input type="file" id="fb_images" name="fb_images[]" accept="image/*" multiple onchange="handleImageSelect(this)" style="display: none;">
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

                        <div class="feedback-field">
                            <label>Videos of any problem encountered <span class="optional-badge">optional</span></label>
                            <div class="file-upload-zone" id="videoDropZone" onclick="document.getElementById('fb_videos').click()">
                                <input type="file" id="fb_videos" name="fb_videos[]" accept="video/*" multiple onchange="handleVideoSelect(this)" style="display: none;">
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
                            Prefer to fill the form directly? <a href="https://docs.google.com/forms/d/e/1FAIpQLSdieicn8HJt1KEgB6kuO7PeInpebNCCHiuFjuBSRzjvc1lBGg/viewform" target="_blank">Open Google Form <i class="fas fa-external-link-alt" style="font-size: 0.75em;"></i></a>
                        </p>
                    </form>
                </div>
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

    const btn = document.getElementById('feedbackSubmitBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    btn.disabled = true;

    // Grab form inputs
    const fullName = document.getElementById('fb_name').value;
    const idea = document.getElementById('fb_idea').value;
    const change = document.getElementById('fb_change').value;
    const rating = document.querySelector('input[name="fb_rating"]:checked')?.value || '5';
    const addremove = document.querySelector('input[name="fb_addremove"]:checked')?.value || 'No';
    const specify = document.getElementById('fb_specify').value;
    const comments = document.getElementById('fb_comments').value;

    // Construct form data using mapped entry IDs
    const formData = new URLSearchParams();
    formData.append('entry.403254705', fullName);
    formData.append('entry.1763506228', idea);
    formData.append('entry.1060550840', change);
    formData.append('entry.2048793754', rating);
    formData.append('entry.1656055856', addremove);
    formData.append('entry.1061421347', specify);
    formData.append('entry.1907099581', comments);

    const googleFormUrl = 'https://docs.google.com/forms/u/0/d/e/1FAIpQLSdieicn8HJt1KEgB6kuO7PeInpebNCCHiuFjuBSRzjvc1lBGg/formResponse';

    // Submit silently using no-cors mode (which Google Forms expects for cross-domain requests)
    fetch(googleFormUrl, {
        method: 'POST',
        mode: 'no-cors',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
    .then(() => {
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Feedback Submitted!';
        btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        
        document.getElementById('wispFeedbackForm').reset();
        selectedImages = [];
        selectedVideos = [];
        if (document.getElementById('imagePreview')) document.getElementById('imagePreview').innerHTML = '';
        if (document.getElementById('videoPreview')) document.getElementById('videoPreview').innerHTML = '';

        if (selectedImages.length > 0 || selectedVideos.length > 0) {
            alert('Your text responses have been submitted! Opening Google Form in a new tab so you can attach your images/videos.');
            window.open('https://docs.google.com/forms/d/e/1FAIpQLSdieicn8HJt1KEgB6kuO7PeInpebNCCHiuFjuBSRzjvc1lBGg/viewform', '_blank');
        }

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.disabled = false;
        }, 4000);
    })
    .catch(err => {
        console.error('Error submitting feedback:', err);
        btn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Submission Failed';
        btn.style.background = '#ef4444';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.disabled = false;
        }, 4000);
    });
}
</script>
@endsection

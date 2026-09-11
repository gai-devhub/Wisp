<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#E8674A">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">   
    <title>Try WISP — Send a message</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Base styles -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
    <style>
        .notification-toast { 
            display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: white; border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-left: 4px solid #10b981; min-width: 300px;
            transform: translateX(120%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .notification-toast.show { transform: translateX(0); }
        .notification-toast.success { border-left-color: #10b981; }
        .notification-toast.error { border-left-color: #ef4444; }
        .notification-toast.warning { border-left-color: #f59e0b; }
        .notification-toast.info { border-left-color: #3b82f6; }
        .notification-toast-icon { font-size: 1.25rem; }
        .notification-toast.success .notification-toast-icon { color: #10b981; }
        .notification-toast.error .notification-toast-icon { color: #ef4444; }
        .notification-toast.warning .notification-toast-icon { color: #f59e0b; }
        .notification-toast.info .notification-toast-icon { color: #3b82f6; }
        .notification-toast-content { flex: 1; }
        .notification-toast-content h4 { margin: 0 0 4px 0; font-size: 0.9rem; font-weight: 600; color: #1e293b; }
        .notification-toast-content p { margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4; }
        .notification-toast-close { background: none; border: none; font-size: 1rem; color: #94a3b8; cursor: pointer; padding: 4px; }
        .notification-toast-close:hover { color: #ef4444; }
        html, body { font-family: var(--font-body); }
        h1, h2, h3, h4 { font-family: var(--font-display); }
    </style>
</head>
<body>
    <div id="notificationContainer" class="try-notification-container"></div>

    <!-- Ambient background -->
    <div class="ambient-grid">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
        <div class="blob blob-5"></div>
    </div>

    @include('welcome.components.header')

    <main class="main try-main">
        <section class="try-section">
            
            <div style="text-align: center; margin-bottom: 40px;">
                <span class="badge"><i class="fas fa-magic"></i> Try WISP Free</span>
                <h1 class="try-hero-title">Create a magical message</h1>
                <p class="try-hero-subtitle">Experience the magic of WISP before signing up. You can create up to 2 free messages right here.</p>
                
                @if($messageCount < 2)
                    <div style="margin-top: 12px; font-size: 0.9rem; color: var(--coral);">
                        You have used {{ $messageCount }} of your 2 trial messages.
                    </div>
                @endif
            </div>

            @if(session('error'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success') && !session('generated_link'))
                <div class="glass-card mb-6" style="background: rgba(232, 103, 74, 0.1); border: 1px solid rgba(232, 103, 74, 0.2); color: var(--coral); padding: 15px 20px; border-radius: 12px; text-align: center;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($messageCount >= 2)
                <div class="glass-card w-full max-w-2xl mx-auto" style="padding: 40px; text-align: center; max-width: 672px; margin: 0 auto;">
                    <i class="fas fa-lock" style="font-size: 3rem; color: var(--coral); margin-bottom: 20px;"></i>
                    <h2 style="font-size: 1.8rem; margin-bottom: 12px;">You've reached the limit</h2>
                    <p style="color: var(--text-muted); margin-bottom: 24px;">We hope you enjoyed trying WISP! To create unlimited messages, schedule sends, and edit them later, please create a free account.</p>
                    <a href="{{ route('auth.login') }}" class="btn-generate-main" style="text-decoration: none;">Create Free Account</a>
                </div>
            @else
                <form action="{{ route('guest.try.create') }}" method="POST" enctype="multipart/form-data" class="w-full" id="guestMessageForm">
                    @csrf
                    <input type="hidden" name="message_id" id="current_message_id" value="">
                    
                    <div class="try-form-grid-2col">
                        
                        <!-- Column 1 -->
                        <div class="try-column">
                            
                            <!-- Message Basics Card -->
                            <div class="glass-card accordion-card" id="messageAccordionCard">
                                 <!-- Clickable Header -->
                                 <div class="accordion-header" onclick="toggleMessageAccordion()">
                                     <div class="accordion-header-left">
                                         <div class="accordion-icon-box accordion-icon-coral">
                                             <i class="fas fa-envelope-open-text"></i>
                                         </div>
                                         <div>
                                             <h3 class="accordion-title">Create Message</h3>
                                             <p class="accordion-subtitle" id="messageBasicsSubtitle">The core details of your magical message.</p>
                                         </div>
                                     </div>
                                     <div class="flex items-center gap-4">
                                         <i class="fas fa-chevron-down accordion-chevron" id="messageAccordionIcon"></i>
                                     </div>
                                 </div>

                                 <!-- Hidden Content -->
                                 <div id="messageAccordionContent" class="hidden accordion-content">
                                     <div class="accordion-body">
                                         <div class="wisp-form-fields-grid">
                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">MESSAGE TYPE</label>
                                                 <input type="text" name="message_type" class="wisp-form-input" placeholder="e.g. Birthday Message" required>
                                             </div>
                                             
                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">MESSAGE TITLE</label>
                                                 <input type="text" name="title" class="wisp-form-input" placeholder="Enter preferred view title" required>
                                             </div>

                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">RECIPIENT'S FULL NAME</label>
                                                 <input type="text" name="recipient_name" class="wisp-form-input" placeholder="Enter recipient's full name" required>
                                             </div>
                                             
                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">RECIPIENT'S SPECIAL NAME</label>
                                                 <input type="text" name="recipient_special_name" class="wisp-form-input" placeholder="Enter recipient's special name">
                                             </div>
                                             
                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">GREETING</label>
                                                 <input type="text" name="greeting" class="wisp-form-input" placeholder="Hello there" required>
                                             </div>

                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">YOUR NAME (SENDER)</label>
                                                 <input type="text" name="sender_name" class="wisp-form-input" placeholder="Gilbert Asare" required>
                                             </div>

                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">LAST NOTES</label>
                                                 <input type="text" name="last_note" class="wisp-form-input" placeholder="eg. Happy birthday...">
                                             </div>
                                             
                                             <div class="wisp-form-group">
                                                 <label class="wisp-form-label">RECEIVING DATE</label>
                                                 <input type="date" name="receiving_date" class="wisp-form-input">
                                             </div>

                                             <div class="wisp-form-group col-span-2">
                                                 <label class="wisp-form-label">MESSAGE CONTENT</label>
                                                 <textarea name="message_body" class="wisp-form-input" rows="3" placeholder="Write your heartfelt message here..." required></textarea>
                                             </div>
                                         </div>

                                         <div class="flex justify-end" style="border-top: 1px solid #f1f5f9; padding-top: 24px;">
                                             <button type="submit" id="innerSaveBtn" class="btn-wisp-save">
                                                 <i class="fas fa-save"></i> <span id="innerSaveText">Save Message</span>
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                            
                        </div>

                        <!-- Column 2 -->
                        <div class="try-column">

                            <!-- Appearance & Media Card -->
                             <div class="glass-card accordion-card" id="mediaAccordionCard">
                                 <!-- Clickable Header -->
                                 <div class="accordion-header" onclick="toggleMediaAccordion()">
                                     <div class="accordion-header-left">
                                         <div class="accordion-icon-box accordion-icon-rose">
                                             <i class="fas fa-image"></i>
                                         </div>
                                         <div>
                                             <h3 class="accordion-title">Add Media Files</h3>
                                             <p class="accordion-subtitle" id="mediaSubtitle">Personalize how your message looks.</p>
                                         </div>
                                     </div>
                                     <div class="flex items-center gap-4">
                                         <i class="fas fa-chevron-down accordion-chevron" id="mediaAccordionIcon"></i>
                                     </div>
                                 </div>

                                 <!-- Hidden Content -->
                                 <div id="mediaAccordionContent" class="hidden accordion-content">
                                     <div class="accordion-body">
                                         <div class="wisp-form-group">
                                             <label class="wisp-form-label">ADD A PHOTO (OPTIONAL)</label>
                                             <div class="wisp-file-upload-box">
                                                 <span class="wisp-file-upload-text"><i class="fas fa-cloud-upload-alt" style="margin-right: 8px;"></i> Choose an image</span>
                                                 <input type="file" name="recipient_image" id="recipient_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                                             </div>
                                             <div class="wisp-file-info" id="recipient-image-info">No file selected</div>
                                         </div>

                                         <div class="wisp-form-group" style="margin-top: 16px;">
                                             <div class="flex items-center justify-between" style="margin-bottom: 8px;">
                                                 <label class="wisp-form-label" style="margin: 0;">UPLOAD MUSIC FILE</label>
                                                 <button type="button" onclick="toggleSpotifySearch()" class="btn-spotify-icon" title="Search on Spotify">
                                                     <i class="fab fa-spotify" style="font-size: 1.1rem;"></i>
                                                 </button>
                                             </div>

                                             <input type="hidden" name="spotify_url" id="spotify_url">
                                             <input type="hidden" name="spotify_name" id="spotify_name">

                                             <!-- Spotify Search Dropdown (Hidden by default) -->
                                             <div id="spotify-search-container" class="hidden spotify-search-container">
                                                 <div class="relative" style="margin-bottom: 12px;">
                                                     <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                                                     <input type="text" id="spotify-search-input" class="spotify-search-input" placeholder="Search for tracks on Spotify...">
                                                 </div>
                                                 <div id="spotify-search-results" class="spotify-search-results">
                                                     <p style="font-size: 0.75rem; color: #64748b; text-align: center; padding: 12px 0;">Type to search for tracks</p>
                                                 </div>
                                             </div>

                                             <div class="wisp-file-upload-box">
                                                 <span class="wisp-file-upload-text"><i class="fas fa-cloud-upload-alt" style="margin-right: 8px;"></i> Choose an audio file</span>
                                                 <input type="file" name="background_music" id="background_music" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="audio/*">
                                             </div>
                                             <div class="wisp-file-info flex items-center justify-between" id="music-file-info">
                                                 <span>No file selected</span>
                                             </div>
                                         </div>
                                         <div class="flex justify-end" style="border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: 16px;">
                                             <button type="submit" id="mediaSaveBtn" class="btn-wisp-save">
                                                 <i class="fas fa-save"></i> <span id="mediaSaveText">Save Media</span>
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                            <!-- Templates Accordion Card -->
                            <div class="glass-card accordion-card" id="templatesAccordionCard">
                                <!-- Clickable Header -->
                                <div class="accordion-header" onclick="toggleTemplatesAccordion()">
                                    <div class="accordion-header-left">
                                        <div class="accordion-icon-box accordion-icon-emerald">
                                            <i class="fas fa-th-large"></i>
                                        </div>
                                        <div>
                                             <h3 class="accordion-title">Template & Appearance</h3>
                                             <p class="accordion-subtitle" id="templateSubtitle">Select an option</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div id="accordionTemplatePreview" class="hidden items-center gap-2">
                                            <div id="accordionThumbnail" style="width: 24px; height: 24px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 1px 2px rgba(0,0,0,0.05);"></div>
                                            <span id="accordionTemplateName" class="accordion-subtitle"></span>
                                        </div>
                                        <i class="fas fa-chevron-down accordion-chevron" id="templatesAccordionIcon"></i>
                                    </div>
                                </div>
                                
                                <!-- Hidden Content (Grid) -->
                                <div id="templatesAccordionContent" class="hidden accordion-content">
                                    <div class="accordion-body">
                                        <input type="hidden" name="template_name" id="selectedTemplateInput" value="view-1">
                                        
                                        <!-- Tabs -->
                                        <div class="template-tabs" style="display: flex; flex-wrap: nowrap; overflow-x: auto; justify-content: flex-start; white-space: nowrap; padding-bottom: 8px; gap: 8px;">
                                            <button type="button" class="template-tab active" data-filter="all">All</button>
                                            @foreach($themes as $theme => $count)
                                                <button type="button" class="template-tab" data-filter="{{ $theme }}">{{ ucfirst($theme) }}</button>
                                            @endforeach
                                        </div>

                                        <!-- Grid -->
                                        <div class="template-grid-accordion" id="accordionTemplateGrid">
                                            <!-- Cards generated by JS -->
                                        </div>
                                        <div class="flex justify-end" style="border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                                            <button type="submit" id="templateSaveBtn" class="btn-wisp-save">
                                                <i class="fas fa-save"></i> <span id="templateSaveText">Save Template</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Generate Card -->
                            <div class="glass-card try-generate-card">
                                <h3 class="accordion-title" style="font-size: 1.25rem; margin-bottom: 8px;">Ready to Share?</h3>
                                <p class="try-hero-subtitle" style="font-size: 0.95rem; margin-bottom: 24px;">Generate your unique magic link and share it instantly.</p>
                                <button type="submit" id="mainSubmitBtn" class="btn-generate-main">
                                    <span id="mainBtnLoader" class="hidden btn-spinner animate-spin"></span>
                                    <i class="fas fa-magic" id="mainSubmitIcon" style="margin-right: 8px;"></i> <span id="mainSubmitText">Generate Magic Link</span>
                                </button>
                            </div>

                        </div>
                    </div>

                </form>
            @endif

            @if(isset($messages) && $messages->count() > 0)
                <div class="recent-messages-wrapper">
                    <h3 class="accordion-title" style="font-size: 1.25rem; margin-bottom: 24px;">Your Recent Magic Messages</h3>
                    <div class="wisp-table-card">
                        <div class="w-full">
                            <table class="wisp-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Recipient</th>
                                        <th class="hide-mobile">Type</th>
                                        <th class="hide-mobile">Created</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($messages as $msg)
                                        @php
                                            $link = $msg->generatedLinks()->where('is_active', true)->first();
                                        @endphp
                                        <tr>
                                            <td style="font-weight: 500;">{{ $msg->title ?? 'Untitled' }}</td>
                                            <td style="color: var(--text-muted);">{{ $msg->recipient_name ?? 'Unknown' }}</td>
                                            <td class="hide-mobile" style="color: var(--text-muted);">{{ ucfirst(str_replace('_', ' ', $msg->message_type)) }}</td>
                                            <td class="hide-mobile" style="color: var(--text-muted);">{{ $msg->created_at->diffForHumans() }}</td>
                                            <td style="text-align: right;">
                                                <div class="table-dropdown-container">
                                                    <button type="button" style="background: none; border: none; color: #9ca3af; padding: 8px; cursor: pointer;">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    
                                                    <!-- Dropdown Menu -->
                                                    <div class="table-dropdown-menu">
                                                        @if($link)
                                                            <a href="{{ $link->generated_url }}" target="_blank" class="table-dropdown-item">
                                                                <i class="fas fa-eye" style="width: 20px; text-align: center; margin-right: 8px;"></i> View
                                                            </a>
                                                        @else
                                                            <span class="table-dropdown-item" style="color: #9ca3af; font-style: italic;">
                                                                <i class="fas fa-eye-slash" style="width: 20px; text-align: center; margin-right: 8px;"></i> Not Generated
                                                            </span>
                                                        @endif
                                                        
                                                        <div style="height: 1px; background-color: #f1f5f9; margin: 4px 0;"></div>
                                                        
                                                        <form action="{{ route('guest.try.delete', $msg->id) }}" method="POST" style="margin: 0;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="table-dropdown-item danger">
                                                                <i class="fas fa-trash-alt" style="width: 20px; text-align: center; margin-right: 8px;"></i> Delete
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
                    </div>
                </div>
            @endif

        </section>
    </main>

    @include('welcome.components.footer')



    

    <script>
        const templates = [];
        const themes = @json($themes);
        
        let idCounter = 1;
        for (const [prefix, count] of Object.entries(themes)) {
            for(let i=1; i<=count; i++) {
                templates.push({
                    id: `${prefix}-${i}`,
                    name: `${prefix.charAt(0).toUpperCase() + prefix.slice(1)} - ${i}`,
                    tag: `#${String(idCounter).padStart(3, '0')}`
                });
                idCounter++;
            }
        }

        let currentSelectedId = 'view-1';
        let accordionOpen = false;

        function toggleTemplatesAccordion() {
            const content = document.getElementById('templatesAccordionContent');
            const icon = document.getElementById('templatesAccordionIcon');
            accordionOpen = !accordionOpen;
            
            if (accordionOpen) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
                renderAccordionGrid();
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        let messageAccordionOpen = false;
        function toggleMessageAccordion() {
            const content = document.getElementById('messageAccordionContent');
            const icon = document.getElementById('messageAccordionIcon');
            messageAccordionOpen = !messageAccordionOpen;
            
            if (messageAccordionOpen) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        let mediaAccordionOpen = false;
        function toggleMediaAccordion() {
            const content = document.getElementById('mediaAccordionContent');
            const icon = document.getElementById('mediaAccordionIcon');
            mediaAccordionOpen = !mediaAccordionOpen;
            
            if (mediaAccordionOpen) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function selectTemplate(id) {
            currentSelectedId = id;
            renderAccordionGrid();
            
            const t = templates.find(x => x.id === currentSelectedId);
            if (t) {
                document.getElementById('selectedTemplateInput').value = t.id;
                document.getElementById('accordionThumbnail').className = 'w-6 h-6 rounded border border-black/10 shadow-sm overflow-hidden relative bg-slate-50';
                document.getElementById('accordionThumbnail').innerHTML = `<iframe src="/templates/gallery-preview/${t.id}" scrolling="no" tabindex="-1" style="width: 500%; height: 500%; border: none; pointer-events: none; transform: scale(0.2); transform-origin: top left;"></iframe>`;
                document.getElementById('accordionTemplateName').innerText = t.name;
                document.getElementById('accordionTemplatePreview').classList.remove('hidden');
                document.getElementById('accordionTemplatePreview').classList.add('flex');
            }
        }

        let currentFilter = 'all';

        function renderAccordionGrid() {
            const grid = document.getElementById('accordionTemplateGrid');
            if(!grid) return;
            
            const filteredTemplates = currentFilter === 'all' 
                ? templates 
                : templates.filter(t => t.id.startsWith(currentFilter));
            
            grid.innerHTML = filteredTemplates.map(t => {
                const isSelected = currentSelectedId === t.id;
                
                return `
                <div class="template-card-container" style="display: flex; flex-direction: column; height: 100%;">
                    <div class="template-card" onclick="selectTemplate('${t.id}')" style="cursor: pointer; ${isSelected ? 'border-color: #3b82f6; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);' : ''}">
                        <div class="template-card-placeholder">
                            <div class="template-radio-btn" style="position: absolute; top: 10px; right: 10px; width: 26px; height: 26px; border: 2px solid ${isSelected ? 'transparent' : '#cbd5e1'}; border-radius: 50%; z-index: 10; background: ${isSelected ? 'transparent' : 'rgba(255,255,255,0.5)'}; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                ${isSelected ? '<i class="fas fa-check-circle" style="color: #3b82f6; font-size: 28px; background: #fff; border-radius: 50%; line-height: 1;"></i>' : ''}
                            </div>
                            <iframe class="template-iframe" src="/templates/preview/${t.id}" scrolling="no" tabindex="-1"></iframe>
                        </div>
                        <span class="template-card-label">${t.tag}</span>
                        <span class="template-card-title">${t.name}</span>
                    </div>
                </div>
                `;
            }).join('');
        }
        
        // Tab click handling
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.template-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    tabs.forEach(t => t.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                    currentFilter = e.currentTarget.getAttribute('data-filter');
                    renderAccordionGrid();
                });
            });
        });
        
        // Init preview
        setTimeout(() => {
            const t = templates.find(x => x.id === currentSelectedId);
            if(t) {
                document.getElementById('accordionThumbnail').className = 'w-6 h-6 rounded border border-black/10 shadow-sm overflow-hidden relative bg-slate-50';
                document.getElementById('accordionThumbnail').innerHTML = `<iframe src="/templates/preview/${t.id}" scrolling="no" tabindex="-1" style="width: 500%; height: 500%; border: none; pointer-events: none; transform: scale(0.2); transform-origin: top left;"></iframe>`;
                document.getElementById('accordionTemplateName').innerText = t.name;
                document.getElementById('accordionTemplatePreview').classList.remove('hidden');
                document.getElementById('accordionTemplatePreview').classList.add('flex');
            }
        }, 100);

        // Spotify Search Logic
        function toggleSpotifySearch() {
            const container = document.getElementById('spotify-search-container');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                document.getElementById('spotify-search-input').focus();
            } else {
                container.classList.add('hidden');
            }
        }

        let searchTimeout = null;
        document.getElementById('spotify-search-input').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            clearTimeout(searchTimeout);
            const resultsContainer = document.getElementById('spotify-search-results');
            if (!query) {
                resultsContainer.innerHTML = '<p class="text-xs text-gray-500 text-center py-4">Type to search for tracks</p>';
                return;
            }

            resultsContainer.innerHTML = '<p class="text-xs text-gray-500 text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Searching...</p>';

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`{{ route('try.spotify.search') }}?q=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    
                    if (data.error) throw new Error(data.error);

                    if (!data.tracks || data.tracks.length === 0) {
                        resultsContainer.innerHTML = '<p class="text-xs text-gray-500 text-center py-4">No tracks found.</p>';
                        return;
                    }

                    resultsContainer.innerHTML = data.tracks.map(track => {
                        const coverUrl = track.album && track.album.images && track.album.images.length > 0 ? track.album.images[0].url : 'https://via.placeholder.com/50';
                        const artistName = track.artists && track.artists.length > 0 ? track.artists[0].name : 'Unknown Artist';
                        const saveUrl = track.external_urls ? track.external_urls.spotify : '';
                        
                        return `
                        <div class="flex items-center gap-3 p-2 hover:bg-black/5 rounded-lg cursor-pointer transition-colors" onclick="selectSpotifyTrack('${saveUrl}', '${track.name.replace(/'/g, "\\'")}')">
                            <img src="${coverUrl}" class="w-10 h-10 rounded shadow-sm object-cover" alt="Cover">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-slate-800 truncate">${track.name}</h4>
                                <p class="text-xs text-slate-500 truncate">${artistName}</p>
                            </div>
                            <button type="button" class="w-8 h-8 rounded-full bg-white border border-gray-200 text-[#1DB954] flex items-center justify-center hover:bg-[#1DB954] hover:text-white hover:border-[#1DB954] transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        `;
                    }).join('');
                } catch (error) {
                    console.error(error);
                    resultsContainer.innerHTML = '<p class="text-xs text-red-500 text-center py-4">Search failed. Ensure Spotify is configured.</p>';
                }
            }, 500);
        });

        function selectSpotifyTrack(url, name) {
            document.getElementById('spotify_url').value = url;
            document.getElementById('spotify_name').value = name;
            
            // Clear file upload selection visually since we are using spotify
            const fileInput = document.getElementById('background_music');
            if(fileInput) fileInput.value = '';
            
            document.getElementById('music-file-info').innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fab fa-spotify text-[#1DB954] text-lg"></i>
                    <span class="text-slate-700 font-semibold truncate max-w-[200px]">${name}</span>
                </div>
                <button type="button" onclick="clearSpotifyTrack()" class="text-red-500 hover:text-red-600 p-1"><i class="fas fa-times"></i></button>
            `;
            
            // Close dropdown
            document.getElementById('spotify-search-container').classList.add('hidden');
        }

        function clearSpotifyTrack() {
            document.getElementById('spotify_url').value = '';
            document.getElementById('spotify_name').value = '';
            document.getElementById('music-file-info').innerHTML = '<span>No file selected</span>';
        }

        // File upload UI listeners
        document.getElementById('recipient_image').addEventListener('change', function(e) {
            const fileInfo = document.getElementById('recipient-image-info');
            if (this.files && this.files[0]) {
                fileInfo.innerHTML = `<span class="text-[#C7502F] font-semibold"><i class="fas fa-check-circle mr-1"></i> ${this.files[0].name}</span>`;
            } else {
                fileInfo.innerHTML = 'No file selected';
            }
        });

        document.getElementById('background_music').addEventListener('change', function(e) {
            const fileInfo = document.getElementById('music-file-info');
            if (this.files && this.files[0]) {
                // Clear spotify selection
                document.getElementById('spotify_url').value = '';
                document.getElementById('spotify_name').value = '';
                
                fileInfo.innerHTML = `
                    <span class="text-[#C7502F] font-semibold flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> 
                        <span class="truncate max-w-[200px]">${this.files[0].name}</span>
                    </span>
                    <button type="button" onclick="document.getElementById('background_music').value=''; document.getElementById('music-file-info').innerHTML='<span>No file selected</span>';" class="text-red-500 hover:text-red-600 p-1"><i class="fas fa-times"></i></button>
                `;
            } else {
                fileInfo.innerHTML = '<span>No file selected</span>';
            }
        });

        // AJAX Form Submission
        let generatedLink = '';
        let isShareMode = false;

        const guestMessageForm = document.getElementById('guestMessageForm');
        if (guestMessageForm) {
            guestMessageForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitter = e.submitter;
                const isMainButton = submitter && submitter.id === 'mainSubmitBtn';

                // If link is already generated, clicking the main button again should open share modal immediately (synchronously)
                if (isMainButton && isShareMode && generatedLink) {
                    if (navigator.share) {
                        try {
                            navigator.share({
                                title: document.getElementById('messageBasicsSubtitle')?.innerText || 'My Message',
                                text: 'I made a magical message for you!',
                                url: generatedLink
                            });
                        } catch (err) {
                            console.log('Share canceled or failed', err);
                        }
                    } else {
                        prompt("Copy your magic link:", generatedLink);
                    }
                    // Submit in background silently to save any latest changes
                    fetch(this.action, { method: 'POST', body: new FormData(this), headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                    return;
                }

            // Set loading states
            if (isMainButton) {
                document.getElementById('mainBtnLoader').classList.remove('hidden');
                document.getElementById('mainSubmitIcon').classList.add('hidden');
                submitter.disabled = true;
            } else if (submitter && submitter.id === 'innerSaveBtn') {
                document.getElementById('innerSaveText').innerText = 'Saving...';
                submitter.disabled = true;
            } else if (submitter && submitter.id === 'mediaSaveBtn') {
                document.getElementById('mediaSaveText').innerText = 'Saving...';
                submitter.disabled = true;
            } else if (submitter && submitter.id === 'templateSaveBtn') {
                document.getElementById('templateSaveText').innerText = 'Saving...';
                submitter.disabled = true;
            }

            const formData = new FormData(this);
            if (isMainButton) {
                formData.append('generate_link', 'true');
            }

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.status === 422) {
                    let errorMsg = Object.values(data.errors).map(e => e.join(' ')).join(' ');
                    showNotification('Error', errorMsg, 'error');
                } else if (data.success) {
                    // Update hidden id
                    document.getElementById('current_message_id').value = data.message_id;
                    
                    if (data.generated_link) {
                        generatedLink = data.generated_link;
                    }

                    // Update subtitles
                    const title = data.title;
                    if(document.getElementById('messageBasicsSubtitle')) document.getElementById('messageBasicsSubtitle').innerText = title;
                    if(document.getElementById('mediaSubtitle')) document.getElementById('mediaSubtitle').innerText = title;
                    if(document.getElementById('templateSubtitle')) document.getElementById('templateSubtitle').innerText = title;

                    showNotification('Success', 'Message updated successfully.', 'success');

                    // Transform main button only if they clicked it to generate the link
                    if (isMainButton && generatedLink) {
                        isShareMode = true;
                        document.getElementById('mainSubmitText').innerText = 'Share Message';
                        document.getElementById('mainSubmitIcon').className = 'fas fa-share-alt mr-2';
                    }

                    // Transform inner button
                    if (submitter && submitter.id === 'innerSaveBtn') {
                        document.getElementById('innerSaveText').innerText = 'Saved!';
                        setTimeout(() => {
                            document.getElementById('innerSaveText').innerText = 'Update Message';
                        }, 2000);
                    } else if (submitter && submitter.id === 'mediaSaveBtn') {
                        document.getElementById('mediaSaveText').innerText = 'Saved!';
                        setTimeout(() => {
                            document.getElementById('mediaSaveText').innerText = 'Save Media';
                        }, 2000);
                    } else if (submitter && submitter.id === 'templateSaveBtn') {
                        document.getElementById('templateSaveText').innerText = 'Saved!';
                        setTimeout(() => {
                            document.getElementById('templateSaveText').innerText = 'Save Template';
                        }, 2000);
                    }
                } else {
                    showNotification('Error', data.error || 'Something went wrong.', 'error');
                }
            } catch (error) {
                console.error('Error saving message:', error);
                showNotification('Error', 'An error occurred while saving your message.', 'error');
            } finally {
                // Reset loading states
                if (isMainButton) {
                    document.getElementById('mainBtnLoader').classList.add('hidden');
                    document.getElementById('mainSubmitIcon').classList.remove('hidden');
                    submitter.disabled = false;
                } else if (submitter && submitter.id === 'innerSaveBtn') {
                    if (document.getElementById('innerSaveText').innerText !== 'Saved!') {
                        document.getElementById('innerSaveText').innerText = 'Update Message';
                    }
                    submitter.disabled = false;
                } else if (submitter && submitter.id === 'mediaSaveBtn') {
                    if (document.getElementById('mediaSaveText').innerText !== 'Saved!') {
                        document.getElementById('mediaSaveText').innerText = 'Save Media';
                    }
                    submitter.disabled = false;
                } else if (submitter && submitter.id === 'templateSaveBtn') {
                    if (document.getElementById('templateSaveText').innerText !== 'Saved!') {
                        document.getElementById('templateSaveText').innerText = 'Save Template';
                    }
                    submitter.disabled = false;
                }
            }
        });
        }
        
        function showNotification(title, message, type = 'success', duration = 5000) {
            const container = document.getElementById('notificationContainer');
            if (!container) return;
            const notification = document.createElement('div');
            notification.className = `notification-toast ${type}`;

            const icon = type === 'success' ? 'fas fa-check-circle' :
                type === 'error' ? 'fas fa-exclamation-circle' :
                    type === 'warning' ? 'fas fa-exclamation-triangle' :
                        'fas fa-info-circle';

            notification.innerHTML = `
                <div class="notification-toast-icon">
                    <i class="${icon}"></i>
                </div>
                <div class="notification-toast-content">
                    <h4>${title}</h4>
                    <p>${message}</p>
                </div>
                <button class="notification-toast-close">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            const autoRemove = setTimeout(() => {
                closeNotification(notification);
            }, duration);

            const closeBtn = notification.querySelector('.notification-toast-close');
            closeBtn.addEventListener('click', () => {
                clearTimeout(autoRemove);
                closeNotification(notification);
            });
        }

        function closeNotification(notification) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }

        // Mobile Nav
        const navToggle = document.getElementById('navToggle');
        const navClose = document.getElementById('navClose');
        const mobileNav = document.getElementById('mobileNav');
        const navOverlay = document.getElementById('navOverlay');
        const openNav = () => {
            if(mobileNav) mobileNav.classList.add('active');
            if(navOverlay) navOverlay.classList.add('active');
            if(mobileNav) mobileNav.setAttribute('aria-hidden', 'false');
        };
        const closeNav = () => {
            if(mobileNav) mobileNav.classList.remove('active');
            if(navOverlay) navOverlay.classList.remove('active');
            if(mobileNav) mobileNav.setAttribute('aria-hidden', 'true');
        };
        if (navToggle) navToggle.addEventListener('click', openNav);
        if (navOverlay) navOverlay.addEventListener('click', closeNav);
        if (mobileNav) {
            mobileNav.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', closeNav);
            });
        }
    </script>
</body>

</html>
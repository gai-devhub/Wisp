<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">   
    <title>Try WISP — Send a message</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Base styles -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
    
</head>
<body>

    <!-- Ambient background -->
    <div class="ambient-grid">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
        <div class="blob blob-5"></div>
    </div>

    @include('welcome.components.header')

    <main class="main" style="padding-top: 8rem; padding-bottom: 6rem; min-height: 100vh;">
        <section class="container" style="max-width: 1400px; margin: 0 auto;">
            
            <div style="text-align: center; margin-bottom: 40px;">
                <span class="badge"><i class="fas fa-magic"></i> Try WISP Free</span>
                <h1 style="font-size: 3rem; font-weight: 700; margin-top: 16px; margin-bottom: 12px;">Create a magical message</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Experience the magic of WISP before signing up. You can create up to 2 free messages right here.</p>
                
                @if($messageCount < 2)
                    <div style="margin-top: 12px; font-size: 0.9rem; color: var(--accent);">
                        You have used {{ $messageCount }} of your 2 trial messages.
                    </div>
                @endif
            </div>

            @if(session('error'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="glass-card success-box">
                    <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 16px;"></i>
                    <h2 style="font-size: 1.5rem; color: white;">Your magic link is ready!</h2>
                    <p style="margin-top: 8px; color: var(--text-muted);">Copy the link below and share it with your recipient.</p>
                    
                    <div class="generated-link-box">
                        <input type="text" id="magicLink" value="{{ session('generated_link') }}" readonly>
                        <button onclick="copyLink()" class="btn btn-secondary" style="padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: white; cursor: pointer;">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    
                    <div style="margin-top: 24px;">
                        <a href="{{ route('guest.try') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">Create another message</a>
                        <span style="color: rgba(255,255,255,0.3); margin: 0 12px;">|</span>
                        <a href="{{ route('auth.login') }}" style="color: white; text-decoration: none; font-weight: 600;">Sign up for more</a>
                    </div>
                </div>
                
                <script>
                    function copyLink() {
                        var copyText = document.getElementById("magicLink");
                        copyText.select();
                        copyText.setSelectionRange(0, 99999);
                        navigator.clipboard.writeText(copyText.value);
                        alert("Copied the magic link!");
                    }
                </script>
            @elseif($messageCount >= 2)
                <div class="glass-card" style="padding: 40px; text-align: center;">
                    <i class="fas fa-lock" style="font-size: 3rem; color: var(--accent); margin-bottom: 20px;"></i>
                    <h2 style="font-size: 1.8rem; margin-bottom: 12px;">You've reached the limit</h2>
                    <p style="color: var(--text-muted); margin-bottom: 24px;">We hope you enjoyed trying WISP! To create unlimited messages, schedule sends, and edit them later, please create a free account.</p>
                    <a href="{{ route('auth.login') }}" class="bg-[var(--primary)] text-white py-4 px-8 w-full rounded-[44px] font-semibold text-[1.1rem] cursor-pointer transition-all duration-300 inline-flex items-center justify-center shadow-[0_4px_15px_rgba(99,102,241,0.2)] hover:-translate-y-[2px] hover:shadow-[0_10px_20px_rgba(99,102,241,0.4)]" style="display: inline-block; text-decoration: none;">Create Free Account</a>
                </div>
            @else
                <form action="{{ route('guest.try.create') }}" method="POST" enctype="multipart/form-data" class="w-full" id="guestMessageForm">
                    @csrf
                    <input type="hidden" name="message_id" id="current_message_id" value="">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start w-full">
                        
                        <!-- Column 1 -->
                        <div class="flex flex-col gap-6 w-full">
                            
                            <!-- Message Basics Card -->
                            <div class="glass-card w-full overflow-hidden transition-all duration-500" id="messageAccordionCard">
                                 <!-- Clickable Header -->
                                 <div class="p-8 cursor-pointer hover:bg-black/5 transition-colors flex items-center justify-between" onclick="toggleMessageAccordion()">
                                     <div class="flex items-center gap-4">
                                         <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 text-xl shrink-0">
                                             <i class="fas fa-envelope-open-text"></i>
                                         </div>
                                         <div>
                                             <h3 class="text-lg font-bold text-[var(--text-main)] mb-1">Message Basics</h3>
                                             <p class="text-sm text-[var(--text-muted)] m-0" id="messageBasicsSubtitle">The core details of your magical message.</p>
                                         </div>
                                     </div>
                                     <div class="flex items-center gap-4">
                                         <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="messageAccordionIcon"></i>
                                     </div>
                                 </div>

                                 <!-- Hidden Content -->
                                 <div id="messageAccordionContent" class="hidden border-t border-gray-100 bg-slate-50/30">
                                     <div class="p-8 pt-6">
                                         <div class="mb-4">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">MESSAGE TYPE</label>
                                             <input type="text" name="message_type" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="e.g. Birthday Message" required>
                                         </div>
                                         
                                         <div class="mb-4">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">PAGE TITLE</label>
                                             <input type="text" name="title" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="Enter preferred view title" required>
                                         </div>

                                         <div class="mb-4">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">GREETING</label>
                                             <input type="text" name="greeting" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="Hello there" required>
                                         </div>

                                         <div class="mb-0">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">MESSAGE CONTENT</label>
                                             <textarea name="message_body" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" rows="3" placeholder="Write your heartfelt message here..." required></textarea>
                                         </div>

                                         <div class="mb-4">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">RECIPIENT'S FULL NAME</label>
                                             <input type="text" name="recipient_name" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="Enter recipient's full name" required>
                                         </div>
                                         
                                         <div class="mb-4">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">RECIPIENT'S SPECIAL NAME</label>
                                             <input type="text" name="recipient_special_name" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="Enter recipient's special name">
                                         </div>

                                         <div class="mb-4">
                                             <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">YOUR NAME (SENDER)</label>
                                             <input type="text" name="sender_name" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="Gilbert Asare" required>
                                         </div>

                                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                             <div class="mb-0">
                                                 <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">LAST NOTES</label>
                                                 <input type="text" name="last_note" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]" placeholder="eg. Happy birthday...">
                                             </div>
                                             
                                             <div class="mb-0">
                                                 <label class="block mb-2 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">RECEIVING DATE</label>
                                                 <input type="date" name="receiving_date" class="w-full bg-black/5 border border-black/10 text-[var(--text-main)] py-2 px-3 rounded-lg text-sm font-[inherit] text-base transition-all duration-300 focus:outline-none focus:border-[var(--accent)] focus:bg-black/5 focus:ring-[3px] focus:ring-indigo-500/20 [&>option]:text-[#1a1a2e]">
                                             </div>
                                         </div>

                                         <div class="flex justify-end border-t border-gray-100 pt-6">
                                             <button type="submit" id="innerSaveBtn" class="bg-[var(--primary)] text-white py-2.5 px-6 rounded-lg font-semibold text-sm cursor-pointer transition-all hover:bg-indigo-600 shadow-sm flex items-center gap-2">
                                                 <i class="fas fa-save"></i> <span id="innerSaveText">Save Message</span>
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                            
                        </div>

                        <!-- Column 2 -->
                        <div class="flex flex-col gap-6 w-full">

                            <!-- Appearance & Media Card -->
                             <div class="glass-card w-full overflow-hidden transition-all duration-500" id="mediaAccordionCard">
                                 <!-- Clickable Header -->
                                 <div class="p-8 cursor-pointer hover:bg-black/5 transition-colors flex items-center justify-between" onclick="toggleMediaAccordion()">
                                     <div class="flex items-center gap-4">
                                         <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-xl shrink-0">
                                             <i class="fas fa-image"></i>
                                         </div>
                                         <div>
                                             <h3 class="text-lg font-bold text-[var(--text-main)] mb-1">Appearance & Media</h3>
                                             <p class="text-sm text-[var(--text-muted)] m-0" id="mediaSubtitle">Personalize how your message looks.</p>
                                         </div>
                                     </div>
                                     <div class="flex items-center gap-4">
                                         <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="mediaAccordionIcon"></i>
                                     </div>
                                 </div>

                                 <!-- Hidden Content -->
                                 <div id="mediaAccordionContent" class="hidden border-t border-gray-100 bg-slate-50/30">
                                     <div class="p-8 pt-6">
                                         <div class="mb-0">
                                             <label class="block mb-3 font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px]">ADD A PHOTO (OPTIONAL)</label>
                                             <div class="relative border-2 border-dashed border-gray-300 rounded-lg py-4 px-4 text-center hover:bg-black/5 transition-colors cursor-pointer">
                                                 <span class="text-sm font-semibold text-slate-700"><i class="fas fa-cloud-upload-alt mr-2"></i> Choose an image</span>
                                                 <input type="file" name="recipient_image" id="recipient_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                                             </div>
                                             <div class="bg-slate-50 border border-gray-100 rounded-lg p-3 mt-2 text-sm text-slate-500" id="recipient-image-info">No file selected</div>
                                         </div>

                                         <div class="mt-8 mb-0">
                                             <div class="flex items-center justify-between mb-3">
                                                 <label class="block font-bold text-[var(--text-main)] text-[0.85rem] uppercase tracking-[0.5px] m-0">UPLOAD MUSIC FILE</label>
                                                 <button type="button" onclick="toggleSpotifySearch()" class="w-10 h-10 rounded-full bg-[#1DB954] text-white flex items-center justify-center hover:scale-110 transition-transform shadow-md" title="Search on Spotify">
                                                     <i class="fab fa-spotify text-xl"></i>
                                                 </button>
                                             </div>

                                             <input type="hidden" name="spotify_url" id="spotify_url">
                                             <input type="hidden" name="spotify_name" id="spotify_name">

                                             <!-- Spotify Search Dropdown (Hidden by default) -->
                                             <div id="spotify-search-container" class="hidden mb-4 border border-gray-100 rounded-xl bg-slate-50 p-4">
                                                 <div class="relative mb-4">
                                                     <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                                     <input type="text" id="spotify-search-input" class="w-full bg-white border border-gray-200 text-slate-700 py-2 pl-10 pr-3 rounded-lg text-sm focus:outline-none focus:border-[#1DB954] focus:ring-[2px] focus:ring-[#1DB954]/20" placeholder="Search for tracks on Spotify...">
                                                 </div>
                                                 <div id="spotify-search-results" class="max-h-[250px] overflow-y-auto space-y-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent">
                                                     <p class="text-xs text-gray-500 text-center py-4">Type to search for tracks</p>
                                                 </div>
                                             </div>

                                             <div class="relative border-2 border-dashed border-gray-300 rounded-lg py-4 px-4 text-center hover:bg-black/5 transition-colors cursor-pointer">
                                                 <span class="text-sm font-semibold text-slate-700"><i class="fas fa-cloud-upload-alt mr-2"></i> Choose an audio file</span>
                                                 <input type="file" name="background_music" id="background_music" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="audio/*">
                                             </div>
                                             <div class="bg-slate-50 border border-gray-100 rounded-lg p-3 mt-2 text-sm text-slate-500 flex items-center justify-between" id="music-file-info">
                                                 <span>No file selected</span>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                            <!-- Templates Accordion Card -->
                            <div class="glass-card w-full overflow-hidden transition-all duration-500" id="templatesAccordionCard">
                                <!-- Clickable Header -->
                                <div class="p-8 cursor-pointer hover:bg-black/5 transition-colors flex items-center justify-between" onclick="toggleTemplatesAccordion()">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xl shrink-0">
                                            <i class="fas fa-th-large"></i>
                                        </div>
                                        <div>
                                             <h3 class="text-lg font-bold text-[var(--text-main)] mb-1">Template Info</h3>
                                             <p class="text-sm text-[var(--text-muted)] m-0" id="templateSubtitle">Select an option</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div id="accordionTemplatePreview" class="hidden items-center gap-2">
                                            <div id="accordionThumbnail" class="w-6 h-6 rounded border border-black/10 shadow-sm"></div>
                                            <span id="accordionTemplateName" class="text-sm font-semibold text-slate-700"></span>
                                        </div>
                                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="templatesAccordionIcon"></i>
                                    </div>
                                </div>
                                
                                <!-- Hidden Content (Grid) -->
                                <div id="templatesAccordionContent" class="hidden border-t border-gray-100 bg-slate-50/30">
                                    <div class="p-6">
                                        <input type="hidden" name="template_name" id="selectedTemplateInput" value="view-1">
                                        
                                        <!-- Tabs -->
                                        <div class="template-tabs [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]" style="display: flex; flex-wrap: nowrap; overflow-x: auto; justify-content: flex-start; white-space: nowrap; padding-bottom: 8px; gap: 8px;">
                                            <button type="button" class="template-tab active" data-filter="all">All</button>
                                            @foreach($themes as $theme => $count)
                                                <button type="button" class="template-tab" data-filter="{{ $theme }}">{{ ucfirst($theme) }}</button>
                                            @endforeach
                                        </div>

                                        <!-- Grid -->
                                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent" id="accordionTemplateGrid" style="max-height: 400px; overflow-y: auto; padding-right: 8px;">
                                            <!-- Cards generated by JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Generate Card -->
                            <div class="glass-card p-8 w-full text-center border-2 border-[var(--accent)] bg-indigo-500/5">
                                <h3 class="text-xl font-bold text-[var(--text-main)] mb-3">Ready to Share?</h3>
                                <p class="text-[0.95rem] text-[var(--text-muted)] mb-6">Generate your unique magic link and share it instantly.</p>
                                <button type="submit" id="mainSubmitBtn" class="bg-[var(--primary)] text-white py-4 px-10 w-auto rounded-[44px] font-semibold text-[1.1rem] cursor-pointer transition-all duration-300 inline-flex items-center justify-center shadow-[0_4px_15px_rgba(99,102,241,0.2)] hover:-translate-y-[2px] hover:shadow-[0_10px_20px_rgba(99,102,241,0.4)] relative">
                                    <span id="mainBtnLoader" class="hidden absolute left-4 w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                    <i class="fas fa-magic mr-2" id="mainSubmitIcon"></i> <span id="mainSubmitText">Generate Magic Link</span>
                                </button>
                            </div>

                        </div>
                    </div>

                </form>
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
            
            // Auto close on select
            toggleTemplatesAccordion();
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
                    const response = await fetch(`{{ route('user.music.spotify.search') }}?q=${encodeURIComponent(query)}`);
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
                fileInfo.innerHTML = `<span class="text-indigo-600 font-semibold"><i class="fas fa-check-circle mr-1"></i> ${this.files[0].name}</span>`;
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
                    <span class="text-indigo-600 font-semibold flex items-center gap-2">
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

            // Set loading states
            if (isMainButton) {
                document.getElementById('mainBtnLoader').classList.remove('hidden');
                document.getElementById('mainSubmitIcon').classList.add('hidden');
                submitter.disabled = true;
            } else if (submitter && submitter.id === 'innerSaveBtn') {
                document.getElementById('innerSaveText').innerText = 'Saving...';
                submitter.disabled = true;
            }

            const formData = new FormData(this);

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

                if (data.success) {
                    // Update hidden id
                    document.getElementById('current_message_id').value = data.message_id;
                    generatedLink = data.generated_link;
                    isShareMode = true;

                    // Update subtitles
                    const title = data.title;
                    if(document.getElementById('messageBasicsSubtitle')) document.getElementById('messageBasicsSubtitle').innerText = title;
                    if(document.getElementById('mediaSubtitle')) document.getElementById('mediaSubtitle').innerText = title;
                    if(document.getElementById('templateSubtitle')) document.getElementById('templateSubtitle').innerText = title;

                    // Transform main button
                    document.getElementById('mainSubmitText').innerText = 'Share Message';
                    document.getElementById('mainSubmitIcon').className = 'fas fa-share-alt mr-2';

                    // Transform inner button
                    if (submitter && submitter.id === 'innerSaveBtn') {
                        document.getElementById('innerSaveText').innerText = 'Saved!';
                        setTimeout(() => {
                            document.getElementById('innerSaveText').innerText = 'Update Message';
                        }, 2000);
                    }

                    // If it was the main button, trigger share
                    if (isMainButton && navigator.share) {
                        try {
                            await navigator.share({
                                title: title,
                                text: 'I made a magical message for you!',
                                url: generatedLink
                            });
                        } catch (err) {
                            console.log('Share canceled or failed', err);
                        }
                    } else if (isMainButton) {
                        // Fallback if no navigator.share
                        prompt("Copy your magic link:", generatedLink);
                    }
                } else {
                    alert(data.error || 'Something went wrong.');
                }
            } catch (error) {
                console.error('Error saving message:', error);
                alert('An error occurred while saving your message.');
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
                }
            }
        });
        }
    </script>
</body>

</html>

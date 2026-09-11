<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Features & Roadmap — WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
</head>
<body>
    <div class="ambient-grid"></div>

    <!-- Navigation Header -->
    @include('welcome.components.header')

    <main class="main" style="padding: 4rem 0;">
        <section class="container">
            <!-- Header -->
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-magic"></i> Platform Features</span>
                <h1 class="display-text">Features &amp; Future <span class="text-gradient">Updates</span></h1>
                <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 600px; margin-top: 16px;">Built to automate your care so you never truly forget a birthday. Discover WISP's current capabilities and our roadmap for the future.</p>
            </div>

            <!-- Current Features -->
            <div style="margin-bottom: 70px;">
                <h2 class="display-text" style="font-size: clamp(1.8rem, 4vw, 2.3rem); margin-bottom: 30px; text-align: center;">Current Core Features</h2>
                <div class="feature-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                    <div class="glass-card" style="padding: 32px;">
                        <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.2rem; margin-bottom: 18px;"><i class="fas fa-brain"></i></div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 10px;">AI Generator Engine</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Drafts personalized, intelligent messages based on tone choices (funny, warm, short) so you never get stuck.</p>
                    </div>
                    <div class="glass-card" style="padding: 32px;">
                        <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.2rem; margin-bottom: 18px;"><i class="fas fa-palette"></i></div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 10px;">Cinematic Layouts</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Multiple beautifully crafted, interactive themes tailored for birthdays, announcements, condolences, and holidays.</p>
                    </div>
                    <div class="glass-card" style="padding: 32px;">
                        <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.2rem; margin-bottom: 18px;"><i class="fas fa-clock"></i></div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 10px;">Scheduled Delivery</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Create your message today and lock it until a targeted release date. Recipients only view it on their specific day.</p>
                    </div>
                    <div class="glass-card" style="padding: 32px;">
                        <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.2rem; margin-bottom: 18px;"><i class="fas fa-lock"></i></div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 10px;">Private Vault Access</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Secure sensitive or personal greeting cards with access passcodes or PIN codes so only the intended eyes read them.</p>
                    </div>
                </div>
            </div>

            <!-- Roadmap -->
            <div style="margin-top: 80px;">
                <div style="text-align: center; margin-bottom: 12px;">
                    <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-route"></i> Roadmap</span>
                    <h2 class="display-text" style="font-size: clamp(1.9rem, 4vw, 2.5rem);">Future Updates &amp; Roadmap</h2>
                    <p style="color: var(--text-muted); font-size: 1.05rem; margin-top: 8px; max-width: 620px; margin-left: auto; margin-right: auto;">Our top 10 priorities on the path to a full SaaS platform. Tap any card to see what it involves and why it matters.</p>
                </div>

                <!-- Status legend -->
                <div class="legend-row">
                    <span class="legend-chip legend-in-progress"><span class="legend-dot"></span> In Progress</span>
                    <span class="legend-chip legend-planned"><span class="legend-dot"></span> Planned</span>
                    <span class="legend-chip legend-research"><span class="legend-dot"></span> Research</span>
                </div><br>

                @php
                    $roadmap = [
                        ['title' => 'Scheduled Delivery & Calendar System', 'category' => 'Core Platform', 'status' => 'in-progress', 'icon' => 'fa-calendar-days',
                            'summary' => 'Automatic delivery via Email or WhatsApp on a chosen date, backed by a built-in calendar that reminds you of upcoming birthdays and anniversaries.',
                            'impact' => 'Extends today\'s basic scheduling into a full planning tool, so you never rely on memory alone.'],
                        ['title' => 'Advanced AI "Magic" Tools', 'category' => 'AI & Personalization', 'status' => 'in-progress', 'icon' => 'fa-wand-magic-sparkles',
                            'summary' => 'AI suggestions that match tone to template, plus one-tap actions like "make it funnier," "make it shorter," or "make it romantic."',
                            'impact' => 'Builds directly on the current AI writer to make editing as fast as writing the first draft.'],
                        ['title' => 'Drag & Drop Wish Builder', 'category' => 'Creation Tools', 'status' => 'in-progress', 'icon' => 'fa-arrows-up-down-left-right',
                            'summary' => 'A lightweight visual builder that lets you drag text blocks, stickers, and images onto a canvas exactly how you want them.',
                            'impact' => 'Turns every template into a true blank canvas — no design experience required.'],
                        ['title' => 'PWA & Push Notifications', 'category' => 'Core Platform', 'status' => 'in-progress', 'icon' => 'fa-mobile-screen',
                            'summary' => 'Install WISP directly to your home screen without an app store, and get push reminders ahead of key dates.',
                            'impact' => 'Makes WISP feel like a native app while staying lightweight and instantly updatable.'],
                        ['title' => 'Collaborative Group Cards', 'category' => 'Creation Tools', 'status' => 'planned', 'icon' => 'fa-people-group',
                            'summary' => 'A "Group Wish" share link so friends and family can co-sign a card, add their own notes and photos, before it\'s delivered as one combined message.',
                            'impact' => 'Turns WISP from a one-to-one tool into something a whole friend group or office can rally around.'],
                        ['title' => 'Audio & Voice Note Integration', 'category' => 'Creation Tools', 'status' => 'planned', 'icon' => 'fa-microphone',
                            'summary' => 'Upload custom background music, record a voice message right in your browser, or choose from an ambient sound library.',
                            'impact' => 'Adds a whole new emotional layer — hearing a voice can land very differently than reading text alone.'],
                        ['title' => 'Dashboard Analytics & Read Receipts', 'category' => 'Core Platform', 'status' => 'planned', 'icon' => 'fa-chart-line',
                            'summary' => 'Track every message you\'ve sent and get notified the moment a recipient opens their wish.',
                            'impact' => 'Gives peace of mind that a scheduled wish actually landed — especially useful for important occasions.'],
                        ['title' => 'Dynamic Media & Asset Library', 'category' => 'Creation Tools', 'status' => 'planned', 'icon' => 'fa-images',
                            'summary' => 'A built-in media manager with photo cropping, plus direct Unsplash and Giphy integration so you never have to leave WISP to find the perfect image or GIF.',
                            'impact' => 'Cuts the time it takes to personalize a wish and removes the need to hunt for images elsewhere.'],
                        ['title' => 'Video Greetings Integration', 'category' => 'Creation Tools', 'status' => 'planned', 'icon' => 'fa-video',
                            'summary' => 'Record short videos directly from your camera and embed them into a card — no external video host required.',
                            'impact' => 'Video often carries more warmth than text or photos alone, especially for milestone moments.'],
                        ['title' => 'AI Video Avatar Generation', 'category' => 'AI & Personalization', 'status' => 'research', 'icon' => 'fa-clapperboard',
                            'summary' => 'Generate a customized, lip-syncing AI avatar that speaks your written wish aloud when the recipient opens it.',
                            'impact' => 'An experimental, high-wow feature that could set WISP apart from every other greeting tool on the market.'],
                    ];
                    $statusLabels = ['in-progress' => 'In Progress', 'planned' => 'Planned', 'research' => 'Research'];
                @endphp

                <div style="display: grid; grid-template-columns: 1fr; gap: 16px; max-width: 900px; margin: 0 auto;">
                    @foreach($roadmap as $i => $item)
                        <div class="glass-card roadmap-item">
                            <div class="roadmap-header">
                                <span class="roadmap-num">{{ $i + 1 }}</span>
                                <div class="roadmap-header-text">
                                    <h3><i class="fas {{ $item['icon'] }}" style="color: var(--coral); margin-right: 8px; font-size: 0.95rem;"></i>{{ $item['title'] }}</h3>
                                    <div class="roadmap-header-meta">
                                        <span class="category-tag">{{ $item['category'] }}</span>
                                        <span class="status-badge status-{{ $item['status'] }}">{{ $statusLabels[$item['status']] }}</span>
                                    </div>
                                </div>
                                <span class="roadmap-toggle">+</span>
                            </div>
                            <div class="roadmap-body">
                                <div class="roadmap-body-inner">
                                    <p>{{ $item['summary'] }}</p>
                                    <p><strong>Why it matters:</strong> {{ $item['impact'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- NEW: Suggest a feature CTA -->
            <div style="margin-top: 80px;">
                <div class="glass-card suggest-panel" style="padding: clamp(40px, 6vw, 64px) clamp(20px, 4vw, 40px); background: var(--plum); border: none; color: var(--cream);">
                    <i class="fas fa-lightbulb" style="font-size: 1.8rem; color: var(--coral); margin-bottom: 16px;"></i>
                    <h2 style="color: var(--cream); font-size: clamp(1.6rem, 4vw, 2.2rem); margin-bottom: 12px;">Don't see what you're looking for?</h2>
                    <p style="color: rgba(253,246,236,0.75); max-width: 520px; margin: 0 auto 26px; line-height: 1.6;">We build this roadmap around what our users actually ask for. Tell us what would make WISP better for you.</p>
                    <a href="mailto:gai.dev.official@gmail.com?subject=Feature%20Suggestion" class="btn btn-primary btn-large"><i class="fas fa-paper-plane"></i> Suggest a feature</a>
                </div>
            </div>

            <!-- Back link -->
            <div style="text-align: center; margin-top: 60px;">
                <a href="{{ url('/') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('welcome.components.footer')

    <script>
        // Header navigation script
        const navToggle = document.getElementById('navToggle');
        const mobileNav = document.getElementById('mobileNav');
        const navOverlay = document.getElementById('navOverlay');
        const navClose = document.getElementById('navClose');

        if (navToggle && mobileNav && navOverlay) {
            const toggleMenu = () => {
                mobileNav.classList.toggle('active');
                navOverlay.classList.toggle('active');
            };
            navToggle.addEventListener('click', toggleMenu);
            navClose.addEventListener('click', toggleMenu);
            navOverlay.addEventListener('click', toggleMenu);
        }

        // Roadmap accordion — open/close to view more detail
        document.querySelectorAll('.roadmap-header').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.closest('.roadmap-item');
                const toggle = header.querySelector('.roadmap-toggle');
                item.classList.toggle('active');
                toggle.textContent = item.classList.contains('active') ? '×' : '+';
            });
        });

        // Roadmap category filters
        const filterButtons = document.querySelectorAll('#roadmapFilters .template-tab');
        const roadmapItems = document.querySelectorAll('.roadmap-item');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const filter = btn.getAttribute('data-filter');
                roadmapItems.forEach(item => {
                    const match = filter === 'all' || item.dataset.category === filter;
                    item.classList.toggle('hidden-by-filter', !match);
                    if (!match) item.classList.remove('active');
                });
            });
        });
    </script>
</body>
</html>
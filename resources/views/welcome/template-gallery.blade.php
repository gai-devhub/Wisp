<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#E8674A">
    <title>Template Gallery — WISP</title>
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

    <main class="main-content-wrapper gallery-container" style="padding-top: 40px; padding-bottom: 80px;">
        <div class="gallery-title">
            <span class="badge"><i class="fas fa-palette"></i> Browse all templates</span>
            <h1 class="display-text" style="font-size: clamp(2rem, 4vw, 2.7rem); margin-top: 16px;">Pick your <span class="text-gradient">vibe</span></h1>
        </div>
        <p class="gallery-subtitle">View all {{ collect($themes)->sum() }} templates. No account needed. Click any card to preview.</p>

        <!-- Stats -->
        <div class="glass-card stats-bar" style="max-width: 900px; margin: 8px auto 56px;">
            <div class="stat-item">
                <div class="stat-number">{{ collect($themes)->sum() }}</div>
                <div class="stat-label">Templates</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ collect($themes)->count() }}</div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">Free</div>
                <div class="stat-label">Every preview</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">0</div>
                <div class="stat-label">Sign-ups required</div>
            </div>
        </div>

        <div class="template-tabs" role="tablist">
            <button type="button" class="template-tab active" data-filter="all">All</button>
            @foreach ($themes as $theme => $count)
                <button type="button" class="template-tab" data-filter="{{ $theme }}">{{ ucfirst($theme) }}</button>
            @endforeach
        </div>

        <div class="template-grid" id="template-grid">
            @php $counter = 0; @endphp
            @foreach ($themes as $theme => $count)
                @for ($n = 1; $n <= $count; $n++)
                    @php 
                        $counter++; 
                        $slug = ($theme === 'view') ? 'view-' . $n : $theme . '-' . $n;
                        $title = ($theme === 'view') ? 'View ' . $n : ucfirst($theme) . ' — ' . $n;
                    @endphp
                    <a href="{{ route('templates.gallery.preview', $slug) }}" target="_blank" rel="noopener" class="template-card" data-theme="{{ $theme }}">
                        <div class="template-card-placeholder">
                            <iframe class="template-iframe" src="{{ route('templates.gallery.preview', $slug) }}" scrolling="no" tabindex="-1"></iframe>
                        </div>
                        <span class="template-card-label">#{{ str_pad($counter, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="template-card-title">{{ $title }}</span>
                        <span class="template-card-preview"><i class="fas fa-external-link-alt"></i> Preview</span>
                    </a>
                @endfor
            @endforeach
        </div>

        <!-- How preview works -->
        <div style="margin-top: 90px;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 12px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-wand-magic-sparkles"></i> How it works</span>
                <h2 class="display-text" style="font-size: clamp(1.9rem, 4vw, 2.5rem);">From template to sent, in minutes</h2>
            </div>
            <div class="steps-grid">
                <div class="glass-card step-card" style="padding: 32px;">
                    <div class="step-num">1</div>
                    <h3 style="font-size: 1.15rem; margin-bottom: 10px;">Pick a template</h3>
                    <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Browse the gallery above and open any card to preview it full-screen — no account needed.</p>
                </div>
                <div class="glass-card step-card" style="padding: 32px;">
                    <div class="step-num">2</div>
                    <h3 style="font-size: 1.15rem; margin-bottom: 10px;">Make it yours</h3>
                    <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Add your message, photos, music, and recipient details — with AI help if you get stuck.</p>
                </div>
                <div class="glass-card step-card" style="padding: 32px;">
                    <div class="step-num">3</div>
                    <h3 style="font-size: 1.15rem; margin-bottom: 10px;">Schedule &amp; share</h3>
                    <p style="color: var(--text-muted); line-height: 1.6; margin: 0; font-size: 0.92rem;">Lock in a delivery date and get a link to share by WhatsApp, SMS, or Email.</p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div style="margin-top: 90px;">
            <div class="cta-panel">
                <i class="fas fa-heart" style="font-size: 1.8rem; color: var(--coral); margin-bottom: 16px;"></i>
                <h2 style="font-size: clamp(1.6rem, 4vw, 2.2rem); margin-bottom: 12px;">Found one you love?</h2>
                <p style="max-width: 520px; margin: 0 auto 26px; line-height: 1.6;">Start building your wish now — your first two messages are free, no account required.</p>
                <a href="{{ route('guest.try') }}" class="btn btn-primary btn-large"><i class="fas fa-magic"></i> Try WISP Free</a>
            </div>
        </div>
    </main>

    @include('welcome.components.footer')

    <script>
        document.querySelectorAll('.template-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.template-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                document.querySelectorAll('.template-card').forEach(card => {
                    const theme = card.dataset.theme;
                    const match = filter === 'all' || theme === filter;
                    card.classList.toggle('hidden-by-filter', !match);
                });
            });
        });

        // mobile nav
        const navToggle = document.getElementById('navToggle');
        const navClose = document.getElementById('navClose');
        const mobileNav = document.getElementById('mobileNav');
        const navOverlay = document.getElementById('navOverlay');
        const openNav = () => {
            mobileNav.classList.add('active');
            navOverlay.classList.add('active');
            mobileNav.setAttribute('aria-hidden', 'false');
        };
        const closeNav = () => {
            mobileNav.classList.remove('active');
            navOverlay.classList.remove('active');
            mobileNav.setAttribute('aria-hidden', 'true');
        };
        if (navToggle) navToggle.addEventListener('click', openNav);
        if (navOverlay) navOverlay.addEventListener('click', closeNav);
        mobileNav.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', closeNav);
        });
    </script>
</body>
</html>
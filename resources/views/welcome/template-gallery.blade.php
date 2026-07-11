<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>Template Gallery — WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
</head>
<body>
    <div class="ambient-grid">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
        <div class="blob blob-5"></div>
    </div>
    @include('welcome.components.header')

    <main class="gallery-container">
        <div class="gallery-title">
            <span class="badge"><i class="fas fa-palette"></i> Browse all templates</span>
            <h1>Pick your vibe</h1>
        </div>
        <p class="gallery-subtitle">View all {{ 6 + collect($themes)->sum() }} templates. No account needed. Click any card to preview.</p>

        <div class="template-tabs" role="tablist">
            <button type="button" class="template-tab active" data-filter="all">All</button>
            <button type="button" class="template-tab" data-filter="view">Views</button>
            @foreach ($themes as $theme => $count)
                <button type="button" class="template-tab" data-filter="{{ $theme }}">{{ ucfirst($theme) }}</button>
            @endforeach
        </div>

        <div class="template-grid" id="template-grid">
            @for ($v = 1; $v <= 6; $v++)
                <a href="{{ route('templates.gallery.preview', 'view-' . $v) }}" target="_blank" rel="noopener" class="template-card" data-theme="view">
                    <div class="template-card-placeholder">
                        <iframe class="template-iframe" src="{{ route('templates.gallery.preview', 'view-' . $v) }}" scrolling="no" tabindex="-1"></iframe>
                    </div>
                    <span class="template-card-label">#{{ str_pad($v, 3, '0', STR_PAD_LEFT) }}</span>
                    <span class="template-card-title">View {{ $v }}</span>
                    <span class="template-card-preview"><i class="fas fa-external-link-alt"></i> Preview</span>
                </a>
            @endfor
            @php $counter = 6; @endphp
            @foreach ($themes as $theme => $count)
                @for ($n = 1; $n <= $count; $n++)
                    @php $counter++; @endphp
                    <a href="{{ route('templates.gallery.preview', $theme . '-' . $n) }}" target="_blank" rel="noopener" class="template-card" data-theme="{{ $theme }}">
                        <div class="template-card-placeholder">
                            <iframe class="template-iframe" src="{{ route('templates.gallery.preview', $theme . '-' . $n) }}" scrolling="no" tabindex="-1"></iframe>
                        </div>
                        <span class="template-card-label">#{{ str_pad($counter, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="template-card-title">{{ ucfirst($theme) }} — {{ $n }}</span>
                        <span class="template-card-preview"><i class="fas fa-external-link-alt"></i> Preview</span>
                    </a>
                @endfor
            @endforeach
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
            document.body.style.overflow = 'hidden';
        };
        const closeNav = () => {
            mobileNav.classList.remove('active');
            navOverlay.classList.remove('active');
            mobileNav.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };
        if (navToggle) navToggle.addEventListener('click', openNav);
        if (navClose) navClose.addEventListener('click', closeNav);
        if (navOverlay) navOverlay.addEventListener('click', closeNav);
        mobileNav.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', closeNav);
        });
    </script>
</body>
</html>

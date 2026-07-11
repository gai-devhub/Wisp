    <nav>
        <div class="container nav-content">
            <a href="{{ url('/') }}#home" class="logo" style="text-decoration: none; color: inherit;">
                <div class="logo-icon">
                    <img src="{{ asset('img/logo.png') }}" alt="WISP">
                </div>
                <span style="color: var(--primary);">WISP</span>
            </a>
            <ul class="nav-links">
                <li><a href="{{ url('/') }}#home">Home</a></li>
                <li><a href="{{ url('/') }}#features">Features</a></li>
                <li><a href="{{ url('/') }}#steps">How it works</a></li>
                <li><a href="{{ url('/') }}#vibes">Styles</a></li>
                <li><a href="{{ route('templates.gallery') }}">Templates</a></li>
                <li><a href="{{ url('/') }}#about">About</a></li>
                <li><a href="{{ url('/') }}#team">Team</a></li>
                <li><a href="{{ url('/') }}#stories">Stories</a></li>
                <li><a href="{{ url('/') }}#faq">FAQ</a></li>
            </ul>
            <div class="nav-right">
                <button type="button" class="nav-toggle" aria-label="Open menu" id="navToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="auth-actions">
                    <a href="{{ route('auth.login') }}" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Log in</a>
                    <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-primary"><i class="fas fa-user-plus"></i> Join</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <div class="nav-overlay" id="navOverlay" aria-hidden="true"></div>
    <div class="mobile-nav" id="mobileNav" aria-hidden="true">
        <div class="nav-close-wrap" style="display: flex; width: 100%; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.25rem; padding-left: 12px;">
                <img src="{{ asset('img/logo.png') }}" alt="WISP" style="height: 28px; width: auto;">
                <span style="color: var(--primary);">WISP</span>
            </div>
            <button type="button" class="nav-toggle" aria-label="Close menu" id="navClose" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-muted);"><i class="fas fa-times"></i></button>
        </div>
        <a href="{{ url('/') }}#home" class="mobile-nav-link"><i class="fas fa-home" style="width: 24px;"></i> Home</a>
        <a href="{{ url('/') }}#features" class="mobile-nav-link"><i class="fas fa-star" style="width: 24px;"></i> Features</a>
        <a href="{{ url('/') }}#steps" class="mobile-nav-link"><i class="fas fa-magic" style="width: 24px;"></i> How it works</a>
        <a href="{{ url('/') }}#vibes" class="mobile-nav-link"><i class="fas fa-palette" style="width: 24px;"></i> Styles</a>
        <a href="{{ route('templates.gallery') }}" class="mobile-nav-link"><i class="fas fa-layer-group" style="width: 24px;"></i> Templates</a>
        <a href="{{ url('/') }}#about" class="mobile-nav-link"><i class="fas fa-info-circle" style="width: 24px;"></i> About</a>
        <a href="{{ url('/') }}#team" class="mobile-nav-link"><i class="fas fa-users" style="width: 24px;"></i> Team</a>
        <a href="{{ url('/') }}#stories" class="mobile-nav-link"><i class="fas fa-book-open" style="width: 24px;"></i> Stories</a>
        <a href="{{ url('/') }}#faq" class="mobile-nav-link"><i class="fas fa-question-circle" style="width: 24px;"></i> FAQ</a>
        <a href="{{ route('auth.login') }}" class="mobile-nav-link" style="margin-top: 12px;"><i class="fas fa-sign-in-alt" style="width: 24px;"></i> Log in</a>
        <a href="{{ route('auth.login') }}?tab=signup" class="mobile-nav-link" style="color: var(--primary); font-weight: 700; background: var(--primary-light); margin-top: 8px;"><i class="fas fa-user-plus" style="width: 24px;"></i> Join</a>
    </div>

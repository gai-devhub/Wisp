    <nav>
        <div class="container nav-content">
            <a href="{{ url('/') }}#home" class="logo" style="text-decoration: none; color: inherit;">
                <div class="logo-icon">
                    <img src="{{ asset('img/logo.png') }}" alt="WISP" style="width: 40px; height: 40px; object-fit: contain;">
                </div>
                <span class="logo-text" style="color: var(--primary);">WISP</span>
            </a>
            <ul class="nav-links">
                <li><a href="{{ route('welcome.features') }}">Features</a></li>
                <li><a href="{{ route('templates.gallery') }}">Templates</a></li>
                <li><a href="{{ route('welcome.about') }}">About</a></li>
                <li><a href="{{ route('welcome.faq') }}">FAQ &amp; Contact</a></li>
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
        <a href="{{ route('welcome.features') }}" class="mobile-nav-link"><i class="fas fa-star" style="width: 24px;"></i> Features</a>
        <a href="{{ route('templates.gallery') }}" class="mobile-nav-link"><i class="fas fa-layer-group" style="width: 24px;"></i> Templates</a>
        <a href="{{ route('welcome.about') }}" class="mobile-nav-link"><i class="fas fa-info-circle" style="width: 24px;"></i> About</a>
        <a href="{{ route('welcome.faq') }}" class="mobile-nav-link"><i class="fas fa-question-circle" style="width: 24px;"></i> FAQ &amp; Contact</a>
        <a href="{{ route('auth.login') }}" class="mobile-nav-link" style="margin-top: 12px;"><i class="fas fa-sign-in-alt" style="width: 24px;"></i> Log in</a>
        <a href="{{ route('auth.login') }}?tab=signup" class="mobile-nav-link" style="color: var(--primary); font-weight: 700; background: var(--primary-light); margin-top: 8px;"><i class="fas fa-user-plus" style="width: 24px;"></i> Join</a>
    </div>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h2 style="font-size: 1.8rem; display: flex; align-items: center; gap: 10px;">
                        <img src="{{ asset('img/logo.png') }}" alt="WISP" style="width: 40px; height: 40px; object-fit: contain;">
                        WISP
                    </h2>
                    <p style="margin-top: 18px; color: var(--text-muted); max-width: 280px;">Wishes that feel like magic. Create, personalize, and share in seconds — by Gilbert Asare · GAI Corp.</p>
                    <div style="margin-top: 28px; background: var(--primary-light); padding: 12px 24px; border-radius: 60px; display: inline-block;">
                        <span style="font-weight: 600; color: var(--text-main);">WISP · Gilbert Asare</span>
                    </div>
                </div>
                <div>
                    <h4>Product</h4>
                    <ul style="list-style: none; margin-top: 20px;">
                        <li style="margin-bottom: 12px;"><a href="{{ url('/') }}#features" style="color: var(--text-muted); text-decoration: none;">Features</a></li>
                        <li style="margin-bottom: 12px;"><a href="{{ route('templates.gallery') }}" style="color: var(--text-muted); text-decoration: none;">Templates</a></li>
                        <li style="margin-bottom: 12px;"><a href="{{ route('auth.login') }}" style="color: var(--text-muted); text-decoration: none;">Log in</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Company</h4>
                    <ul style="list-style: none; margin-top: 20px;">
                        <li style="margin-bottom: 12px;"><a href="{{ url('/') }}#about" style="color: var(--text-muted); text-decoration: none;">About WISP</a></li>
                        <li style="margin-bottom: 12px;"><a href="{{ url('/') }}#team" style="color: var(--text-muted); text-decoration: none;">Team</a></li>
                        <li style="margin-bottom: 12px;"><a href="{{ url('/') }}#stories" style="color: var(--text-muted); text-decoration: none;">Stories</a></li>
                        <li style="margin-bottom: 12px;"><a href="{{ url('/') }}#faq" style="color: var(--text-muted); text-decoration: none;">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Legal</h4>
                    <ul style="list-style: none; margin-top: 20px;">
                        <li style="margin-bottom: 12px;"><a href="{{ route('help.policy') }}" style="color: var(--text-muted); text-decoration: none;">Privacy</a></li>
                        <li style="margin-bottom: 12px;"><a href="{{ route('help.terms') }}" style="color: var(--text-muted); text-decoration: none;">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom" style="display: flex; justify-content: space-between; align-items: center; margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--glass-border);">
                <p style="color: var(--text-muted);">&copy; {{ date('Y') }} WISP. All rights reserved. A GAI Corp project.</p>
                <div style="display: flex; gap: 20px;">
                    <a href="#"><i class="fab fa-twitter" style="color: var(--text-muted);"></i></a>
                    <a href="#"><i class="fab fa-instagram" style="color: var(--text-muted);"></i></a>
                    <a href="#"><i class="fab fa-github" style="color: var(--text-muted);"></i></a>
                </div>
            </div>
        </div>
    </footer>

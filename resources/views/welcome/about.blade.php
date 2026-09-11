<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>About Us — WISP</title>
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
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-info-circle"></i> About Us</span>
                <h1 class="display-text">What is <span class="text-gradient">WISP</span>?</h1>
                <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 600px; margin-top: 16px;">WISP is a digital keepsake and scheduled messaging platform that makes wishing magical and personal.</p>
            </div>

            <!-- Content -->
            <div class="glass-card" style="padding: 48px; margin-bottom: 48px;">
                <p style="color: var(--text-main); font-size: 1.2rem; line-height: 1.8; margin-bottom: 24px; font-weight: 500;">
                    We believe that cards and birthday wishes shouldn't just be an afterthought. In a world full of quick, boring texts, WISP helps you create beautiful, customized, and memorable greeting pages that show people you actually care.
                </p>
                <p style="color: var(--text-muted); line-height: 1.75; margin-bottom: 20px;">
                    Users can easily design layout greetings, write personalized messages with the assistance of our customized AI writer, attach memorable photos or videos, select immersive music soundtracks, and control access permissions. Messages can then be scheduled to arrive exactly on their special day.
                </p>
                <p style="color: var(--text-muted); line-height: 1.75; margin-bottom: 30px;">
                    Whether it's a milestone birthday, a heartfelt thank you, romantic vows, or encouraging thoughts during difficult times, WISP preserves these memories as secure digital pages that can be opened and enjoyed anytime.
                </p>

                <div style="margin-top: 30px; font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-bottom: 16px;">With WISP, users can:</div>
                <ul style="list-style: none; color: var(--text-muted); line-height: 2.2; font-size: 1.05rem;">
                    <li><i class="fas fa-check-circle" style="color: var(--success); margin-right: 12px;"></i> Create and save personalized messages</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success); margin-right: 12px;"></i> Attach high-quality images, videos, and custom files</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success); margin-right: 12px;"></i> Add soundtracks and voice notes</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success); margin-right: 12px;"></i> Schedule messages for future dates</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success); margin-right: 12px;"></i> Protect private messages with passcode access controls</li>
                    <li><i class="fas fa-check-circle" style="color: var(--success); margin-right: 12px;"></i> Share direct links seamlessly to WhatsApp, SMS, or Email</li>
                </ul>
            </div>

            <!-- Mission & Audience -->
            <div class="about-mission-grid" style="gap: 30px; margin-bottom: 80px;">
                <div class="glass-card" style="padding: 32px; border-left: 5px solid var(--coral);">
                    <h3 style="font-size: 1.4rem; margin-bottom: 12px;"><i class="fas fa-rocket" style="color: var(--coral); margin-right: 8px;"></i> Our Mission</h3>
                    <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Make it easy for everyone to send wishes that feel genuine and look stunning — without the hassle.</p>
                </div>
                <div class="glass-card" style="padding: 32px; border-left: 5px solid var(--plum);">
                    <h3 style="font-size: 1.4rem; margin-bottom: 12px;"><i class="fas fa-user-group" style="color: var(--plum); margin-right: 8px;"></i> Who it's for</h3>
                    <p style="color: var(--text-muted); line-height: 1.6; margin: 0;">Friends, families, colleagues, and anyone who wants to say it right — birthdays, condolences, thank-yous, and every moment in between.</p>
                </div>
            </div>

            {{--
            <!-- NEW: Milestones stat bar -->
            <div style="margin-bottom: 80px;">
                <div class="glass-card stats-bar">
                    <div class="stat-item">
                        <div class="stat-number">2024</div>
                        <div class="stat-label">Founded</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">48K+</div>
                        <div class="stat-label">Wishes sent</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Team members</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Countries reached</div>
                    </div>
                </div>
            </div>
            --}}

            <!-- NEW: Our story timeline -->
            <div style="margin-bottom: 90px;">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                    <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-timeline"></i> Our Story</span>
                    <h2 class="display-text" style="font-size: clamp(2rem, 4vw, 2.7rem);">How WISP came to be</h2>
                    <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 560px; margin-top: 12px;">A small idea about a missed birthday text turned into a platform people trust with their most meaningful moments.</p>
                </div>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-lightbulb"></i></div>
                        <div class="timeline-year">The spark</div>
                        <h3>A forgotten birthday</h3>
                        <p>It all started when Gilbert truly forgot someone's birthday. To make up for it, he created a special digital birthday card with an apology letter inside. Shortly after, he designed another custom template for his auntie.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-cogs"></i></div>
                        <div class="timeline-year">The realization</div>
                        <h3>Automating the magic</h3>
                        <p>Realizing he often forgot important dates, Gilbert thought: "Why don't I build a system to schedule every single occasion?" The vision was born to automate sending personalized messages directly via phone or email.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-code"></i></div>
                        <div class="timeline-year">Late 2025</div>
                        <h3>Building the system</h3>
                        <p>The idea couldn't wait any longer. By the end of 2025, Gilbert started actively developing the WISP platform, laying the foundation for a tool that would hold onto messages and deliver them at exactly the right time.</p>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-rocket"></i></div>
                        <div class="timeline-year">July 15, 2026</div>
                        <h3>Live and active</h3>
                        <p>On his own birthday, Gilbert officially launched WISP to the world. The system went live, allowing anyone to easily schedule, automate, and preserve their most meaningful moments.</p>
                    </div>
                </div>
            </div>

            <!-- NEW: What we value -->
            <div style="margin-bottom: 90px;">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 40px;">
                    <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-compass"></i> What We Value</span>
                    <h2 class="display-text" style="font-size: clamp(2rem, 4vw, 2.7rem);">The principles behind WISP</h2>
                </div>
                <div class="values-grid">
                    <div class="glass-card value-card">
                        <div class="value-icon"><i class="fas fa-heart"></i></div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 8px;">Genuine over generic</h3>
                        <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6;">Every feature we build starts with one question: does this make a wish feel more personal?</p>
                    </div>
                    <div class="glass-card value-card">
                        <div class="value-icon"><i class="fas fa-lock"></i></div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 8px;">Private by default</h3>
                        <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6;">Your messages are yours. Passcodes and expiry controls are built in, not bolted on.</p>
                    </div>
                    <div class="glass-card value-card">
                        <div class="value-icon"><i class="fas fa-feather"></i></div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 8px;">Simple, not stripped-down</h3>
                        <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6;">Powerful tools should still take seconds to use. We design for the person in a hurry.</p>
                    </div>
                    <div class="glass-card value-card">
                        <div class="value-icon"><i class="fas fa-seedling"></i></div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 8px;">Built to grow with you</h3>
                        <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.6;">From a single birthday card to a team-wide celebration calendar — WISP scales with what you need.</p>
                    </div>
                </div>
            </div>

            <!-- NEW: Founder quote -->
            <div class="glass-card" style="padding: clamp(40px, 6vw, 64px) clamp(20px, 4vw, 40px); margin-bottom: 60px; background: var(--cream-deep); border: none;">
                <div class="quote-block">
                    <i class="fas fa-quote-left"></i>
                    <p>"We didn't set out to build another messaging app. We set out to make sure the people you love never have to wonder if you remembered."</p>
                    <div class="quote-attribution">
                        <img src="{{ asset('img/owner.jpeg') }}" alt="Gilbert Asare">
                        <div style="text-align: left;">
                            <strong style="display: block; font-size: 0.95rem; color: var(--plum);">Gilbert Asare</strong>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">CEO &amp; Founder, WISP</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back link -->
            <div style="text-align: center; margin-top: 40px;">
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
    </script>
</body>
</html>
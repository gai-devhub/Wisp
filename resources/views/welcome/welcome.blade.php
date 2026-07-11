<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">   
    <title>WISP — Wishes that feel like magic</title>
    <!-- Fonts (same Outfit + fallback) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 (identical) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- subtle base style – same color roots, advanced glassmorphism, but fresh & welcoming -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
</head>
<body>

    <script>
        // Register service worker if supported
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(() => console.log('SW registered')).catch(() => {});
        }

        // If app opened in standalone (installed), redirect straight to login/passcode
        (function(){
            function isStandalone() {
                return (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;
            }
            if (isStandalone()) {
                // Direct installed app to login route
                window.location.replace("{{ route('auth.login') }}");
            }
        })();
    </script>

    <!-- Advanced ambient canvas — exactly the elevated vibe from userpage but reimagined -->
    <div class="ambient-grid">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
        <div class="blob blob-5"></div>
    </div>

    <!-- Navigation — identical sharpness, better glassmorphism -->
    @include('welcome.components.header')

    <main class="main">
        <!-- HERO — advanced, kinetic, matches userpage boldness -->
        <section id="home" class="hero container">
            <div class="hero-grid">
                <div class="hero-text">
                    <span class="badge"><img src="{{ asset('img/logo.png') }}" alt="" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 6px;"> WISP · By Gilbert Asare</span>
                    <h1>
                        <span class="text-gradient">Wishes</span><br>that feel like<span style="color: var(--accent);"> magic.</span>
                    </h1>
                    <p><strong>WISP</strong> is your personal wish studio. From birthdays to “just because” — craft stunning AI‑powered greetings in seconds. Choose templates, add photos and music, share a link or send via WhatsApp. Your moments, elevated.</p>
                    <div class="hero-actions">
                        <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-primary btn-large"><i class="fas fa-wand-magic-sparkles"></i> Create your wish</a>
                        <a href="{{ route('guest.try') }}" class="btn btn-outline btn-large"><i class="fas fa-magic"></i> Try without account</a>
                        <a href="#about" class="btn btn-outline btn-large"><i class="fas fa-info-circle"></i> About WISP</a>
                        <a href="#stories" class="btn btn-outline btn-large"><i class="fas fa-play"></i> See stories</a>
                    </div>
                    <div style="margin-top: 40px; display: flex; flex-wrap: wrap; gap: 24px 32px; color: var(--text-muted);">
                        <span><i class="fas fa-check-circle" style="color: var(--success);"></i> 200+ templates</span>
                        <span><i class="fas fa-check-circle" style="color: var(--success);"></i> AI writer</span>
                        <span><i class="fas fa-check-circle" style="color: var(--success);"></i> Shareable links</span>
                        <span><i class="fas fa-check-circle" style="color: var(--success);"></i> WhatsApp ready</span>
                        <span><i class="fas fa-check-circle" style="color: var(--success);"></i> Free to start</span>
                    </div>
                </div>
                <!-- floating visual cards — same playful yet premium as dashboard preview -->
                <div class="hero-visual">
                    <div class="floating-card card-1 glass-card" style="background: rgba(255,255,255,0.8);">
                        <div class="mock-avatar">🎂</div>
                        <h4 style="font-weight: 700; margin-bottom: 4px;">Sarah's 30th</h4>
                        <p style="color: var(--text-muted);">"Best wish message ever. She cried!"</p>
                        <div style="margin-top: 18px; display: flex; gap: 6px;">
                            <i class="fas fa-heart" style="color: var(--accent);"></i>
                            <i class="fas fa-heart" style="color: var(--accent);"></i>
                            <i class="fas fa-heart" style="color: var(--accent);"></i>
                        </div>
                    </div>
                    <div class="floating-card card-2 glass-card" style="width: 260px;">
                        <div class="mock-avatar">🎓</div>
                        <h4 style="font-weight: 700; margin-bottom: 4px;">Graduation</h4>
                        <p style="color: var(--text-muted);">"Clever and so personal. Thank you!"</p>
                    </div>
                    <div class="floating-card card-3 glass-card" style="background: white; border: 2px solid rgba(99,102,241,0.3);">
                        <i class="fas fa-quote-left" style="color: var(--primary); font-size: 1.6rem; margin-bottom: 12px;"></i>
                        <p style="font-size: 1.05rem;">I sent a wish in 40 seconds. He thought I spent hours.</p>
                        <div style="margin-top: 16px; display: flex; align-items: center; gap: 10px;">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=64&h=64&fit=crop" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                            <span style="font-weight: 600;">Maria</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Marquee – occasions infinite – same energy as userpage's preview carousel but waaay cooler -->
        <section class="marquee-section">
            <div class="marquee">
                <div class="marquee-content">
                    <span class="marquee-tag glass"><i class="fas fa-birthday-cake"></i> Birthday</span>
                    <span class="marquee-tag glass" style="background: var(--primary-light);"><i class="fas fa-lightbulb"></i> Inspirational</span>
                    <span class="marquee-tag glass"><i class="fas fa-ring"></i> Wedding Vows</span>
                    <span class="marquee-tag glass"><i class="fas fa-dove"></i> Condolences</span>
                    <span class="marquee-tag glass" style="background: var(--primary-light);"><i class="fas fa-hand-holding-medical"></i> Get Well Soon</span>
                    <span class="marquee-tag glass"><i class="fas fa-hands-clapping"></i> Thank You</span>
                    <span class="marquee-tag glass"><i class="fas fa-award"></i> Congratulations</span>
                    <span class="marquee-tag glass" style="background: var(--primary-light);"><i class="fas fa-glass-cheers"></i> Wedding Wishes</span>
                    <span class="marquee-tag glass"><i class="fas fa-heart"></i> Anniversary</span>
                    <span class="marquee-tag glass"><i class="fas fa-baby-carriage"></i> New Baby</span>
                    <span class="marquee-tag glass" style="background: var(--primary-light);"><i class="fas fa-graduation-cap"></i> Graduation</span>
                    <span class="marquee-tag glass"><i class="fas fa-umbrella-beach"></i> Retirement</span>
                    <span class="marquee-tag glass"><i class="fas fa-briefcase"></i> Promotion</span>
                    <span class="marquee-tag glass" style="background: var(--primary-light);"><i class="fas fa-champagne-glasses"></i> New Year</span>
                    <span class="marquee-tag glass"><i class="fas fa-gifts"></i> Holiday</span>
                    <span class="marquee-tag glass"><i class="fas fa-cloud"></i> Thinking of You</span>
                    <span class="marquee-tag glass" style="background: var(--primary-light);"><i class="fas fa-face-frown"></i> Apology</span>
                    <span class="marquee-tag glass"><i class="fas fa-kiss-wink-heart"></i> Romance</span>
                    <span class="marquee-tag glass"><i class="fas fa-user-group"></i> Friendship</span>
                </div>
            </div>
        </section>

        <!-- FEATURE section: 4 pillars -->
        <section id="features" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                <span class="badge" style="margin-bottom: 24px;"><i class="fa-regular fa-sparkle"></i> Why WISP</span>
                <h2 class="display-text" style="max-width: 800px;">Better than candles <span class="text-gradient">on a cake</span></h2>
                <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 640px; margin-top: 16px;">Everything you need to create, personalize, and send wishes that people actually remember.</p>
            </div>
            <div class="feature-grid">
                <div class="glass-card" style="padding: 40px 28px;">
                    <div class="feature-icon"><i class="fas fa-brain"></i></div>
                    <h3 style="font-size: 1.8rem; margin-bottom: 14px; font-weight: 700;">AI whisper</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">Our engine writes like you— only wittier, warmer and never awkward.</p>
                </div>
                <div class="glass-card" style="padding: 40px 28px;">
                    <div class="feature-icon"><i class="fas fa-palette"></i></div>
                    <h3 style="font-size: 1.8rem; margin-bottom: 14px; font-weight: 700;">Cinematic designs</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">From minimalist to confetti‑explosion. 6 unique template families.</p>
                </div>
                <div class="glass-card" style="padding: 40px 28px;">
                    <div class="feature-icon"><i class="fas fa-link"></i></div>
                    <h3 style="font-size: 1.8rem; margin-bottom: 14px; font-weight: 700;">Smart links</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">Expire when you want, track views, send via WhatsApp in one tap.</p>
                </div>
                <div class="glass-card" style="padding: 40px 28px;">
                    <div class="feature-icon"><i class="fas fa-music"></i></div>
                    <h3 style="font-size: 1.8rem; margin-bottom: 14px; font-weight: 700;">Add soundtrack</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">Upload background music — make your message a moment.</p>
                </div>
            </div>
            </div>
        </section>

        <!-- From blank to brilliance — 3 steps to WISP -->
        <section id="steps" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-check"></i> 3 steps to WISP</span>
                <h2 style="font-size: 3rem; font-weight: 700; color: var(--text-main);">From blank to brilliance</h2>
            </div>
            <div class="steps-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; max-width: 960px; margin: 0 auto;">
                <div class="glass-card step-card" style="padding: 40px 32px; text-align: center;">
                    <span style="display: inline-flex; width: 56px; height: 56px; background: linear-gradient(145deg, var(--primary), var(--secondary)); color: white; border-radius: 50%; align-items: center; justify-content: center; font-weight: 700; font-size: 1.5rem; margin-bottom: 20px;">1</span>
                    <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 12px;">Pick occasion</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">Birthday, anniversary, or just because — we have 30+ categories.</p>
                </div>
                <div class="glass-card step-card" style="padding: 40px 32px; text-align: center;">
                    <span style="display: inline-flex; width: 56px; height: 56px; background: linear-gradient(145deg, var(--primary), var(--secondary)); color: white; border-radius: 50%; align-items: center; justify-content: center; font-weight: 700; font-size: 1.5rem; margin-bottom: 20px;">2</span>
                    <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 12px;">Customize</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">Add name, message, photo, music. AI helps you polish.</p>
                </div>
                <div class="glass-card step-card" style="padding: 40px 32px; text-align: center;">
                    <span style="display: inline-flex; width: 56px; height: 56px; background: linear-gradient(145deg, var(--primary), var(--secondary)); color: white; border-radius: 50%; align-items: center; justify-content: center; font-weight: 700; font-size: 1.5rem; margin-bottom: 20px;">3</span>
                    <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 12px;">Share</h3>
                    <p style="color: var(--text-muted); line-height: 1.6;">Send a link or share via WhatsApp — done in seconds.</p>
                </div>
            </div>
        </section>

        <!-- Pick your vibe — 6 signature styles -->
        <section id="vibes" class="container" style="padding: 6rem 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; margin-bottom: 40px;">
                <div>
                    <span class="badge" style="margin-bottom: 12px;"><i class="fas fa-palette"></i> 6 signature styles</span>
                    <h2 style="font-size: 3rem; font-weight: 700; margin-top: 8px;">Pick your vibe</h2>
                </div>
                <p style="max-width: 400px; color: var(--text-muted); font-size: 1.1rem;">From elegant minimal to glitter bomb. All responsive.</p>
            </div>
            <div class="vibes-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                <div class="glass-card vibe-card" style="padding: 32px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(99,102,241,0.12); border-radius: 24px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-sun" style="font-size: 2rem; color: var(--primary);"></i></div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Aurora</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Soft gradients, elegant serif.</p>
                    <a href="{{ route('templates.gallery') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">14 templates →</a>
                </div>
                <div class="glass-card" style="padding: 32px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(236,72,153,0.12); border-radius: 24px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-birthday-cake" style="font-size: 2rem; color: var(--accent);"></i></div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Confetti</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Party vibes, bold colors.</p>
                    <a href="{{ route('templates.gallery') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">12 templates →</a>
                </div>
                <div class="glass-card" style="padding: 32px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(168,85,247,0.12); border-radius: 24px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-feather" style="font-size: 2rem; color: var(--secondary);"></i></div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Minimal</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Clean lines, timeless.</p>
                    <a href="{{ route('templates.gallery') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">18 templates →</a>
                </div>
                <div class="glass-card" style="padding: 32px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(16,185,129,0.12); border-radius: 24px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-leaf" style="font-size: 2rem; color: var(--success);"></i></div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Garden</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Nature-inspired, soft tones.</p>
                    <a href="{{ route('templates.gallery') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">10 templates →</a>
                </div>
                <div class="glass-card" style="padding: 32px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(245,158,11,0.12); border-radius: 24px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-sparkles" style="font-size: 2rem; color: var(--warning);"></i></div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Glitter</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Shimmer and shine.</p>
                    <a href="{{ route('templates.gallery') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">8 templates →</a>
                </div>
                <div class="glass-card" style="padding: 32px; text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(99,102,241,0.12); border-radius: 24px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-heart" style="font-size: 2rem; color: var(--accent);"></i></div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">Romance</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Sweet and heartfelt.</p>
                    <a href="{{ route('templates.gallery') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">15 templates →</a>
                </div>
            </div>
        </section>

        <!-- ABOUT WISP -->
        <section id="about" class="container" style="padding: 6rem 0;">
            <div class="glass-card" style="padding: 60px 50px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center;">
                <div>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 28px;">
                        <img src="{{ asset('img/logo.png') }}" alt="WISP" style="width: 72px; height: 72px; object-fit: contain;">
                        <div>
                            <span class="badge"><i class="fas fa-heart"></i> About us</span>
                            <h2 style="font-size: 2.5rem; font-weight: 700; margin-top: 8px;">What is <span class="text-gradient">WISP</span>?</h2>
                        </div>
                    </div>
                    <p style="color: var(--text-muted); line-height: 1.75; margin-bottom: 20px; font-size: 1.1rem;">WISP is a wish &amp; message creator built for real life. Whether it’s a birthday, graduation, thank-you note, or just because — you get beautiful templates, an AI writer, and one-tap sharing so your words hit different.</p>
                    <p style="color: var(--text-muted); line-height: 1.75; margin-bottom: 24px;">We believe every moment deserves a thoughtful message. No more last-minute generic cards. Create a custom wish in under a minute, add a photo or song, set an expiry, and share via link or WhatsApp. Simple, personal, memorable.</p>
                    <ul style="list-style: none; color: var(--text-muted); line-height: 2;">
                        <li><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Built by Gilbert Asare · GAI Corp</li>
                        <li><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Privacy-first, no spam</li>
                        <li><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Free to use, upgrade when you need more</li>
                    </ul>
                </div>
                <div style="display: grid; gap: 20px;">
                    <div class="glass-card" style="padding: 28px; border-left: 4px solid var(--primary);">
                        <h4 style="font-size: 1.25rem; margin-bottom: 8px;">Our mission</h4>
                        <p style="color: var(--text-muted); line-height: 1.6;">Make it easy for everyone to send wishes that feel genuine and look stunning — without the hassle.</p>
                    </div>
                    <div class="glass-card" style="padding: 28px; border-left: 4px solid var(--accent);">
                        <h4 style="font-size: 1.25rem; margin-bottom: 8px;">Who it’s for</h4>
                        <p style="color: var(--text-muted); line-height: 1.6;">Friends, families, colleagues, and anyone who wants to say it right — birthdays, condolences, thank-yous, and every moment in between.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Brains behind the brilliance — Team -->
        <section id="team" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-users"></i> The WISP geniuses</span>
                <h2 style="font-size: 3rem; font-weight: 700; color: var(--text-main);">Brains behind the brilliance</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; max-width: 1100px; margin: 0 auto;">
                <div class="glass-card" style="padding: 36px; text-align: center;">
                    <img src="{{ asset('img/owner.jpeg') }}" alt="Gilbert Asare" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                    <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 6px;">Gilbert Asare</h3>
                    <p style="color: var(--accent); font-weight: 600; font-size: 0.95rem;">CEO &amp; Founder</p>
                </div>
                <div class="glass-card" style="padding: 36px; text-align: center;">
                    <img src="{{ asset('img/isp1.jpeg') }}" alt="Juliana Asare" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                    <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 6px;">Juliana Asare</h3>
                    <p style="color: var(--accent); font-weight: 600; font-size: 0.95rem;">First inspiration &amp; motivator</p>
                </div>
                <div class="glass-card" style="padding: 36px; text-align: center;">
                    <img src="{{ asset('img/isp2.jpeg') }}" alt="Loh" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                    <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 6px;">Afia</h3>
                    <p style="color: var(--accent); font-weight: 600; font-size: 0.95rem;">Second inspiration</p>
                </div>
                <div class="glass-card" style="padding: 36px; text-align: center;">
                    <img src="{{ asset('img/isp3.jpeg') }}" alt="Loh" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                    <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 6px;">Theophilus Yaw</h3>
                    <p style="color: var(--accent); font-weight: 600; font-size: 0.95rem;">Chief Happiness Officer</p>
                </div>
            </div>
        </section>

        <!-- Recognized by -->
        <section class="container" style="padding: 5rem 0;">
            <div class="glass-card" style="padding: clamp(20px, 4vw, 50px); text-align: center;">
                <span class="badge" style="margin-bottom: 24px;"><i class="fas fa-trophy"></i> Recognized by</span>
                <div class="awards-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 32px; align-items: start;">
                    <div>
                        <i class="fas fa-medal" style="font-size: 2.5rem; color: #f59e0b; margin-bottom: 16px;"></i>
                        <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">TechCrunch</h4>
                        <p style="color: var(--text-muted);">"Most innovative"</p>
                    </div>
                    <div>
                        <i class="fas fa-medal" style="font-size: 2.5rem; color: #94a3b8; margin-bottom: 16px;"></i>
                        <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">Product Hunt</h4>
                        <p style="color: var(--text-muted);">"#1 in Greetings"</p>
                    </div>
                    <div>
                        <i class="fas fa-medal" style="font-size: 2.5rem; color: var(--secondary); margin-bottom: 16px;"></i>
                        <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">Forbes</h4>
                        <p style="color: var(--text-muted);">"30 under 30"</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visual stories -->
        <section id="stories" class="container" style="padding: 5rem 0 7rem;">
            <div style="display: flex; justify-content: space-between; align-items: end; margin-bottom: 40px;">
                <div>
                    <span class="badge"><i class="fas fa-crown"></i> WISP in action</span>
                    <h2 style="font-size: clamp(2rem, 6vw, 3.2rem); font-weight: 700; margin-top: 8px; line-height: 1.1;">Stories & <span style="color: var(--accent);">magic</span></h2>
                </div>
                <p style="max-width: 380px; color: var(--text-muted);">See how others use WISP to create wishes that get remembered.</p>
            </div>
            <div class="story-grid">
                <div class="story-card glass-card" style="border-radius: 40px;">
                    <video src="https://www.w3schools.com/html/mov_bbb.mp4" poster="https://images.unsplash.com/photo-1513201099705-a9746e1e201f?auto=format&fit=crop&q=80&w=800" controls preload="none"></video>
                    <div class="story-overlay" style="pointer-events: none;">
                        <span style="background: rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 30px; font-size: 0.9rem; backdrop-filter: blur(6px);">Gilbert Asare · CEO</span>
                        <h3 style="font-size: 2rem; margin-top: 10px;">"We made wishes wilder"</h3>
                    </div>
                </div>
                <div class="story-card glass-card">
                    <video src="https://www.w3schools.com/html/mov_bbb.mp4" poster="https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&q=80&w=800" controls preload="none"></video>
                    <div class="story-overlay" style="pointer-events: none;">
                        <span style="background: rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 30px; font-size: 0.9rem; backdrop-filter: blur(6px);">Juliana Asare · First inspiration</span>
                        <h3 style="font-size: 2rem; margin-top: 10px;">"From 0 to wish in 12 sec"</h3>
                    </div>
                </div>
                <div class="story-card glass-card">
                    <video src="https://www.w3schools.com/html/mov_bbb.mp4" poster="https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&q=80&w=800" controls preload="none"></video>
                    <div class="story-overlay" style="pointer-events: none;">
                        <span style="background: rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 30px; font-size: 0.9rem; backdrop-filter: blur(6px);">Afia · Second inspiration</span>
                        <h3 style="font-size: 2rem; margin-top: 10px;">"We cry happy tears"</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ & Testimonials -->
        <section id="faq" class="container" style="padding: 3rem 0 6rem;">
            <div style="display: block;">
                <div style="margin-bottom: 4rem;">
                    <span class="badge"><i class="fas fa-question-circle"></i> FAQ</span>
                    <h2 style="font-size: 2.8rem; font-weight: 700; margin: 20px 0 24px;">Questions about <span class="text-gradient">WISP</span>?</h2>
                    <div class="faq-grid">
                        <div class="faq-block glass-card">
                            <div class="faq-item">
                                <span style="font-weight: 600;">Is WISP really free?</span>
                                <div class="faq-plus">+</div>
                            </div>
                            <div class="faq-answer">Yes! WISP is free to start. Create wishes, use templates, and share links at no cost. We offer premium features for power users who need more.</div>
                        </div>
                        <div class="faq-block glass-card">
                            <div class="faq-item">
                                <span style="font-weight: 600;">Can I use my own photos?</span>
                                <div class="faq-plus">+</div>
                            </div>
                            <div class="faq-answer">Absolutely. Upload your photos to personalize any template. Add a recipient photo, background image, or your own pictures to make wishes truly yours.</div>
                        </div>
                        <div class="faq-block glass-card">
                            <div class="faq-item">
                                <span style="font-weight: 600;">Do wishes expire?</span>
                                <div class="faq-plus">+</div>
                            </div>
                            <div class="faq-answer">You choose the expiry. Set a custom duration (e.g. 24 hours, 7 days) or leave it open. Expired links stop working so your message stays special.</div>
                        </div>
                        <div class="faq-block glass-card">
                            <div class="faq-item">
                                <span style="font-weight: 600;">How do I share a wish?</span>
                                <div class="faq-plus">+</div>
                            </div>
                            <div class="faq-answer">Copy the unique link we generate for each wish. Share it via text, email, or social. You can also send it directly via WhatsApp with one tap.</div>
                        </div>
                        <div class="faq-block glass-card">
                            <div class="faq-item">
                                <span style="font-weight: 600;">Who built WISP?</span>
                                <div class="faq-plus">+</div>
                            </div>
                            <div class="faq-answer">WISP was built by Gilbert Asare at GAI Corp. Our mission is to make it easy for everyone to send wishes that feel genuine and look stunning.</div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="badge"><i class="fas fa-star"></i> Loved by many</span>
                    <h2 style="font-size: 2.8rem; font-weight: 700; margin: 20px 0 32px;">What people say</h2>
                    <div class="testimonial-scroll">
                        <div class="testimonial-item glass">
                            <i class="fas fa-quote-right" style="color: var(--primary); font-size: 2rem; opacity: 0.3; margin-bottom: 16px;"></i>
                            <p style="font-size: 1.25rem; margin-bottom: 24px;">“I sent a wish to my sister in NYC. She thought I hired a poet.”</p>
                            <div style="display: flex; gap: 14px; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=64&h=64&fit=crop" style="width: 48px; height: 48px; border-radius: 50%;">
                                <div><strong>Ben Carter</strong><span style="color: var(--text-muted); display: block;">Brother of the year</span></div>
                            </div>
                        </div>
                        <div class="testimonial-item glass">
                            <i class="fas fa-quote-right" style="color: var(--secondary); font-size: 2rem; opacity: 0.3; margin-bottom: 16px;"></i>
                            <p style="font-size: 1.25rem; margin-bottom: 24px;">“The templates are so gorgeous I almost didn’t send them. Almost.”</p>
                            <div style="display: flex; gap: 14px; align-items: center;">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=64&h=64&fit=crop" style="width: 48px; height: 48px; border-radius: 50%;">
                                <div><strong>Sophia Lee</strong><span style="color: var(--text-muted); display: block;">Design critic</span></div>
                            </div>
                        </div>
                    </div>
                    <!-- <div style="margin-top: 20px; display: flex; gap: 12px; justify-content: flex-end;">
                        <button class="btn btn-outline"><i class="fas fa-arrow-left"></i></button>
                        <button class="btn btn-outline"><i class="fas fa-arrow-right"></i></button>
                    </div> -->
                </div>
            </div>
        </section>

        <!-- Never miss a wish — Newsletter -->
        <section class="container" style="padding-bottom: 4rem;">
            <div class="glass-card" style="padding: clamp(32px, 5vw, 60px) clamp(20px, 4vw, 50px); background: linear-gradient(145deg, rgba(99,102,241,0.06), rgba(236,72,153,0.06)); border: 1px solid rgba(99,102,241,0.15); text-align: center;">
                <h2 style="font-size: clamp(1.8rem, 5vw, 2.5rem); font-weight: 700; margin-bottom: 12px;">Never miss a wish</h2>
                <p style="font-size: clamp(1rem, 3vw, 1.2rem); color: var(--text-muted); margin-bottom: 24px;">Get new templates, AI updates and WISP magic.</p>
                <form action="#" method="POST" style="display: flex; gap: 12px; max-width: 480px; margin: 0 auto; flex-wrap: wrap; justify-content: center;">
                    @csrf
                    <input type="email" name="email" placeholder="Your email" required style="flex: 1; min-width: 220px; padding: 14px 20px; border: 1px solid var(--border); border-radius: 12px; font-size: 1rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 14px 32px;">Subscribe</button>
                </form>
            </div>
        </section>

        <!-- CTA -->
        <section class="container" style="padding-bottom: 4rem;">
            <div class="glass-card" style="padding: clamp(40px, 6vw, 80px) clamp(20px, 4vw, 40px); background: linear-gradient(145deg, rgba(99,102,241,0.08), rgba(236,72,153,0.08)); border: 1px solid rgba(99,102,241,0.2); text-align: center;">
                <img src="{{ asset('img/logo.png') }}" alt="WISP" style="width: clamp(48px, 6vw, 64px); height: auto; margin-bottom: 20px; object-fit: contain;">
                <h2 style="font-size: clamp(2rem, 6vw, 3.2rem); font-weight: 700; margin-bottom: 20px;">Ready to send wishes with <span class="text-gradient">WISP</span>?</h2>
                <p style="font-size: clamp(1rem, 3vw, 1.35rem); color: var(--text-muted); max-width: 600px; margin: 0 auto 28px;">Join thousands who never miss a moment. Create your first wish in under a minute.</p>
                <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-primary btn-large" style="padding: clamp(14px, 3vw, 18px) clamp(32px, 5vw, 54px); font-size: clamp(1.1rem, 3vw, 1.3rem);"><i class="fas fa-wand-magic-sparkles"></i> Start with WISP</a>
                <div style="margin-top: 30px; color: var(--text-muted);"><i class="fas fa-shield-alt"></i> No credit card. Free to start.</div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('welcome.components.footer')

    <script>
        // subtle nav background change – same as dashboard's smoothness
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.style.background = 'rgba(255,255,255,0.85)';
                nav.style.backdropFilter = 'blur(22px)';
                nav.style.borderBottom = '1px solid rgba(99,102,241,0.2)';
            } else {
                nav.style.background = 'rgba(255,255,255,0.7)';
                nav.style.borderBottom = '1px solid var(--glass-border)';
            }
        });

        // FAQ accordion – click to show/hide answer
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const block = item.closest('.faq-block');
                const plus = item.querySelector('.faq-plus');
                block.classList.toggle('active');
                plus.textContent = block.classList.contains('active') ? '−' : '+';
            });
        });

        // testimonial scroll buttons (simple)
        const scrollBox = document.querySelector('.testimonial-scroll');
        const btns = document.querySelectorAll('.fa-arrow-left, .fa-arrow-right');
        if (btns.length && scrollBox) {
            btns[0].parentElement.addEventListener('click', () => scrollBox.scrollBy({ left: -420, behavior: 'smooth' }));
            btns[1].parentElement.addEventListener('click', () => scrollBox.scrollBy({ left: 420, behavior: 'smooth' }));
        }

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

        // Hero Visual Cards Cycling Animation
        const heroCards = Array.from(document.querySelectorAll('.hero-visual .floating-card'));
        if (heroCards.length > 0) {
            let zIndices = [1, 2, 3];
            
            heroCards.forEach((card, index) => {
                card.style.zIndex = zIndices[index] || 1;
                card.style.cursor = 'pointer';
                card.style.transition = 'box-shadow 0.4s ease'; 
            });

            const bringCardToFront = (clickedIndex) => {
                const maxZ = Math.max(...zIndices);
                if (zIndices[clickedIndex] === maxZ) return;

                zIndices[clickedIndex] = maxZ + 1;
                
                // Pop effect
                heroCards[clickedIndex].style.boxShadow = '0 30px 60px rgba(99,102,241,0.3)';
                setTimeout(() => {
                    heroCards[clickedIndex].style.boxShadow = '';
                }, 500);

                const sorted = [...zIndices].sort((a,b) => a - b);
                zIndices = zIndices.map(z => sorted.indexOf(z) + 1);

                heroCards.forEach((card, i) => {
                    card.style.zIndex = zIndices[i];
                });
            };

            heroCards.forEach((card, index) => {
                card.addEventListener('click', () => {
                    bringCardToFront(index);
                    resetCycle();
                });
            });

            let cycleInterval;
            const startCycle = () => {
                cycleInterval = setInterval(() => {
                    // Bring the bottom card to the front
                    const minZ = Math.min(...zIndices);
                    const indexToBringFront = zIndices.indexOf(minZ);
                    bringCardToFront(indexToBringFront);
                }, 5000);
            };

            const resetCycle = () => {
                clearInterval(cycleInterval);
                startCycle();
            };

            startCycle();
        }
    </script>
</body>
</html>
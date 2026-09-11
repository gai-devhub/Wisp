<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#2B1F3D">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <title>WISP — Wishes that feel like magic</title>
    <meta name="description" content="WISP is a digital keepsake and scheduled messaging platform. Craft meaningful AI-powered greetings, attach memories like photos and music, and schedule them for future delivery.">
    <meta name="keywords" content="WISP, wish studio, AI greetings, birthday wishes, digital cards, templates gallery, create wish">
    <link rel="canonical" href="{{ config('app.url') }}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ config('app.url') }}" />
    <meta property="og:title" content="WISP — Wishes that feel like magic" />
    <meta property="og:description" content="Craft stunning AI-powered greetings in seconds. Choose templates, add photos and music, share a link or send via WhatsApp." />
    <meta property="og:image" content="{{ asset('img/logo.png') }}" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ config('app.url') }}" />
    <meta property="twitter:title" content="WISP — Wishes that feel like magic" />
    <meta property="twitter:description" content="Craft stunning AI-powered greetings in seconds. Choose templates, add photos and music, share a link or send via WhatsApp." />
    <meta property="twitter:image" content="{{ asset('img/logo.png') }}" />

    <!-- JSON-LD Structured Data for Google Search Sitelinks -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Wisp",
      "url": "{{ config('app.url') }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "{{ config('app.url') }}/login?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Wisp Navigation links",
      "description": "Quick links to Wisp key pages",
      "itemListElement": [
        {
          "@type": "SiteNavigationElement",
          "position": 1,
          "name": "Log In",
          "url": "{{ route('auth.login') }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 2,
          "name": "Create Account",
          "url": "{{ route('auth.login') }}?tab=signup"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 3,
          "name": "Templates Gallery",
          "url": "{{ route('templates.gallery') }}"
        },
        {
          "@type": "SiteNavigationElement",
          "position": 4,
          "name": "Try Wisp (Guest Mode)",
          "url": "{{ route('guest.try') }}"
        }
      ]
    }
    </script>

    <!-- Fonts: Fraunces (display) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
</head>
<body>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(() => console.log('SW registered')).catch(() => {});
        }
        (function(){
            function isStandalone() {
                return (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;
            }
            if (isStandalone()) {
                window.location.replace("{{ route('auth.login') }}");
            }
        })();
    </script>

    <div class="ambient-grid"></div>

    @include('welcome.components.header')

    <main class="main">

        <!-- HERO -->
        <section id="home" class="hero container">
            <div class="hero-grid">
                <div class="hero-text">
                    <h1>
                        <span class="text-gradient">Wishes</span> that feel<br>like <span style="color: var(--plum);">magic.</span>
                    </h1>
                    <p>Born from a forgotten birthday and a belated apology letter, <strong>WISP</strong> is a digital keepsake and scheduled messaging platform designed so you never miss the moments that matter. Write it once, schedule it, and let WISP automate the magic.</p>
                    <div class="hero-actions">
                        <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-primary btn-large"><i class="fas fa-wand-magic-sparkles"></i> Create your wish</a>
                        <a href="{{ route('guest.try') }}" class="btn btn-outline btn-large"><i class="fas fa-magic"></i> Try without an account</a>
                    </div>
                    <div class="trust-row">
                        <span><i class="fas fa-check-circle"></i> 200+ templates</span>
                        <span><i class="fas fa-check-circle"></i> AI writing assist</span>
                        <span><i class="fas fa-check-circle"></i> Scheduled delivery</span>
                        <span><i class="fas fa-check-circle"></i> WhatsApp ready</span>
                        <span><i class="fas fa-check-circle"></i> Free to start</span>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="floating-card card-1 glass-card">
                        <div class="mock-avatar">🎂</div>
                        <h4 style="font-size: 1.05rem; margin-bottom: 4px;">Sarah's 30th</h4>
                        <p style="color: var(--text-muted); font-size: 0.92rem;">"Best wish message ever. She cried!"</p>
                        <div style="margin-top: 16px; display: flex; gap: 6px;">
                            <i class="fas fa-heart" style="color: var(--coral);"></i>
                            <i class="fas fa-heart" style="color: var(--coral);"></i>
                            <i class="fas fa-heart" style="color: var(--coral);"></i>
                        </div>
                    </div>
                    <div class="floating-card card-2 glass-card">
                        <div class="mock-avatar">💍</div>
                        <h4 style="font-size: 1.05rem; margin-bottom: 4px;">Wedding Day</h4>
                        <p style="color: var(--text-muted); font-size: 0.92rem;">"Clever and so personal. Thank you!"</p>
                    </div>
                    <div class="floating-card card-3 glass-card" style="border: 2px solid var(--coral-border);">
                        <i class="fas fa-quote-left" style="color: var(--coral); font-size: 1.5rem; margin-bottom: 10px;"></i>
                        <p style="font-size: 1rem;">I scheduled a wish in 40 seconds. He thought I'd spent hours on it.</p>
                        <div style="margin-top: 14px; display: flex; align-items: center; gap: 10px;">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=64&h=64&fit=crop" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover;">
                            <span style="font-weight: 700; font-size: 0.92rem;">Maria</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Marquee — occasions -->
        <section class="marquee-section">
            <div class="marquee">
                <div class="marquee-content">
                    <span class="marquee-tag"><i class="fas fa-birthday-cake"></i> Birthday</span>
                    <span class="marquee-tag"><i class="fas fa-lightbulb"></i> Inspirational</span>
                    <span class="marquee-tag"><i class="fas fa-ring"></i> Wedding Vows</span>
                    <span class="marquee-tag"><i class="fas fa-dove"></i> Condolences</span>
                    <span class="marquee-tag"><i class="fas fa-hand-holding-medical"></i> Get Well Soon</span>
                    <span class="marquee-tag"><i class="fas fa-hands-clapping"></i> Thank You</span>
                    <span class="marquee-tag"><i class="fas fa-award"></i> Congratulations</span>
                    <span class="marquee-tag"><i class="fas fa-glass-cheers"></i> Wedding Wishes</span>
                    <span class="marquee-tag"><i class="fas fa-heart"></i> Anniversary</span>
                    <span class="marquee-tag"><i class="fas fa-baby-carriage"></i> New Baby</span>
                    <span class="marquee-tag"><i class="fas fa-graduation-cap"></i> Graduation</span>
                    <span class="marquee-tag"><i class="fas fa-umbrella-beach"></i> Retirement</span>
                    <span class="marquee-tag"><i class="fas fa-briefcase"></i> Promotion</span>
                    <span class="marquee-tag"><i class="fas fa-champagne-glasses"></i> New Year</span>
                    <span class="marquee-tag"><i class="fas fa-gifts"></i> Holiday</span>
                    <span class="marquee-tag"><i class="fas fa-cloud"></i> Thinking of You</span>
                    <span class="marquee-tag"><i class="fas fa-face-frown"></i> Apology</span>
                    <span class="marquee-tag"><i class="fas fa-kiss-wink-heart"></i> Romance</span>
                    <span class="marquee-tag"><i class="fas fa-user-group"></i> Friendship</span>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                <span class="badge" style="margin-bottom: 20px;"><i class="fa-regular fa-sparkles"></i> Why WISP</span>
                <h2 class="display-text" style="max-width: 760px;">Better than a <span class="text-gradient">belated apology</span></h2>
                <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 620px; margin-top: 16px;">Because truly forgetting someone's birthday hurts. WISP gives you everything you need to schedule, personalize, and ensure you always show up.</p>
            </div>
            <div class="feature-grid">
                <div class="glass-card" style="padding: 36px 26px;">
                    <div class="feature-icon"><i class="fas fa-brain"></i></div>
                    <h3 style="font-size: 1.4rem; margin-bottom: 12px;">AI whisper</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">Our writing engine sounds like you — only wittier, warmer, and never awkward.</p>
                </div>
                <div class="glass-card" style="padding: 36px 26px;">
                    <div class="feature-icon"><i class="fas fa-palette"></i></div>
                    <h3 style="font-size: 1.4rem; margin-bottom: 12px;">Cinematic designs</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">From minimalist to full celebration. Six signature template families.</p>
                </div>
                <div class="glass-card" style="padding: 36px 26px;">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <h3 style="font-size: 1.4rem; margin-bottom: 12px;">Scheduled delivery</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">Set the exact date and time. WISP delivers it — you don't have to remember to.</p>
                </div>
                <div class="glass-card" style="padding: 36px 26px;">
                    <div class="feature-icon"><i class="fas fa-music"></i></div>
                    <h3 style="font-size: 1.4rem; margin-bottom: 12px;">Add a soundtrack</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">Upload background music and photos — turn a message into a moment.</p>
                </div>
            </div>
        </section>

        <!-- STEPS -->
        <section id="steps" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-check"></i> Three steps to WISP</span>
                <h2 class="display-text" style="font-size: clamp(2.2rem, 4vw, 3rem);">From blank to brilliance</h2>
            </div>
            <div class="steps-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 980px; margin: 0 auto;">
                <div class="glass-card step-card" style="padding: 36px 30px; text-align: center;">
                    <span class="step-num">1</span>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Pick an occasion</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">Birthday, wedding, anniversary, or just because — 30+ categories to start from.</p>
                </div>
                <div class="glass-card step-card" style="padding: 36px 30px; text-align: center;">
                    <span class="step-num">2</span>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Customize it</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">Add a name, message, photo, or music. Let the AI help you polish the words.</p>
                </div>
                <div class="glass-card step-card" style="padding: 36px 30px; text-align: center;">
                    <span class="step-num">3</span>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Schedule &amp; share</h3>
                    <p style="color: var(--text-muted); line-height: 1.65;">Pick the exact delivery time, or send now via link or WhatsApp — done in seconds.</p>
                </div>
            </div>
        </section>

        <!-- VIBES -->
        <section id="vibes" class="container" style="padding: 6rem 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; margin-bottom: 40px;">
                <div>
                    <span class="badge" style="margin-bottom: 12px;"><i class="fas fa-palette"></i> Six signature styles</span>
                    <h2 class="display-text" style="font-size: clamp(2.2rem, 4vw, 2.8rem); margin-top: 8px;">Pick your vibe</h2>
                </div>
                <p style="max-width: 400px; color: var(--text-muted); font-size: 1.05rem;">From elegant and minimal to full celebration. Every template is fully responsive.</p>
            </div>
            <div class="vibes-grid">
                @php
                    $vibes = [
                        'view' => ['icon' => 'fa-eye', 'desc' => 'Classic layout, flexible views.'],
                        'aurora' => ['icon' => 'fa-sun', 'desc' => 'Soft gradients, elegant serif.'],
                        'casual' => ['icon' => 'fa-coffee', 'desc' => 'Relaxed, informal, easygoing.'],
                        'confetti' => ['icon' => 'fa-birthday-cake', 'desc' => 'Party energy, bold colors.'],
                        'minimal' => ['icon' => 'fa-feather', 'desc' => 'Clean lines, timeless feel.'],
                        'garden' => ['icon' => 'fa-leaf', 'desc' => 'Nature-inspired, soft tones.'],
                        'glitter' => ['icon' => 'fa-star', 'desc' => 'Shimmer, shine, celebration.'],
                        'romance' => ['icon' => 'fa-heart', 'desc' => 'Sweet, warm, and heartfelt.'],
                    ];
                @endphp
                @foreach($vibes as $key => $vibe)
                    @if(isset($themes[$key]))
                        <div class="glass-card {{ $key === 'aurora' ? 'vibe-card' : '' }}" style="padding: 32px; text-align: center;">
                            <div class="vibe-icon-box"><i class="fas {{ $vibe['icon'] }}" style="font-size: 1.9rem; color: var(--coral);"></i></div>
                            <h3 style="font-size: 1.25rem; margin-bottom: 8px;">{{ ucfirst($key) }}</h3>
                            <p style="color: var(--text-muted); margin-bottom: 16px;">{{ $vibe['desc'] }}</p>
                            <a href="{{ route('templates.gallery') }}" style="color: var(--coral-dark); font-weight: 700; text-decoration: none;">{{ $themes[$key] }} templates →</a>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>

        <!-- PRICING — new section -->
        <!-- <section id="pricing" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-tags"></i> Simple pricing</span>
                <h2 class="display-text" style="font-size: clamp(2.2rem, 4vw, 3rem);">Start free, upgrade when you're ready</h2>
                <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 560px; margin-top: 16px;">No subscriptions required to send a heartfelt wish. Pay only for the extras that matter to you.</p>
            </div>
            
             Promotional pricing banner 
            <div style="background: var(--coral-light); border: 1.5px solid var(--coral-border); border-radius: 16px; padding: 18px 24px; max-width: 900px; margin: 0 auto 40px; display: flex; align-items: center; justify-content: center; gap: 14px; text-align: center;">
                <i class="fas fa-gift" style="font-size: 1.6rem; color: var(--coral);"></i>
                <p style="margin: 0; color: var(--plum); font-weight: 600; font-size: 0.98rem; line-height: 1.5;">
                    🎉 <strong style="color: var(--coral-dark);">Special Beta Launch Offer:</strong> Plus and Occasions+ memberships are <span style="background: rgba(232, 103, 74, 0.15); padding: 2px 6px; border-radius: 4px;">100% Free throughout 2026 & 2027!</span> Billing will begin starting in 2028.
                </p>
            </div>

            <div class="pricing-grid">
                <div class="glass-card pricing-card">
                    <h3 style="font-size: 1.3rem;">Starter</h3>
                    <div class="price">Free</div>
                    <p style="color: var(--text-muted); font-size: 0.92rem;">Perfect for the occasional wish.</p>
                    <ul>
                        <li><i class="fas fa-check"></i> 2 free wishes per month</li>
                        <li><i class="fas fa-check"></i> Access to core templates</li>
                        <li><i class="fas fa-check"></i> Shareable links</li>
                        <li><i class="fas fa-check"></i> Basic scheduling</li>
                    </ul>
                    <a href="{{ route('guest.try') }}" class="btn btn-outline" style="justify-content: center;">Try it free</a>
                </div>
                <div class="glass-card pricing-card featured">
                    <span class="pricing-tag">Most popular</span>
                    <h3 style="font-size: 1.3rem;">Plus</h3>
                    <div class="price">$1<span style="font-size: 1rem; font-weight: 500;">/mo</span></div>
                    <p class="text-muted-inverse" style="font-size: 0.92rem;">For people who never want to miss a moment.</p>
                    <ul>
                        <li><i class="fas fa-check"></i> Unlimited wishes</li>
                        <li><i class="fas fa-check"></i> Full template library (200+)</li>
                        <li><i class="fas fa-check"></i> AI writing assistant</li>
                        <li><i class="fas fa-check"></i> Music &amp; photo attachments</li>
                        <li><i class="fas fa-check"></i> WhatsApp direct send</li>
                    </ul>
                    <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-primary" style="justify-content: center;">Get Plus</a>
                </div>
                <div class="glass-card pricing-card">
                    <h3 style="font-size: 1.3rem;">Occasions+</h3>
                    <div class="price">$11<span style="font-size: 1rem; font-weight: 500;">/yr</span></div>
                    <p style="color: var(--text-muted); font-size: 0.92rem;">For families and teams who plan ahead.</p>
                    <ul>
                        <li><i class="fas fa-check"></i> Everything in Plus</li>
                        <li><i class="fas fa-check"></i> Shared occasion calendar</li>
                        <li><i class="fas fa-check"></i> Recurring reminders</li>
                        <li><i class="fas fa-check"></i> Priority delivery support</li>
                    </ul>
                    <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-outline" style="justify-content: center;">Choose annual</a>
                </div>
            </div>
        </section> -->

        <!-- TEAM -->
        <section id="team" class="container" style="padding: 6rem 0;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 48px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-users"></i> The WISP team</span>
                <h2 class="display-text" style="font-size: clamp(2.2rem, 4vw, 3rem);">Brains behind the brilliance</h2>
            </div>
            <div class="team-grid" style="max-width: 850px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
                <!-- Member 1 -->
                <div class="glass-card" style="position: relative; overflow: hidden; padding: 40px 32px; text-align: center; border-radius: var(--radius-lg);">
                    <!-- Blurred Image Fill Overlay -->
                    <div style="position: absolute; inset: 0; background-image: url('{{ asset('img/owner.jpeg') }}'); background-size: cover; background-position: center; filter: blur(28px) scale(1.2); opacity: 0.35; pointer-events: none; z-index: 0;"></div>
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(255,255,255,0.72) 0%, rgba(255,255,255,0.92) 100%); pointer-events: none; z-index: 0;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <img src="{{ asset('img/owner.jpeg') }}" alt="Gilbert Asare" class="team-photo" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 8px 24px rgba(0,0,0,0.12); margin-bottom: 16px;">
                        <h3 style="font-size: 1.35rem; margin-bottom: 6px; font-weight: 700; color: var(--plum);">Gilbert Asare</h3>
                        <p style="color: var(--coral-dark); font-weight: 700; font-size: 0.88rem; text-transform: uppercase; letter-spacing: 0.06em; margin: 0;">CEO &amp; Founder</p>
                        
                        <div class="team-social-links" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 16px;">
                            <!-- Portfolio -->
                            <a href="#" target="_blank" title="Portfolio" class="team-social-icon" id="gilbertPortfolioLink">
                                <i class="fas fa-globe"></i>
                            </a>
                            <!-- LinkedIn -->
                            <a href="#" target="_blank" title="LinkedIn" class="team-social-icon" id="gilbertLinkedinLink">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <!-- GitHub -->
                            <a href="#" target="_blank" title="GitHub" class="team-social-icon" id="gilbertGithubLink">
                                <i class="fab fa-github"></i>
                            </a>
                            <!-- WhatsApp -->
                            <a href="#" target="_blank" title="WhatsApp" class="team-social-icon" id="gilbertWhatsappLink">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <!-- Instagram -->
                            <a href="#" target="_blank" title="Instagram" class="team-social-icon" id="gilbertInstagramLink">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Member 2 -->
                <div class="glass-card" style="position: relative; overflow: hidden; padding: 40px 32px; text-align: center; border-radius: var(--radius-lg);">
                    <!-- Blurred Image Fill Overlay -->
                    <div style="position: absolute; inset: 0; background-image: url('{{ asset('img/owner.1.jpeg') }}'); background-size: cover; background-position: center; filter: blur(28px) scale(1.2); opacity: 0.35; pointer-events: none; z-index: 0;"></div>
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(255,255,255,0.72) 0%, rgba(255,255,255,0.92) 100%); pointer-events: none; z-index: 0;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <img src="{{ asset('img/owner.1.jpeg') }}" alt="Co-Founder" class="team-photo" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 8px 24px rgba(0,0,0,0.12); margin-bottom: 16px;">
                        <h3 style="font-size: 1.35rem; margin-bottom: 6px; font-weight: 700; color: var(--plum);">Team Member</h3>
                        <p style="color: var(--coral-dark); font-weight: 700; font-size: 0.88rem; text-transform: uppercase; letter-spacing: 0.06em; margin: 0;">Co-Founder &amp; CTO</p>
                        
                        <div class="team-social-links" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 16px;">
                            <!-- Portfolio -->
                            <a href="#" target="_blank" title="Portfolio" class="team-social-icon">
                                <i class="fas fa-globe"></i>
                            </a>
                            <!-- LinkedIn -->
                            <a href="#" target="_blank" title="LinkedIn" class="team-social-icon">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <!-- GitHub -->
                            <a href="#" target="_blank" title="GitHub" class="team-social-icon">
                                <i class="fab fa-github"></i>
                            </a>
                            <!-- WhatsApp -->
                            <a href="#" target="_blank" title="WhatsApp" class="team-social-icon">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <!-- Instagram -->
                            <a href="#" target="_blank" title="Instagram" class="team-social-icon">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Try WISP -->
        <section id="try-wisp" class="container" style="padding: 2rem 0 5rem;">
            <div class="glass-card" style="padding: clamp(40px, 6vw, 80px) clamp(20px, 4vw, 60px); text-align: center; border-radius: var(--radius-lg); overflow: hidden; background: linear-gradient(150deg, var(--white), var(--cream-deep));">
                <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; background: var(--coral-light); filter: blur(60px); border-radius: 50%; z-index: 0;"></div>
                <div style="position: absolute; bottom: -50px; right: -50px; width: 150px; height: 150px; background: var(--plum-light); filter: blur(60px); border-radius: 50%; z-index: 0;"></div>

                <div style="position: relative; z-index: 1;">
                    <span class="badge" style="margin-bottom: 20px;"><i class="fas fa-wand-magic-sparkles"></i> Try WISP for free</span>
                    <h2 class="display-text" style="font-size: clamp(2rem, 5vw, 3.2rem); margin-bottom: 16px;">Experience the <span class="text-gradient">magic</span></h2>
                    <p style="color: var(--text-muted); font-size: clamp(1.02rem, 2vw, 1.15rem); max-width: 600px; margin: 0 auto 32px; line-height: 1.65;">Create a stunning wish message in seconds, without signing up. Try it out and see how easy it is to make someone smile.</p>

                    <a href="{{ route('guest.try') }}" class="btn btn-primary btn-large">
                        Create a free message <i class="fas fa-arrow-right"></i>
                    </a>

                    <div style="margin-top: 22px; color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">
                        No credit card required. Free up to 2 messages.
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ & Testimonials -->
        <section id="faq" class="container" style="padding: 3rem 0 6rem;">
            <div style="margin-bottom: 4rem;">
                <span class="badge"><i class="fas fa-question-circle"></i> FAQ</span>
                <h2 class="display-text" style="font-size: clamp(2rem, 4vw, 2.6rem); margin: 18px 0 24px;">Questions about <span class="text-gradient">WISP</span>?</h2>
                <div class="faq-grid">
                    <div class="faq-block glass-card">
                        <div class="faq-item">
                            <span style="font-weight: 700;">Is WISP really free?</span>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">Yes! WISP is free to start. Create wishes, use templates, and share links at no cost. We offer premium features for people who want more.</div>
                    </div>
                    <div class="faq-block glass-card">
                        <div class="faq-item">
                            <span style="font-weight: 700;">Can I use my own photos?</span>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">Absolutely. Upload your own photos to personalize any template — a recipient photo, background image, or your own pictures.</div>
                    </div>
                    <div class="faq-block glass-card">
                        <div class="faq-item">
                            <span style="font-weight: 700;">Do wishes expire?</span>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">You choose the expiry. Set a custom duration (24 hours, 7 days, or longer) or leave it open. Expired links stop working so your message stays special.</div>
                    </div>
                    <div class="faq-block glass-card">
                        <div class="faq-item">
                            <span style="font-weight: 700;">How do I share a wish?</span>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">Copy the unique link we generate for each wish. Share it via text, email, or social — or send it directly via WhatsApp with one tap.</div>
                    </div>
                    <div class="faq-block glass-card">
                        <div class="faq-item">
                            <span style="font-weight: 700;">Can I schedule a wish in advance?</span>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">Yes — set the exact date and time you want it delivered. Plan a whole year of birthdays and anniversaries ahead of time if you like.</div>
                    </div>
                    <div class="faq-block glass-card">
                        <div class="faq-item">
                            <span style="font-weight: 700;">Who built WISP?</span>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">WISP was built by Gilbert Asare. Our mission is to make it easy for everyone to send wishes that feel genuine and look stunning.</div>
                    </div>
                </div>
            </div>

            <span class="badge"><i class="fas fa-star"></i> Loved by many</span>
            <h2 class="display-text" style="font-size: clamp(2rem, 4vw, 2.6rem); margin: 18px 0 30px;">What people say</h2>
            <div class="testimonial-grid">
                <div class="glass-card testimonial-card">
                    <div>
                        <i class="fas fa-quote-right" style="color: var(--coral); font-size: 2rem; opacity: 0.3; margin-bottom: 18px; display: block;"></i>
                        <p style="font-size: 1.15rem; margin-bottom: 24px; line-height: 1.6; font-weight: 500;">"I sent a wish to my sister in New York. She thought I'd hired a poet."</p>
                    </div>
                    <div style="display: flex; gap: 14px; align-items: center;">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=64&h=64&fit=crop" class="testimonial-avatar">
                        <div><strong style="font-size: 1rem;">Ben Carter</strong><span style="color: var(--text-muted); display: block; font-size: 0.88rem;">Brother of the year</span></div>
                    </div>
                </div>
                <div class="glass-card testimonial-card">
                    <div>
                        <i class="fas fa-quote-right" style="color: var(--plum); font-size: 2rem; opacity: 0.3; margin-bottom: 18px; display: block;"></i>
                        <p style="font-size: 1.15rem; margin-bottom: 24px; line-height: 1.6; font-weight: 500;">"The templates are so gorgeous I almost didn't send them. Almost."</p>
                    </div>
                    <div style="display: flex; gap: 14px; align-items: center;">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=64&h=64&fit=crop" class="testimonial-avatar">
                        <div><strong style="font-size: 1rem;">Sophia Lee</strong><span style="color: var(--text-muted); display: block; font-size: 0.88rem;">Design critic</span></div>
                    </div>
                </div>
                <div class="glass-card testimonial-card">
                    <div>
                        <i class="fas fa-quote-right" style="color: var(--coral-dark); font-size: 2rem; opacity: 0.3; margin-bottom: 18px; display: block;"></i>
                        <p style="font-size: 1.15rem; margin-bottom: 24px; line-height: 1.6; font-weight: 500;">"WISP makes scheduling wishes absolute magic. Super clean and fast!"</p>
                    </div>
                    <div style="display: flex; gap: 14px; align-items: center;">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=64&h=64&fit=crop" class="testimonial-avatar">
                        <div><strong style="font-size: 1rem;">Lawrence</strong><span style="color: var(--text-muted); display: block; font-size: 0.88rem;">Early adopter</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="container" style="padding-bottom: 4rem;">
            <div class="cta-panel">
                <h2 style="font-size: clamp(1.8rem, 4vw, 2.4rem); margin-bottom: 12px;">Never miss a wish</h2>
                <p style="font-size: 1.05rem; margin-bottom: 30px;">Get new templates, product updates, and WISP news in your inbox.</p>

                @if(session('success'))
                    <div style="background: rgba(255,255,255,0.12); color: var(--cream); padding: 12px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('info'))
                    <div style="background: rgba(255,255,255,0.12); color: var(--cream); padding: 12px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
                        {{ session('info') }}
                    </div>
                @endif

                <form action="{{ route('subscribe') }}" method="POST" style="display: flex; gap: 12px; max-width: 480px; margin: 0 auto; flex-flow: row wrap; justify-content: center; align-items: center;">
                    @csrf
                    <input type="email" name="email" placeholder="Your email" required class="newsletter-input">
                    <button type="submit" class="btn btn-primary" style="border-radius: 50px;">Subscribe</button>
                </form>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="container" style="padding-bottom: 4rem;">
            <div class="glass-card" style="padding: clamp(40px, 6vw, 80px) clamp(20px, 4vw, 40px); text-align: center; border-radius: var(--radius-lg); background: linear-gradient(150deg, var(--cream-deep), var(--white));">
                <img src="{{ asset('img/logo.png') }}" alt="WISP" style="width: clamp(44px, 6vw, 60px); height: auto; margin-bottom: 18px; object-fit: contain;">
                <h2 class="display-text" style="font-size: clamp(1.9rem, 6vw, 3rem); margin-bottom: 16px;">Ready to send wishes with <span class="text-gradient">WISP</span>?</h2>
                <p style="font-size: clamp(1rem, 3vw, 1.25rem); color: var(--text-muted); max-width: 580px; margin: 0 auto 26px;">Join thousands who never miss a moment. Create your first wish in under a minute.</p>
                <a href="{{ route('auth.login') }}?tab=signup" class="btn btn-primary btn-large"><i class="fas fa-wand-magic-sparkles"></i> Start with WISP</a>
                <div style="margin-top: 26px; color: var(--text-muted); font-weight: 600;"><i class="fas fa-shield-alt"></i> No credit card. Free to start.</div>
            </div>
        </section>
    </main>

    @include('welcome.components.footer')

    <script>
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.style.background = 'rgba(253,246,236,0.94)';
                nav.style.backdropFilter = 'blur(22px)';
                nav.style.borderBottom = '1px solid rgba(43,31,61,0.12)';
            } else {
                nav.style.background = 'rgba(253,246,236,0.82)';
                nav.style.borderBottom = '1px solid var(--glass-border)';
            }
        });

        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const block = item.closest('.faq-block');
                const plus = item.querySelector('.faq-plus');
                block.classList.toggle('active');
                plus.textContent = block.classList.contains('active') ? '−' : '+';
            });
        });

        const scrollBox = document.querySelector('.testimonial-scroll');
        const btns = document.querySelectorAll('.testimonial-buttons .fa-arrow-left, .testimonial-buttons .fa-arrow-right');
        if (btns.length >= 2 && scrollBox) {
            btns[0].parentElement.addEventListener('click', () => scrollBox.scrollBy({ left: -420, behavior: 'smooth' }));
            btns[1].parentElement.addEventListener('click', () => scrollBox.scrollBy({ left: 420, behavior: 'smooth' }));
        }

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
        if (navClose) navClose.addEventListener('click', closeNav);
        if (navOverlay) navOverlay.addEventListener('click', closeNav);
        if (mobileNav) {
            mobileNav.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', closeNav);
            });
        }

        // Hero visual cards cycling
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

                heroCards[clickedIndex].style.boxShadow = '0 30px 60px rgba(232,103,74,0.28)';
                setTimeout(() => {
                    heroCards[clickedIndex].style.boxShadow = '';
                }, 500);

                const sorted = [...zIndices].sort((a, b) => a - b);
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
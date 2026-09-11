<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>FAQ & Contact — WISP</title>
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
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 40px;">
                <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-question-circle"></i> FAQ</span>
                <h1 class="display-text">Frequently Asked <span class="text-gradient">Questions</span></h1>
                <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 600px; margin-top: 16px;">Clear and simple answers to help you get the most out of WISP.</p>
            </div>

            <!-- Category filters -->
            <div class="roadmap-filters" id="faqFilters">
                <button class="template-tab active" data-filter="all">All questions</button>
                <button class="template-tab" data-filter="General">General</button>
                <button class="template-tab" data-filter="Billing">Billing &amp; Plans</button>
                <button class="template-tab" data-filter="Delivery">Delivery &amp; Scheduling</button>
                <button class="template-tab" data-filter="Privacy">Privacy &amp; Security</button>
                <button class="template-tab" data-filter="Sharing">Sharing</button>
            </div>

            @php
                $faqs = [
                    ['category' => 'General', 'q' => 'Why was WISP created?',
                        'a' => 'Our founder, Gilbert, built WISP after truly forgetting a friend\'s birthday and having to send a belated apology letter. He realized there needed to be a system to schedule and automate every occasion so you never miss a moment.'],
                    ['category' => 'General', 'q' => 'What is WISP?',
                        'a' => 'WISP is a digital keepsake and scheduled messaging platform born out of that very problem. It allows users to write custom AI-powered greeting cards, add photos and music, set custom release dates, and ensure they are delivered exactly on time.'],
                    ['category' => 'General', 'q' => 'Do I need to create an account to use WISP?',
                        'a' => 'You can try WISP without an account and create up to two trial messages. Creating a free account unlocks unlimited messages, editing, and the ability to manage everything you\'ve sent from one place.'],
                    ['category' => 'General', 'q' => 'What occasions is WISP good for?',
                        'a' => 'Birthdays, anniversaries, weddings, condolences, thank-yous, and any moment where a quick text just wouldn\'t feel like enough.'],
                    ['category' => 'Billing', 'q' => 'Is WISP free to use?',
                        'a' => 'Yes, WISP is free to start. Free users can write messages, use basic templates, and send links. Premium plans are available to unlock special templates, remove watermarks, and use audio attachments.'],
                    ['category' => 'Billing', 'q' => 'What do I get with a Premium plan?',
                        'a' => 'Premium removes watermarks, unlocks custom fonts and audio uploads, and gives you access to our full library of premium layouts as they\'re released.'],
                    ['category' => 'Billing', 'q' => 'Can I cancel my subscription anytime?',
                        'a' => 'Yes. You can cancel from your account settings at any time, and you\'ll keep Premium access until the end of your current billing period.'],
                    ['category' => 'Delivery', 'q' => 'How does scheduled delivery work?',
                        'a' => 'When creating a card, you can set a target delivery date. WISP will lock the card and automatically deliver or notify the recipient via the chosen channel (Email or WhatsApp link) exactly on that day.'],
                    ['category' => 'Delivery', 'q' => 'Can I edit a message after it\'s scheduled?',
                        'a' => 'Yes, you can update the content, media, or delivery date of a scheduled message any time before it\'s released to your recipient.'],
                    ['category' => 'Delivery', 'q' => 'What happens if I miss the delivery date?',
                        'a' => 'Nothing is lost — the message simply becomes available as soon as you\'re ready to send it, and you can update the date to any point in the future.'],
                    ['category' => 'Privacy', 'q' => 'Are my wishes secure?',
                        'a' => 'Absolutely. WISP offers security and privacy configurations. You can set access passcodes/PINs on specific cards so that only the designated recipient with the correct passcode can read the message.'],
                    ['category' => 'Privacy', 'q' => 'Can anyone else see my messages?',
                        'a' => 'Private messages are only accessible via your direct link and, if enabled, a passcode. WISP never shares your message content publicly or with other users.'],
                    ['category' => 'Sharing', 'q' => 'How do I share a message?',
                        'a' => 'Once your wish page is generated, you will get a unique link. You can easily copy and paste this link anywhere (WhatsApp, SMS, Telegram) or send it directly.'],
                    ['category' => 'Sharing', 'q' => 'Can more than one recipient view the same wish?',
                        'a' => 'Yes — anyone with the link (and passcode, if you set one) can view it, so it works well for group celebrations as well as one-to-one wishes.'],
                ];
            @endphp

            <div class="faq-grid" style="max-width: 900px; margin: 0 auto 90px;">
                @foreach ($faqs as $faq)
                    <div class="faq-block" data-category="{{ $faq['category'] }}">
                        <div class="faq-item" onclick="toggleFaq(this)">
                            <h3 style="font-size: 1.08rem; margin: 0;">{{ $faq['q'] }}</h3>
                            <div class="faq-plus">+</div>
                        </div>
                        <div class="faq-answer">{{ $faq['a'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Support channels -->
            <div style="margin-bottom: 80px;">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 40px;">
                    <span class="badge" style="margin-bottom: 16px;"><i class="fas fa-headset"></i> Support</span>
                    <h2 class="display-text" style="font-size: clamp(2rem, 4vw, 2.7rem);">Get in touch with us</h2>
                    <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 560px; margin-top: 12px;">Have questions, suggestions, or technical concerns? Here's how to reach us.</p>
                </div>

                <div class="feature-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); max-width: 900px; margin: 48px auto 0;">
                    <div class="glass-card" style="padding: 32px; text-align: center;">
                        <div class="feature-icon" style="margin: 0 auto 18px;"><i class="fas fa-paper-plane"></i></div>
                        <h3 style="font-size: 1.15rem; margin-bottom: 10px;">Email Support</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0 0 16px; font-size: 0.92rem;">For general inquiries, account help, or developer partnerships.</p>
                        <a href="mailto:gai.dev.official@gmail.com" style="font-weight: 700; color: var(--coral-dark); text-decoration: none;">gai.dev.official@gmail.com</a>
                    </div>
                    <div class="glass-card" style="padding: 32px; text-align: center;">
                        <div class="feature-icon" style="margin: 0 auto 18px;"><i class="fas fa-comment-dots"></i></div>
                        <h3 style="font-size: 1.15rem; margin-bottom: 10px;">Feedback &amp; Suggestions</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0 0 18px; font-size: 0.92rem;">We build our roadmap around what our users actually ask for.</p>
                        <a href="https://docs.google.com/forms/d/e/1FAIpQLSdieicn8HJt1KEgB6kuO7PeInpebNCCHiuFjuBSRzjvc1lBGg/viewform" target="_blank" class="btn btn-primary" style="padding: 10px 22px; font-size: 0.88rem;"><i class="fas fa-external-link-alt"></i> Open Feedback Form</a>
                    </div>
                    <div class="glass-card" style="padding: 32px; text-align: center;">
                        <div class="feature-icon" style="margin: 0 auto 18px;"><i class="fas fa-route"></i></div>
                        <h3 style="font-size: 1.15rem; margin-bottom: 10px;">See What's Coming</h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin: 0 0 18px; font-size: 0.92rem;">Curious what we're building next? Check the full feature roadmap.</p>
                        <a href="{{ url('/features') }}" class="btn btn-outline" style="padding: 10px 22px; font-size: 0.88rem;"><i class="fas fa-magic"></i> View Roadmap</a>
                    </div>
                </div>
            </div>

            <!-- Still need help CTA -->
            <div class="cta-panel" style="margin-bottom: 60px;">
                <i class="fas fa-life-ring" style="font-size: 1.8rem; color: var(--coral); margin-bottom: 16px;"></i>
                <h2 style="font-size: clamp(1.6rem, 4vw, 2.2rem); margin-bottom: 12px;">Still couldn't find an answer?</h2>
                <p style="max-width: 520px; margin: 0 auto 26px; line-height: 1.6;">Reach out directly and a real person on our small team will get back to you.</p>
                <a href="mailto:gai.dev.official@gmail.com" class="btn btn-primary btn-large"><i class="fas fa-paper-plane"></i> Email the team</a>
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
        // FAQ toggle script
        function toggleFaq(element) {
            const block = element.parentElement;
            block.classList.toggle('active');
        }

        // FAQ category filters
        const faqFilterButtons = document.querySelectorAll('#faqFilters .template-tab');
        const faqBlocks = document.querySelectorAll('.faq-block');
        faqFilterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                faqFilterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                faqBlocks.forEach(block => {
                    const match = filter === 'all' || block.dataset.category === filter;
                    block.classList.toggle('hidden-by-filter', !match);
                    if (!match) block.classList.remove('active');
                });
            });
        });

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
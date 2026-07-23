<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #d4af37; /* Sophisticated Gold */
            --glow: rgba(167, 139, 250, 0.4);
            --bg-dark: #0f172a;
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --text-light: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: var(--bg-dark);
            background-image: radial-gradient(circle at 50% 50%, #1e293b 0%, #0f172a 100%);
            font-family: 'Inter', sans-serif;
            color: var(--text-light);
            overflow-x: hidden; overflow-y: auto;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 480px;
            perspective: 1000px;
        }

        /* --- Header/Title --- */
        .hero-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin: 0;
            background: linear-gradient(to bottom, #fff 30%, #a78bfa 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-style: italic;
        }

        /* --- Glass Card --- */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: transform 0.4s ease, border-color 0.4s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            border-color: rgba(167, 139, 250, 0.4);
        }

        /* --- Profile Section --- */
        .profile-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            margin-bottom: 15px;
            box-shadow: 0 0 20px var(--glow);
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--bg-dark);
        }

        .recipient-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #fff;
        }

        /* --- Message Content --- */
        .message-body {
            text-align: center;
            line-height: 1.7;
            font-size: 1.05rem;
            color: #cbd5e1;
        }

        .greeting {
            display: block;
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .last-note {
            display: block;
            margin-top: 25px;
            font-style: italic;
            color: #a78bfa;
            font-weight: 600;
        }

        /* --- Floating Audio --- */
        .audio-pill {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 100;
        }

        #toggleAudio {
            background: var(--accent);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: var(--bg-dark);
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s;
        }

        #toggleAudio:active { transform: scale(0.9); }

        .overlay { position: fixed; inset: 0; background: black; z-index: 9999; display: none; }

        @media (max-width: 480px) {
            h1 { font-size: 2.5rem; }
            .glass-card { padding: 30px 20px; }
            .audio-pill { bottom: 20px; right: 20px; }
        }
    
        /* Universal Mobile Responsiveness */
        @media (max-width: 768px) {
            h1 { font-size: clamp(2rem, 8vw, 3rem) !important; margin: 10px 0 !important; }
            h2, .greeting-text { font-size: clamp(1.5rem, 6vw, 2.2rem) !important; }
            p, .message-body, .description { font-size: 1rem !important; line-height: 1.5 !important; }
            body { padding: 10px !important; min-height: 100vh !important; overflow-y: auto !important; }
            .container { flex-direction: column !important; padding: 0 !important; }
            .content-section, .visual-section { padding: 20px !important; width: 100% !important; border: none !important; }
            .card { width: 100% !important; padding: 15px !important; max-height: none !important; margin-top: 10px !important; }
            .hero-img-wrapper, .logo { width: min(150px, 40vw) !important; height: min(150px, 40vw) !important; }
            #audioPlayer { bottom: 15px !important; right: 15px !important; padding: 8px !important; }
        }

        @media (max-width: 480px) {
            h1 { font-size: clamp(1.6rem, 6vw, 2rem) !important; }
            .card { padding: 10px !important; border-radius: 16px !important; }
            .content-section { padding: 15px 10px !important;  overflow-y: auto; max-height: 100vh;  justify-content: flex-start !important; padding-top: 40px !important; }
        }
            /* Global Mobile Scroll Fix */
        @media (max-width: 1024px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
            }
                        .visual-anchor, .hero-img-wrapper, .left-panel, .image-portal, .image-section {
                height: auto !important;
                min-height: 40vh;
                position: relative !important;
            }
            .viewport, .container, .wrapper, .main-container {
                display: flex !important;
                flex-direction: column !important;
            }
            .viewport, .container, .wrapper, .main-container {
                height: auto !important;
                min-height: 100vh;
                overflow: visible !important;
            }
            .content-hub, .message-panel, .message-box, .letter-box, .content-area, .text-panel, .message-wrapper, .message-container, .content-section, .text-section, .message-content, .main-content, .card {
                max-height: none !important;
                overflow-y: visible !important;
                height: auto !important;
            }
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>
    <div class="overlay" id="overlay"></div>

    <div class="container">
        <header class="hero-title">
            <h1 id="mainTitle">{{ $message->title }}</h1>
        </header>

        <main class="glass-card">
            <div class="profile-wrapper">
                <div class="avatar-circle">
                    @if(isset($mediaFiles) && $mediaFiles->recipient_image)
                        <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="Recipient">
                    @else
                        <div style="background: #334155; width: 100%; height: 100%; border-radius: 50%;"></div>
                    @endif
                </div>
                <div class="recipient-name">
                    {{ $message->recipient_special_name ?? $message->recipient_name }}
                </div>
            </div>

            <div class="message-body">
                <span class="greeting">{{ $message->greeting }}</span>
                <div class="main-text">
                    {!! $message->message_display !!}
                </div>
                <span class="last-note">{{ $message->last_note }}</span>
            </div>
        </main>
    </div>

        <x-background-music :mediaFiles="$mediaFiles">
        <div class="audio-pill">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Smooth Particles Configuration
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#a78bfa" },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: true, distance: 150, color: "#a78bfa", opacity: 0.2, width: 1 },
                move: { enable: true, speed: 1, direction: "top", random: true, out_mode: "out" }
            },
            interactivity: {
                events: { onhover: { enable: true, mode: "bubble" } },
                modes: { bubble: { size: 4, opacity: 0.6 } }
            }
        });

        // Privacy & Security
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'block';
                alert('Privacy Protection: Screenshots are disabled.');
                setTimeout(() => { document.getElementById('overlay').style.display = 'none'; }, 2000);
            }
        });
    </script>
</body>
</html>




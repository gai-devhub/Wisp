<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #a78bfa;
            --secondary: #c4b5fd;
            --bg: #1a0a2e;
            --card-bg: rgba(255, 255, 255, 0.05);
            /* Darker glass */
            --content-bg: rgba(255, 255, 255, 0.95);
            --new: #7c3aed;
            --text: #1e1b4b;
        }

        body {
            background: var(--bg);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden; overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Cinematic Background Glows */
        .aura {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.4;
            animation: moveAura 20s infinite alternate;
        }

        @keyframes moveAura {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(100px, 50px);
            }
        }

        /* The Main Structure */
        .wrapper {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            animation: fadeIn 1.2s ease-out;
        }

        /* Top Title Bar */
        .head-banner {
            text-align: left;
            padding-left: 20px;
            border-left: 4px solid var(--primary);
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            margin: 0;
            background: linear-gradient(to right, #fff, var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Main Content Layout (Horizontal on Desktop) */
        .main-panel {
            background: var(--content-bg);
            border-radius: 24px;
            display: grid;
            grid-template-columns: 300px 1fr;
            overflow-y: auto; overflow-x: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-height: 70vh;
        }

        /* Sidebar with Image */
        .sidebar {
            background: linear-gradient(135deg, var(--new), var(--bg));
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-box {
            position: relative;
            margin-bottom: 20px;
        }

        .logo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .recipient-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin-top: 15px;
        }

        /* Message Area */
        .message-area {
            padding: 50px;
            color: var(--text);
            overflow-y: auto;
            text-align: left;
            background: #fff;
        }

        .greeting {
            font-size: 2rem;
            font-family: 'Cormorant Garamond', serif;
            color: var(--new);
            display: block;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--secondary);
            padding-bottom: 10px;
        }

        .description {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #444;
        }

        .footer-note {
            margin-top: 30px;
            display: block;
            font-weight: 600;
            color: var(--new);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        /* Floating Audio Control */
        #audioPlayer {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 15px;
            border-radius: 100px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        }

        #toggleAudio {
            background: var(--new);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 850px) {
            .main-panel {
                grid-template-columns: 1fr;
                max-height: 80vh;
            }

            .sidebar {
                padding: 20px;
            }

            .logo {
                width: 100px;
                height: 100px;
            }

            .message-area {
                padding: 30px;
            }

            h1 {
                font-size: 2.2rem;
            }
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 9999;
            display: none;
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
            .content-section { padding: 15px 10px !important; }
        }
    </style>
</head>

<body>
    <div id="particles-js"></div>
    <div class="aura" style="width: 400px; height: 400px; top: -100px; right: -100px; background: var(--new);"></div>
    <div class="aura"
        style="width: 300px; height: 300px; bottom: -50px; left: -50px; background: var(--primary); animation-delay: -5s;">
    </div>

    <div class="overlay" id="overlay"></div>
        <x-background-music :mediaFiles="$mediaFiles">
        <div id="audioPlayer">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <div class="wrapper">
        <div class="head-banner">
            <h1>{{ $message->title }}</h1>
        </div>

        <div class="main-panel">
            <div class="sidebar">
                <div class="profile-box">
                    <div class="logo">
                        @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                            <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="Profile">
                        @endif
                    </div>
                </div>
                <div class="recipient-name">
                    {{ $message->recipient_special_name ?? $message->recipient_name }}
                </div>
            </div>

            <div class="message-area">
                <strong class="greeting">{{ $message->greeting }}</strong>
                <div class="description">
                    {!! $message->message_display !!}
                </div>
                <span class="footer-note">{{ $message->last_note }}</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 40, density: { enable: true, value_area: 800 } },
                color: { value: "#c4b5fd" },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 0.5, direction: "top", random: true, out_mode: "out" }
            }
        });

        // Security
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'block';
                alert('Screenshots disabled for privacy.');
                setTimeout(() => document.getElementById('overlay').style.display = 'none', 1000);
            }
        });
    </script>
</body>

</html>
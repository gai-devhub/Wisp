<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,600&family=Montserrat:wght@200;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #a78bfa;
            --secondary: #c4b5fd;
            --bg: #0a0518;
            --accent: #7c3aed;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body {
            background: var(--bg);
            background: radial-gradient(circle at 50% 50%, #1e104b 0%, #0a0518 100%);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden; overflow-y: auto;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Shifting Nebula Effect */
        .nebula {
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: url('https://www.transparenttextures.com/patterns/stardust.png');
            opacity: 0.3;
            z-index: 2;
        }

        .container {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 500px;
            perspective: 1000px;
        }

        /* Floating Orbital Image */
        .orbital-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 40px;
        }

        .orbital-ring {
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border: 2px solid var(--primary);
            border-radius: 50%;
            border-left-color: transparent;
            border-bottom-color: transparent;
            animation: rotate 4s linear infinite;
        }

        .orbital-ring.slow {
            animation: rotate 8s linear infinite reverse;
            border-color: var(--secondary);
            opacity: 0.3;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
        }

        .recipient-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.5);
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* The Card */
        .card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: slideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) rotateX(-10deg);
            }

            to {
                opacity: 1;
                transform: translateY(0) rotateX(0deg);
            }
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            margin: 0 0 10px;
            background: linear-gradient(to right, #fff, var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 10px 20px rgba(167, 139, 250, 0.2);
        }

        .special-name {
            font-size: 1rem;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 30px;
            display: block;
            font-weight: 600;
        }

        .description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 200;
        }

        .description b,
        .description strong {
            color: #fff;
            font-weight: 600;
            font-style: italic;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
        }

        /* Player Bar */
        .player-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 10px 25px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 100;
        }

        #toggleAudio {
            background: var(--primary);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            font-size: 1.2rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #toggleAudio:hover {
            background: var(--accent);
            transform: scale(1.1);
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2.2rem;
            }

            .card {
                padding: 30px 20px;
            }

            .orbital-wrapper {
                width: 140px;
                height: 140px;
            }
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
    <div class="nebula"></div>

    <div class="container">
        <div class="orbital-wrapper">
            <div class="orbital-ring"></div>
            <div class="orbital-ring slow"></div>
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" class="recipient-img" alt="Hero">
            @endif
        </div>

        <div class="card">
            <h1>{{ $message->title }}</h1>
            <span class="special-name">{{ $message->recipient_special_name ?? $message->recipient_name }}</span>

            <div class="description">
                <strong>{{ $message->greeting }}</strong><br><br>
                {!! $message->message_display !!}
                <br><br>
                <b>{{ $message->last_note }}</b>
            </div>
        </div>
    </div>

        <x-background-music :mediaFiles="$mediaFiles">
        <div class="player-bar">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#c4b5fd" },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 0.5, direction: "top", out_mode: "out" }
            }
        });
    </script>
</body>

</html>




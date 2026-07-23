<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Quicksand:wght@300;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --white: #ffffff;
            --text: #2c3e50;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #fdfdfd;
            font-family: 'Quicksand', sans-serif;
            color: var(--text);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Animated Canvas Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: radial-gradient(circle at center, #ffffff 0%, #f0f0f0 100%);
        }

        /* Main Viewport */
        .viewport {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px;
            width: 100%;
            max-width: 800px;
        }

        .symbol {
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
            color: var(--gold);
            letter-spacing: 15px;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInDown 2s forwards 0.5s;
        }

        h1 {
            font-family: 'Cinzel', serif;
            font-size: 3.5rem;
            font-weight: 400;
            margin-bottom: 40px;
            background: linear-gradient(to bottom, #2c3e50, #d4af37);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0;
            animation: fadeIn 2s forwards 1s;
        }

        .message-container {
            font-size: 1.4rem;
            line-height: 2;
            font-weight: 300;
            min-height: 200px;
         overflow-y: auto !important; max-height: 100vh !important; justify-content: flex-start !important; padding-top: 40px !important; }

        #typed-message {
            display: inline;
        }

        .cursor {
            display: inline-block;
            width: 2px;
            height: 1.4rem;
            background: var(--gold);
            margin-left: 5px;
            animation: blink 1s infinite;
        }

        /* Portrait Accent */
        .portrait-circle {
            width: 120px;
            height: 120px;
            margin: 40px auto;
            border-radius: 50%;
            padding: 5px;
            border: 1px solid var(--gold);
            opacity: 0;
            animation: scaleIn 1.5s forwards 2s;
        }

        .portrait-circle img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            filter: grayscale(100%) brightness(1.1);
            transition: 0.5s;
        }

        .portrait-circle:hover img {
            filter: grayscale(0%);
        }

        /* Audio Control — centered under portrait */
        .audio-wrapper {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        #play-btn {
            background: none;
            border: 1px solid #ddd;
            padding: 10px 25px;
            border-radius: 30px;
            font-family: 'Cinzel', serif;
            font-size: 0.7rem;
            letter-spacing: 3px;
            cursor: pointer;
            transition: all 0.4s;
        }

        #play-btn:hover {
            border-color: var(--gold);
            color: var(--gold);
            letter-spacing: 5px;
        }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
        @keyframes blink { 50% { opacity: 0; } }

        @media (max-width: 600px) {
            h1 { font-size: 2rem; }
            .message-container { font-size: 1.1rem; }
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
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <div class="viewport">
        <div class="symbol">{{ $message->greeting ?? 'Hello' }}</div>
        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        
        <div class="message-container">
            <span id="typed-message"></span><span class="cursor"></span>
        </div>

        <div class="portrait-circle">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>

        <x-background-music :mediaFiles="$mediaFiles">
            <div class="audio-wrapper">
                <button type="button" id="play-btn" data-idle-label="Play">Play</button>
            </div>
        </x-background-music>
    </div>

    @php
        $typewriterText = trim(strip_tags($message->message_display ?? $message->message ?? ''));
    @endphp
    <div id="typewriter-data" data-message='@json($typewriterText)' hidden aria-hidden="true"></div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Cinematic Particle Setup
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#d4af37" },
                shape: { type: "circle" },
                opacity: { value: 0.1, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: true, distance: 150, color: "#d4af37", opacity: 0.05, width: 1 },
                move: { enable: true, speed: 0.5, direction: "top", random: true }
            }
        });

        // Typewriter Logic
        (function () {
            const dataEl = document.getElementById('typewriter-data');
            const target = document.getElementById('typed-message');
            if (!dataEl || !target) return;

            let message = '';
            try {
                message = JSON.parse(dataEl.getAttribute('data-message') || '""');
            } catch (e) {
                message = dataEl.getAttribute('data-message') || '';
            }
            if (!message) return;

            let i = 0;
            const speed = 50;

            function typeWriter() {
                if (i < message.length) {
                    target.textContent += message.charAt(i);
                    i++;
                    setTimeout(typeWriter, speed);
                }
            }

            setTimeout(typeWriter, 3000);
        })();
    </script>
</body>
</html>


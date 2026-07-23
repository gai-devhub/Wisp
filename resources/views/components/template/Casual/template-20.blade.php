<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@1,300;1,600&family=Montserrat:wght@200;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-space: #050508;
            --aurora-light: #eeeff9;
            --gold-leaf: #af944d;
        }

        body, html {
            margin: 0; padding: 0;
            height: 100%; width: 100%;
            background: var(--deep-space);
            font-family: 'Montserrat', sans-serif;
            color: var(--aurora-light);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* The Animated Stardust Layer */
        #star-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .glass-note {
            position: relative;
            z-index: 10;
            width: 80%;
            max-width: 600px;
            padding: 60px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .meta-ref {
            font-size: 0.6rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--gold-leaf);
            margin-bottom: 40px;
            display: block;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-weight: 300;
            font-style: italic;
            margin: 0 0 30px 0;
            line-height: 1;
        }

        .quote {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            line-height: 2.2;
            font-style: italic;
            opacity: 0.8;
            margin-bottom: 40px;
        }

        .sig {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            color: var(--gold-leaf);
        }

        /* Minimal Audio Bar */
        #audio-bar {
            position: fixed;
            bottom: 40px;
            right: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 100;
        }

        .play-btn {
            background: none;
            border: 1px solid var(--gold-leaf);
            color: var(--gold-leaf);
            width: 45px; height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.8rem;
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

    <canvas id="star-canvas"></canvas>

    <div class="glass-note">
        <span class="meta-ref">{{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }} // Celestial Bond</span>
        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        <div class="quote">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
        </div>
        @auth
            <div class="sig">{{ auth()->user()->name }}</div>
        @endauth
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <div id="audio-bar">
            <span style="font-size: 0.5rem; letter-spacing: 3px; color: #555;">AMBIENT SYMPHONY</span>
            <button type="button" class="play-btn" data-idle-label="&#9835;">&#9835;</button>
        </div>
    </x-background-music>

    <script>
        // Simple Dynamic Particle Engine
        const canvas = document.getElementById('star-canvas');
        const ctx = canvas.getContext('2d');
        let stars = [];

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        class Star {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 1.5;
                this.speed = Math.random() * 0.5;
            }
            update() {
                this.y -= this.speed;
                if (this.y < 0) this.y = canvas.height;
            }
            draw() {
                ctx.fillStyle = "rgba(238, 239, 249, 0.4)";
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function init() {
            for (let i = 0; i < 100; i++) stars.push(new Star());
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            stars.forEach(s => { s.update(); s.draw(); });
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', resize);
        resize(); init(); animate();
    </script>
</body>
</html>




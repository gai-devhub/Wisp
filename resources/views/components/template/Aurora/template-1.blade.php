<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #a78bfa;
            --secondary: #c4b5fd;
            --bg: #0f0a1e;
            --card-bg: rgba(255, 255, 255, 0.08);
            --new: #7c3aed;
            --text-light: #f3f4f6;
            --gold: #fbbf24;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text-light);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            overflow-x: hidden; overflow-y: auto; min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Ambient Glow Backgrounds */
        .aura {
            position: fixed;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.15) 0%, rgba(15, 10, 30, 0) 70%);
            border-radius: 50%;
            z-index: 0;
            filter: blur(60px);
            animation: pulse 10s infinite alternate;
        }

        @keyframes pulse {
            from {
                transform: scale(1);
                opacity: 0.5;
            }

            to {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        /* Layout Structure */
        .container {
            display: flex;
            position: relative;
            z-index: 10;
            min-height: 100vh;
            width: 100%;
        }

        /* Left side: Visual Hero */
        .visual-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .hero-img-wrapper {
            position: relative;
            width: 280px;
            height: 280px;
        }

        .hero-img-wrapper::before {
            content: '';
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            border: 2px solid var(--primary);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
            border-style: dashed;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .recipient-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--bg);
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.3);
        }

        /* Right side: Content Panel */
        .content-section {
            flex: 1.2;
            background: rgba(15, 10, 30, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            padding: 80px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
         overflow-y: auto; max-height: 100vh;  justify-content: flex-start !important; padding-top: 40px !important; }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            margin: 0 0 20px 0;
            line-height: 1;
            background: linear-gradient(to right, var(--primary), #fff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .recipient-name {
            font-size: 1.5rem;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .message-body {
            font-size: 1.2rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            max-width: 500px;
        }

        .message-body b,
        .message-body strong {
            color: var(--primary);
            font-size: 1.4rem;
        }

        .greeting-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            color: var(--gold);
            margin-bottom: 20px;
            display: block;
        }

        /* Audio Player Styling */
        #audioPlayer {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 100;
            background: var(--card-bg);
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid rgba(167, 139, 250, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        #toggleAudio {
            background: var(--new);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
        }

        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }

            .visual-section {
                height: auto;
                padding: 60px 20px;
                position: relative;
            }

            .content-section {
                border-left: none;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding: 40px 30px;
            }

            h1 {
                font-size: 3rem;
            }

            .hero-img-wrapper {
                width: 200px;
                height: 200px;
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
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
            }
            .viewport, .container, .wrapper, .main-container {
                display: flex !important;
                flex-direction: column !important;
            }
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
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
    <div class="aura" style="top: -10%; right: -10%;"></div>
    <div class="aura" style="bottom: -10%; left: -10%; animation-delay: -5s;"></div>
    <div class="overlay" id="overlay"></div>


    <div class="container">
        <div class="visual-section">
            <div class="hero-img-wrapper">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" class="recipient-img" alt="Recipient">
                @endif
            </div>
            <div style="margin-top: 30px; text-align: center;">
                <div class="recipient-name">{{ $message->recipient_special_name ?? $message->recipient_name }}</div>
            </div>
        </div>

        <div class="content-section">
            <h1>{{ $message->title }}</h1>

            <div class="message-body">
                <span class="greeting-text">{{ $message->greeting }}</span>
                {!! $message->message_display !!}
                <br><br>
                <div style="border-left: 3px solid var(--primary); padding-left: 20px; margin-top: 30px;">
                    <b
                        style="font-family: 'Cormorant Garamond', serif; font-style: italic;">{{ $message->last_note }}</b>
                </div>
            </div>
        </div>
    </div>

        <x-background-music :mediaFiles="$mediaFiles">
        <div id="audioPlayer">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#a78bfa" },
                shape: { type: "circle" },
                opacity: { value: 0.2, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: true, distance: 200, color: "#a78bfa", opacity: 0.1, width: 1 },
                move: { enable: true, speed: 0.8, direction: "none", random: true, straight: false, out_mode: "out" }
            }
        });

        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'block';
                alert('Screenshots disabled.');
                e.preventDefault();
            }
        });
    </script>
</body>

</html>





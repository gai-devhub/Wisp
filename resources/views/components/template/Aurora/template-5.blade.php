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
            --bg: #0b061a;
            /* Deepened dark theme */
            --accent: #7c3aed;
            --text-light: #f3f4f6;
        }

        body {
            background: var(--bg);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-light);
            overflow-x: hidden; overflow-y: auto;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* The Hero Layout */
        .main-stage {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 90%;
            max-width: 800px;
        }

        /* Large Breathable Image */
        .recipient-hero {
            position: relative;
            width: 200px;
            height: 200px;
            margin-bottom: -30px;
            /* Overlap effect */
            z-index: 11;
        }

        .recipient-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 40px;
            /* Modern squircle */
            border: 2px solid var(--primary);
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.4);
            animation: breath 4s infinite ease-in-out;
        }

        @keyframes breath {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 20px 50px rgba(124, 58, 237, 0.4);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 25px 70px rgba(167, 139, 250, 0.6);
            }
        }

        /* Borderless Glass Message Panel */
        .message-box {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            padding: 80px 40px 40px 40px;
            border-radius: 40px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
         overflow-y: auto !important; max-height: 100vh !important; justify-content: flex-start !important; padding-top: 40px !important; }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            margin: 0 0 10px 0;
            background: linear-gradient(135deg, var(--primary), #fff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .name-badge {
            font-size: 1.2rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--secondary);
            margin-bottom: 30px;
            display: block;
        }

        .description {
            font-size: 1.15rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
        }

        .description b,
        .description strong {
            color: var(--primary);
            font-size: 1.3rem;
        }

        /* Top-Right Vinyl Music Button */
        .music-disk-container {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        #toggleAudio {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: conic-gradient(#111, #333, #111, #333);
            border: 2px solid var(--primary);
            color: white;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: 0.3s;
        }

        .playing {
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2.2rem;
            }

            .recipient-hero {
                width: 150px;
                height: 150px;
            }

            .message-box {
                padding: 60px 20px 30px 20px;
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

        <x-background-music :mediaFiles="$mediaFiles">
        <div class="music-disk-container">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
        </div>
    </x-background-music>

    <div class="main-stage">
        <div class="recipient-hero">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="Hero">
            @endif
        </div>

        <div class="message-box">
            <h1>{{ $message->title }}</h1>
            <span class="name-badge">{{ $message->recipient_special_name ?? $message->recipient_name }}</span>

            <div class="description">
                <strong>{{ $message->greeting }}</strong><br><br>
                {!! $message->message_display !!}
                <br><br>
                <b>{{ $message->last_note }}</b>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 50, density: { enable: true, value_area: 800 } },
                color: { value: "#a78bfa" },
                shape: { type: "circle" },
                opacity: { value: 0.2, random: true },
                size: { value: 4, random: true },
                line_linked: { enable: true, distance: 200, color: "#a78bfa", opacity: 0.1, width: 1 },
                move: { enable: true, speed: 0.8, direction: "none", out_mode: "out" }
            }
        });
    </script>
</body>

</html>





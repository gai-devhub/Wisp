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
            --bg: #1e1b4b;
            --card-bg: rgba(255, 255, 255, 0.95);
            --new: #7c3aed;
            --text: #1e1b4b;
        }

        body {
            background: var(--bg);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* New Background Glows */
        .glow {
            position: fixed;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(50px);
        }

        /* Hero Section Structure */
        .hero-section {
            width: 100%;
            padding-top: 60px;
            padding-bottom: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
        }

        .profile-container {
            position: relative;
            margin-bottom: 25px;
        }

        .main-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.4);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .main-photo:hover {
            transform: scale(1.05) rotate(3deg);
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.5rem, 8vw, 4rem);
            margin: 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        /* Narrative Message Panel */
        .content-panel {
            width: 90%;
            max-width: 600px;
            background: var(--card-bg);
            border-radius: 30px 30px 0 0;
            padding: 50px 40px;
            color: var(--text);
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
            margin-top: 20px;
            flex-grow: 1;
        }

        .recipient-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            color: var(--new);
            margin-bottom: 30px;
            display: block;
            font-weight: 700;
        }

        .description {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #333;
            text-align: left;
        }

        .description strong {
            color: var(--new);
            font-size: 1.4rem;
        }

        .footer-note {
            margin-top: 40px;
            display: block;
            text-align: center;
            font-weight: 600;
            background: linear-gradient(90deg, var(--new), var(--primary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 1.3rem;
            letter-spacing: 1px;
        }

        /* Floating Audio Control */
        #audioPlayer {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        #toggleAudio {
            background: var(--new);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
        }

        @media (max-width: 600px) {
            .content-panel {
                padding: 40px 25px;
            }

            .main-photo {
                width: 120px;
                height: 120px;
            }

            h1 {
                font-size: 2.2rem;
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
        @media (max-width: 768px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
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
    <div class="glow" style="top: -10%; left: -10%;"></div>
    <div class="glow" style="bottom: 10%; right: -10%;"></div>
        <x-background-music :mediaFiles="$mediaFiles">
        <div id="audioPlayer">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <div class="hero-section">
        <div class="profile-container">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" class="main-photo" alt="Recipient">
            @endif
        </div>
        <h1>{{ $message->title }}</h1>
    </div>

    <div class="content-panel">
        <span class="recipient-title">{{ $message->recipient_special_name ?? $message->recipient_name }}</span>

        <div class="description">
            <strong>{{ $message->greeting }}</strong>
            <br><br>
            {!! $message->message_display !!}
            <br><br>
            <span class="footer-note">{{ $message->last_note }}</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#c4b5fd" },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 0.6, direction: "top", out_mode: "out" }
            }
        });
    </script>
</body>

</html>



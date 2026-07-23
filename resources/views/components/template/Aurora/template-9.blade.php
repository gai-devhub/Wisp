<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #a78bfa;
            --secondary: #c4b5fd;
            --bg: #2d1b69;
            --card-bg: rgba(255, 255, 255, 0.07);
            --content-text: rgba(255, 255, 255, 0.9);
            --new: #7c3aed;
            --text: #1e1b4b;
        }
        
        body {
            position: relative;
            background: var(--bg);
            background: radial-gradient(circle at center, #1e1b4b 0%, #0f0a1e 100%);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden; overflow-y: auto;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #particles-js { position: fixed; width: 100%; height: 100%; z-index: -1; }

        /* Background Glows */
        .glow {
            position: absolute;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
            z-index: -1;
            filter: blur(50px);
            animation: pulse 10s infinite alternate;
        }

        @keyframes pulse {
            from { opacity: 0.5; transform: scale(1); }
            to { opacity: 1; transform: scale(1.2); }
        }

        .main-container {
            display: flex;
            width: 90%;
            max-width: 1000px;
            height: 80vh;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Left Side: Hero Image */
        .visual-sidebar {
            flex: 1;
            position: relative;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-img-container {
            width: 250px;
            height: 320px;
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid var(--primary);
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.3);
            transform: rotate(-2deg);
            transition: 0.5s ease;
        }

        .hero-img-container:hover {
            transform: rotate(0deg) scale(1.02);
            box-shadow: 0 0 40px rgba(167, 139, 250, 0.5);
        }

        .hero-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Right Side: Message */
        .content-area {
            flex: 1.5;
            padding: 60px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            margin: 0;
            background: linear-gradient(90deg, #fff, var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.1;
        }

        .recipient-special {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: var(--primary);
            margin-top: 10px;
            font-weight: 600;
        }

        .message-body {
            margin-top: 40px;
            color: var(--content-text);
            font-size: 1.15rem;
            line-height: 1.8;
            font-weight: 300;
        }

        .message-body strong {
            color: #fff;
            font-size: 1.5rem;
            font-family: 'Cormorant Garamond', serif;
            display: block;
            margin-bottom: 15px;
        }

        .last-note {
            margin-top: auto;
            padding-top: 30px;
            font-style: italic;
            color: var(--secondary);
            font-size: 1.2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Audio Controls */
        #audioPlayer {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 100px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        #toggleAudio {
            background: var(--primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        #toggleAudio:hover { transform: scale(1.1); background: var(--new); }

        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 9999; display: none; }

        /* Mobile Adjustments */
        @media (max-width: 900px) {
            .main-container { flex-direction: column; height: 90vh; overflow-y: auto; }
            .visual-sidebar { border-right: none; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 30px; }
            .content-area { padding: 30px; }
            h1 { font-size: 2.5rem; }
            .hero-img-container { width: 180px; height: 230px; }
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
    <div class="glow" style="top: -10%; left: -10%;"></div>
    <div class="glow" style="bottom: -10%; right: -10%; animation-delay: -5s;"></div>
    
    <div class="overlay" id="overlay"></div>
        <x-background-music :mediaFiles="$mediaFiles">
        <div id="audioPlayer">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <div class="main-container">
        <div class="visual-sidebar">
            <div class="hero-img-container">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @endif
            </div>
            <div class="recipient-special">{{ $message->recipient_special_name ?? $message->recipient_name }}</div>
        </div>

        <div class="content-area">
            <h1>{{ $message->title }}</h1>
            <div class="message-body">
                <strong>{{ $message->greeting }}</strong>
                {!! $message->message_display !!}
            </div>
            <div class="last-note">{{ $message->last_note }}</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#a78bfa" },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: true, distance: 150, color: "#a78bfa", opacity: 0.1, width: 1 },
                move: { enable: true, speed: 0.4, direction: "none", random: true, straight: false, out_mode: "out" }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'block';
                alert('Screenshots are not allowed on this page.');
                e.preventDefault();
            }
        });
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
    </script>
</body>
</html>


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
            --card-bg: rgba(255, 255, 255, 0.92);
            --new: #7c3aed;
            --text: #1e1b4b;
        }
        
        body {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            text-align: center;
            background: var(--bg);
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            margin: 0;
            padding: 20px;
            overflow-x: hidden; overflow-y: auto;
        }

        #particles-js { position: fixed; width: 100%; height: 100%; z-index: -1; }
        #mainTitle { text-align: center; font-size: 30px; margin-bottom: 1px; position: relative; overflow: hidden; }
        h1 {
            color: #1a1a1a;
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            font-size: 3rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
            margin: 20px 0;
        }
        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s ease;
        }
        h1:hover::after { transform: scaleX(1); }
        h2 { color: #1a1a1a; font-weight: 400; font-size: 2rem; }
        p { font-size: 1.5rem; line-height: 1.5; color: #333; }
        .card {
            width: 90%;
            max-width: 400px;
            border: 1px solid rgba(167, 139, 250, 0.3);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            background: var(--card-bg);
        }
        .header { display: flex; align-items: center; margin-bottom: 1.5rem; }
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
            border: 2px solid var(--primary);
            box-shadow: 0 0 15px rgba(167, 139, 250, 0.4);
            transition: transform 0.3s;
        }
        .logo:hover { transform: rotate(15deg) scale(1.1); }
        .logo img { width: 100%; height: 100%; object-fit: cover; }
        .content { display: inline-block; }
        .title { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; color: var(--text); margin: 0; }
        strong { color: var(--text); font-weight: 600; }
        b {
            font-weight: 600;
            background: linear-gradient(90deg, var(--new), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .description { font-size: 1.1rem; color: #393737; margin-top: 5px; line-height: 1.5; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.5); z-index: 9999; display: none; }
        #audioPlayer {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: var(--card-bg);
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            border: 1px solid rgba(167, 139, 250, 0.3);
        }
        #audioControls { display: flex; align-items: center; gap: 10px; }
        #toggleAudio {
            background: linear-gradient(135deg, var(--primary), var(--new));
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .floating-elements {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        .floating-element {
            position: absolute;
            background: rgba(167, 139, 250, 0.12);
            border: 1px solid rgba(167, 139, 250, 0.3);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(50px, 50px) rotate(90deg); }
            50% { transform: translate(0, 100px) rotate(180deg); }
            75% { transform: translate(-50px, 50px) rotate(270deg); }
        }
        @media (max-width: 1080px) { h1 { font-size: 2rem; } h2 { font-size: 1.5rem; } p { font-size: 1.2rem; } .card { max-height: 80vh; } }
        @media (max-width: 600px) { h1 { font-size: 1.8rem; } h2 { font-size: 1.2rem; } p { font-size: 1.1rem; } .card { max-height: 75vh; } #audioPlayer { bottom: 10px; right: 10px; } }
        @media (max-width: 400px) { h1 { font-size: 1.6rem; } h2 { font-size: 1rem; } p { font-size: 1rem; } .card { max-height: 70vh; } }
    
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
    <div class="floating-elements">
        <div class="floating-element" style="width: 100px; height: 100px; top: 20%; left: 10%;"></div>
        <div class="floating-element" style="width: 150px; height: 150px; top: 60%; left: 35%;"></div>
        <div class="floating-element" style="width: 80px; height: 80px; top: 80%; left: 40%;"></div>
    </div>
    <div class="overlay" id="overlay"></div>
        <x-background-music :mediaFiles="$mediaFiles">
        <div id="audioPlayer">
            <div id="audioControls">
                <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
        </div>
    </x-background-music>
    <div class="head">
        <h1 id="mainTitle"><b>{{ $message->title }}</b></h1>
    </div>
    <div class="card">
        <div class="header">
            <div class="logo">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @endif
            </div>
            <div class="content">
                <div class="title"><b>{{ $message->recipient_special_name ?? $message->recipient_name }}</b></div>
            </div>
        </div>
        <p class="description"><strong>{{ $message->greeting }}</strong></br></br>{!! $message->message_display !!}</br></br></br><b>{{ $message->last_note }}</b></p>
    </div>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#a78bfa" },
                shape: { type: "circle" },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 150, color: "#a78bfa", opacity: 0.4, width: 1 },
                move: { enable: true, speed: 0.5, direction: "none", random: true, straight: false, out_mode: "out" }
            },
            interactivity: {
                detect_on: "canvas",
                events: { onhover: { enable: true, mode: "repulse" }, onclick: { enable: true, mode: "push" } }
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@200;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #a78bfa;
            --secondary: #c4b5fd;
            --bg: #0b061a;
            --accent: #7c3aed;
            --glass: rgba(255, 255, 255, 0.08);
        }
        
        body {
            background: var(--bg);
            background: radial-gradient(circle at center, #1a0b3b 0%, #080414 100%);
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

        #particles-js { position: fixed; width: 100%; height: 100%; z-index: 1; }

        /* The Presentation Wrapper */
        .wrapper {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* MASSIVE IMAGE HOLDER - FIXED & ENHANCED */
        .image-holder {
            position: relative;
            width: 220px;
            height: 220px;
            margin-bottom: -60px; /* Pulls the card up behind it */
            z-index: 20;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-outer-glow {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; /* Organic moving shape */
            background: var(--primary);
            filter: blur(25px);
            opacity: 0.4;
            animation: morph 8s linear infinite;
        }

        .image-frame {
            position: relative;
            width: 180px;
            height: 180px;
            padding: 5px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 38% 62% 63% 37% / 41% 44% 56% 59%; /* Unique organic curve */
            animation: morph 8s linear infinite;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        @keyframes morph {
            0% { border-radius: 38% 62% 63% 37% / 41% 44% 56% 59%; }
            50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            100% { border-radius: 38% 62% 63% 37% / 41% 44% 56% 59%; }
        }

        .image-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
        }

        /* MESSAGE CARD */
        .card {
            background: var(--glass);
            backdrop-filter: blur(35px);
            -webkit-backdrop-filter: blur(35px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            padding: 80px 40px 40px 40px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            width: 100%;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.4rem;
            margin: 10px 0 5px 0;
            background: linear-gradient(to bottom, #fff, var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .recipient-name {
            font-size: 1.1rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 30px;
            display: block;
            font-weight: 300;
        }

        .message-body {
            font-size: 1.15rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 200;
            max-height: 35vh;
            overflow-y: auto;
            padding: 0 10px;
        }

        .message-body::-webkit-scrollbar { width: 3px; }
        .message-body::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        .greeting {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            color: #fff;
            display: block;
            margin-bottom: 15px;
            font-style: italic;
        }

        .note {
            margin-top: 30px;
            display: block;
            font-weight: 600;
            color: var(--primary);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        /* MUSIC CONTROLLER - Top Right */
        .audio-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.2);
            padding: 8px 15px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        #toggleAudio {
            background: var(--primary);
            border: none;
            width: 38px; height: 38px;
            border-radius: 50%;
            cursor: pointer;
            color: white;
            font-size: 1.1rem;
        }

        @media (max-width: 600px) {
            .image-holder { width: 180px; height: 180px; margin-bottom: -50px; }
            .image-frame { width: 140px; height: 140px; }
            .card { padding: 70px 25px 30px 25px; }
            h1 { font-size: 1.9rem; }
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
    <x-background-music :mediaFiles="$mediaFiles">
        <div class="audio-container">
            <button type="button" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <div class="wrapper">
        <div class="image-holder">
            <div class="image-outer-glow"></div>
            <div class="image-frame">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="Recipient Image">
                @else
                    <div style="width:100%; height:100%; background: var(--glass);"></div>
                @endif
            </div>
        </div>

        <div class="card">
            <h1>{{ $message->title }}</h1>
            <span class="recipient-name">{{ $message->recipient_special_name ?? $message->recipient_name }}</span>
            
            <div class="message-body">
                <span class="greeting">{{ $message->greeting }}</span>
                {!! $message->message_display !!}
                <span class="note">{{ $message->last_note }}</span>
            </div>
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
                line_linked: { enable: false },
                move: { enable: true, speed: 0.5, direction: "top", out_mode: "out" }
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Quicksand:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0f0a1e;
            --accent-lavender: #a78bfa;
            --accent-gold: #d4af37;
            --text-light: #f3f4f6;
            --glass: rgba(255, 255, 255, 0.05);
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-color);
            background: radial-gradient(circle at center, #1a0b3b 0%, #080414 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Quicksand', sans-serif;
            color: var(--text-light);
            overflow-x: hidden;
            padding: 20px;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: radial-gradient(var(--accent-lavender) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.1;
            z-index: -1;
        }

        .card-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 60px;
            max-width: 1100px;
            width: 100%;
            z-index: 10;
        }

        .polaroid {
            background: #1e1b4b;
            padding: 15px 15px 50px 15px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            transform: rotate(-3deg);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-shrink: 0;
            position: relative;
            border: 1px solid rgba(167, 139, 250, 0.3);
        }

        .polaroid:hover {
            transform: rotate(0deg) scale(1.03);
            border-color: var(--accent-gold);
        }

        .photo-frame {
            width: 280px;
            height: 350px;
            background: #000;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .photo-frame img { width: 100%; height: 100%; object-fit: cover; }

        .name-tag {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            text-align: center;
            margin-top: 15px;
            color: var(--accent-gold);
        }

        .controls { position: absolute; top: 25px; right: 25px; z-index: 10; }

        button {
            padding: 10px 20px;
            border: none;
            background: linear-gradient(135deg, var(--accent-lavender), var(--accent-gold));
            color: white;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            cursor: pointer;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .letter-box {
            background: #fff;
            padding: 50px;
            max-width: 550px;
            border-radius: 10px;
            border-left: 6px solid var(--accent-gold);
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            color: #1e1b4b;
        }

        h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #7c3aed, #ac7834);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .intro { font-weight: 600; margin-bottom: 20px; font-size: 1.2rem; color: #7c3aed; }

        .message-content { line-height: 1.8; color: #333; margin-bottom: 30px;  overflow-y: auto; max-height: 100vh; }

        .closing { font-family: 'Dancing Script', cursive; font-size: 1.8rem; color: #ac7834; }

        @media (max-width: 850px) {
            .card-container { flex-direction: column; gap: 40px; }
            body { overflow-y: auto; }
        }

        .confetti { position: fixed; width: 8px; height: 8px; z-index: 1000; top: -10px; border-radius: 50%; }
    
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
            .content-section { padding: 15px 10px !important;  overflow-y: auto; max-height: 100vh; }
        }
    </style>
</head>
<body>

    <div class="card-container">
        
        <div class="polaroid">
            <div class="photo-frame">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @else
                    <img src="{{ asset('img/default-profile.jpg') }}" alt="Default">
                @endif
            </div>
            <div class="name-tag">{{ $message->recipient_special_name ?? $message->recipient_name }} 💕</div>
        </div>

        <main class="letter-box">
            <h1>{{ $message->title }}</h1>
            <p class="intro">{{ $message->greeting }}</p>
            
            <div class="message-content">
                {!! $message->message_display !!}
            </div>

            <p class="closing">{{ $message->last_note }}</p>

            <x-background-music :mediaFiles="$mediaFiles">
                <div class="controls">
                    <button type="button" id="playBtn" data-idle-label="Play Music">&#127925; Play Music</button>
                </div>
            </x-background-music>
        </main>

    </div>

    <script>
        window.addEventListener('wisp:music-play', createConfetti);

        function createConfetti() {
            const colors = ['#a78bfa', '#d4af37', '#7c3aed', '#ffffff'];
            for (let i = 0; i < 60; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                document.body.appendChild(confetti);
                
                confetti.animate([
                    { top: '-10px', opacity: 1 },
                    { top: '100vh', opacity: 0 }
                ], {
                    duration: Math.random() * 3000 + 2000,
                    easing: 'cubic-bezier(0, .9, .57, 1)'
                }).onfinish = () => confetti.remove();
            }
        }
    </script>
</body>
</html>

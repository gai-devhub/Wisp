<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Montserrat:wght@300;400&family=Playfair+Display:ital,wght@1,400;1,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --rose-gold: #e5b0a3;
            --soft-white: #ffffff;
            --text-gray: #4a4a4a;
            --heart-red: #d63031;
        }

        body {
            margin: 0;
            background: var(--soft-white);
            font-family: 'Montserrat', sans-serif;
            color: var(--text-gray);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden; overflow-y: auto;
        }

        /* Floating petals background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .romance-card {
            position: relative;
            z-index: 10;
            width: 85%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 2px;
            box-shadow: 0 10px 60px rgba(0,0,0,0.03);
            border: 1px solid #f0f0f0;
        }

        .image-side {
            position: relative;
            overflow: hidden;
        }

        .image-side img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 15px solid white;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .content-side {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px solid #eee;
        }

        .intro {
            font-family: 'Pinyon Script', cursive;
            font-size: 2.5rem;
            color: var(--rose-gold);
            margin-bottom: 0;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-style: italic;
            margin: 0 0 30px 0;
            color: #222;
        }

        .love-note {
            font-size: 1.05rem;
            line-height: 2;
            color: #555;
            font-style: italic;
        }

        .highlight-word {
            color: var(--heart-red);
            font-weight: 600;
        }

        .signature {
            margin-top: 40px;
            font-family: 'Pinyon Script', cursive;
            font-size: 2.2rem;
            color: var(--rose-gold);
        }

        #music-button {
            position: fixed;
            top: 30px;
            right: 30px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--rose-gold);
            z-index: 100;
        }

        @media (max-width: 900px) {
            .romance-card { grid-template-columns: 1fr; padding: 20px; overflow-y: auto; height: 90vh; }
            .content-side { border-left: none; padding: 20px 0; }
            h1 { font-size: 2rem; }
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

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-button" data-idle-color="#e5b0a3">&#10084;</button>
    </x-background-music>

    <div class="romance-card">
        <div class="image-side">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>

        <div class="content-side">
            <div class="intro">Mawupemɔ,</div>
            <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
            
            <div class="love-note">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
            </div>


            <div class="signature">
                    @auth
                        <p>{{ auth()->user()->name }}</p>
                    @endauth
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 25 },
                color: { value: "#e5b0a3" },
                shape: { type: "circle" },
                opacity: { value: 0.3 },
                size: { value: 5, random: true },
                move: { enable: true, speed: 1, direction: "bottom" }
            }
        });
    </script>
</body>
</html>


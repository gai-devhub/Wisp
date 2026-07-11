<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #c5a059;
            --soft-white: #f8f9fa;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--soft-white);
            background-image: radial-gradient(#d1d1d1 0.5px, transparent 0.5px);
            background-size: 20px 20px; /* Subtle dot grid background */
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 40px;
            align-items: center;
        }

        /* Polaroid Photo Section */
        .photo-frame {
            background: white;
            padding: 15px 15px 60px 15px;
            box-shadow: 0 10px 30px var(--shadow);
            transform: rotate(-3deg);
            transition: transform 0.5s ease;
        }

        .photo-frame:hover {
            transform: rotate(0deg) scale(1.02);
        }

        .photo-frame img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            filter: sepia(20%);
        }

        .photo-caption {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            text-align: center;
            margin-top: 15px;
            color: var(--gold);
        }

        /* Content Section */
        .letter-content {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            padding: 40px;
            border-left: 5px solid var(--gold);
            box-shadow: 10px 10px 0px rgba(197, 160, 89, 0.1);
        }

        h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            margin: 0 0 20px 0;
            color: #333;
        }

        .message {
            line-height: 1.8;
            color: #555;
            font-size: 0.95rem;
        }

        .signature {
            margin-top: 30px;
            font-family: 'Dancing Script', cursive;
            font-size: 1.5rem;
        }

        /* Minimal Audio Player */
        #audioContainer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 100;
        }

        #playBtn {
            background: var(--gold);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 10px var(--shadow);
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            .photo-frame {
                max-width: 300px;
                margin: 0 auto;
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
            .content-section { padding: 15px 10px !important; }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <div class="container">
        <div class="photo-frame">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
            @endif
            <div class="photo-caption">{{ $message->recipient_special_name ?? $message->recipient_name }}</div>
        </div>

        <div class="letter-content">
            <h1>{{ $message->greeting ?? 'Hello' }}</h1>
            <div class="message">
            <p>{!! $message->message_display !!}</p>
                <div class="signature">
                    {!! nl2br(e($message->last_note ?? '')) !!}
                </div>
            </div>
        </div>
    </div>
    <x-background-music :mediaFiles="$mediaFiles">
        <div id="audioContainer">
            <button type="button" id="playBtn" data-idle-label="&#9835;">&#9835;</button>
        </div>
    </x-background-music>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 30 },
                color: { value: "#c5a059" },
                shape: { type: "polygon", polygon: { nb_sides: 5 } },
                opacity: { value: 0.2 },
                size: { value: 4, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 1.5, direction: "none" }
            }
        });
    </script>
</body>
</html>
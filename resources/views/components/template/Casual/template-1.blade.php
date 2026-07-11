<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --accent: #d4af37;
            --white: #ffffff;
            --text-main: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            font-family: 'Poppins', sans-serif;
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .card {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            text-align: center;
            animation: fadeIn 1.5s ease-out;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            background: linear-gradient(to right, #d4af37, #b8860b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 20px 0;
        }

        .logo {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 4px solid var(--white);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 0 auto 20px auto;
            transition: transform 0.5s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .description {
            line-height: 1.8;
            font-size: 1.05rem;
            text-align: justify;
            color: #444;
        }

        .highlight {
            color: var(--accent);
            font-weight: 600;
        }

        #audioPlayer {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
            background: white;
            padding: 10px 18px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        #toggleAudio {
            border: none;
            background: #d4af37;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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

    <div class="card">
        <div class="header">
            <div class="logo">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @endif
            </div>
            <h1>{{ $message->greeting ?? 'Hello' }}</h1>
            <h2 style="font-family: 'Playfair Display'; font-style: italic; color: #888; margin-bottom: 25px;">
                {{ $message->recipient_special_name ?? $message->recipient_name }}</h2>
        </div>

        <div class="description">
            <p><strong>{{ $message->greeting }},</strong></p></br>
            <p>{!! $message->message_display !!}</p></br>
            <p style="text-align: center; margin-top: 35px;">
                <span class="highlight">{{ $message->last_note ?? '' }}</span>
            </p>
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
                number: { value: 50, density: { enable: true, value_area: 800 } },
                color: { value: "#d4af37" },
                shape: { type: "circle" },
                opacity: { value: 0.2, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 0.8, direction: "top", random: true, out_mode: "out" }
            }
        });
    </script>
</body>

</html>
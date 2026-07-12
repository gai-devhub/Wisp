<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Tenor+Sans&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --cream: #f9f9f7;
            --text: #2c2c2c;
        }

        body {
            margin: 0;
            background-color: var(--cream);
            font-family: 'Tenor Sans', sans-serif;
            color: var(--text);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden; overflow-y: auto;
        }

        /* Abstract Background Decor */
        .circle-decor {
            position: fixed;
            top: -10%;
            right: -5%;
            width: 40vw;
            height: 40vw;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 50%;
            z-index: 1;
            animation: rotate 20s linear infinite;
        }

        .main-stage {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 90%;
            max-width: 900px;
        }

        .portrait-frame {
            position: relative;
            width: 280px;
            height: 380px;
            margin: 0 auto 50px auto;
            overflow: hidden;
            clip-path: inset(0 0 0 0);
            transition: clip-path 1.5s cubic-bezier(0.77, 0, 0.175, 1);
        }

        .portrait-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.2);
            transition: transform 2s ease;
        }

        .portrait-frame.visible {
            clip-path: inset(0 0 0 0);
        }

        .portrait-frame.visible img {
            transform: scale(1);
        }

        h1 {
            font-family: 'Italiana', serif;
            font-size: clamp(3rem, 8vw, 6rem);
            margin: 0;
            font-weight: 400;
            letter-spacing: -2px;
            line-height: 1;
        }

        .since-label {
            font-size: 0.7rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 20px;
            display: block;
        }

        .inspirational-quote {
            max-width: 600px;
            margin: 40px auto;
            font-size: 1.1rem;
            line-height: 1.8;
            opacity: 0;
            transform: translateY(20px);
            transition: all 1s ease 0.5s;
        }

        .inspirational-quote.reveal {
            opacity: 1;
            transform: translateY(0);
        }

        /* Floating Audio Icon */
        #music-trigger {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: var(--gold);
            text-decoration: underline;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .portrait-frame { width: 200px; height: 280px; }
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

    <div class="circle-decor"></div>
    
    <div class="main-stage">
        <span class="since-label">Est. 2013</span>
        <div class="portrait-frame" id="frame">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>
        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        <div class="inspirational-quote" id="quote">
            <p>{!! $message->message_display !!}</p></br>
            <br><br>
            <strong># {{ $message->last_note ?? '' }}</strong>
        </div>
    </div>

    <button type="button" id="music-trigger" data-idle-label="ENABLE AUDIO" >ENABLE AUDIO</button>
    <x-background-music :mediaFiles="$mediaFiles">

    </x-background-music>

    <script>
        window.onload = () => {
            document.getElementById('frame').classList.add('visible');
            document.getElementById('quote').classList.add('reveal');
        };
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@1,400;1,700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.6);
            --gold-accent: #c5a059;
            --text-main: #121212;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: #f4f4f4;
            overflow: hidden;
        }

        /* Ambient Background */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            filter: blur(20px) brightness(0.9);
            z-index: 1;
            transform: scale(1.1);
        }

        /* Content Overlay */
        .glass-canvas {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5%;
        }

        .main-card {
            background: var(--glass);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            width: 100%;
            max-width: 1100px;
            height: 80vh;
            border-radius: 40px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            display: flex;
            overflow: hidden;
            box-shadow: 0 50px 100px rgba(0,0,0,0.1);
            animation: slideUp 1.2s ease-out;
        }

        /* Image Side */
        .visual-side {
            flex: 1.2;
            position: relative;
            overflow: hidden;
        }

        .visual-side img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 10s ease-out;
        }

        .main-card:hover .visual-side img {
            transform: scale(1.1);
        }

        /* Text Side */
        .content-side {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
        }

        .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 6px;
            color: var(--gold-accent);
            margin-bottom: 15px;
            font-weight: 600;
        }

        h1 {
            font-family: 'Bodoni Moda', serif;
            font-size: 3.5rem;
            margin: 0;
            color: var(--text-main);
            font-style: italic;
            font-weight: 700;
        }

        .message-box {
            margin-top: 30px;
            font-size: 1rem;
            line-height: 1.8;
            color: #333;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 15px;
        }

        /* Custom Scrollbar for message */
        .message-box::-webkit-scrollbar { width: 3px; }
        .message-box::-webkit-scrollbar-thumb { background: var(--gold-accent); }

        .signature-area {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        #play-control {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            background: white;
            color: var(--text-main);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 0.8rem;
            letter-spacing: 2px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        #play-control:hover {
            background: var(--gold-accent);
            color: white;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 950px) {
            .main-card { flex-direction: column; height: auto; border-radius: 20px; }
            .visual-side { height: 40vh; }
            h1 { font-size: 2.2rem; }
            .content-side { padding: 30px; }
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
    <div class="ambient-bg" @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image) style="background-image: url('{{ asset('storage/' . $mediaFiles->recipient_image) }}'); background-repeat: no-repeat; background-position: center center;" @endif></div>

    <div class="glass-canvas">
        <div class="main-card">
            <div class="visual-side">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>
            
            <div class="content-side">
                <div class="label">{{ $message->title ?? 'A Celebration' }}</div>
                <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
                
                <div class="message-box">
                    <p>{!! $message->message_display ?? nl2br(e($message->message ?? '')) !!}</p>
                </div>

                <div class="signature-area">
                    {!! nl2br(e($message->last_note ?? '')) !!}
                </div>
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="play-control" data-idle-label="PLAY MUSIC">PLAY MUSIC</button>
    </x-background-music>
</body>
</html>

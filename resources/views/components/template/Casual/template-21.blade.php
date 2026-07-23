<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <!-- Premium Editorial Typography -->
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-canvas: #0a0b0d;
            --text-main: #f9fafb;
            --text-muted: #8e939e;
            --line-color: rgba(255, 255, 255, 0.06);
            --accent: #dfb76c;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            width: 100vw;
            background-color: var(--bg-canvas);
            font-family: 'DM Sans', sans-serif;
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Large Ambient Backdrop */
        .ambient-glow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at var(--x, 30%) var(--y, 30%),
                    rgba(223, 183, 108, 0.03) 0%,
                    transparent 60%);
            z-index: 1;
            pointer-events: none;
        }

        /* Spacious Editorial Canvas */
        .editorial-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1100px;
            padding: 8% 6%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 60px;
            box-sizing: border-box;
        }

        @media (min-width: 768px) {
            .editorial-container {
                grid-template-columns: 35% 1fr;
                gap: 80px;
                align-items: start;
            }
        }

        /* Left Column: Metadata & Title */
        .aside-panel {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid var(--line-color);
            padding-bottom: 30px;
        }

        @media (min-width: 768px) {
            .aside-panel {
                border-bottom: none;
                border-right: 1px solid var(--line-color);
                padding-bottom: 0;
                padding-right: 40px;
                position: sticky;
                top: 8%;
            }
        }

        .date-stamp {
            font-size: 0.75rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 24px;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-weight: 300;
            line-height: 1.1;
            margin: 0;
            letter-spacing: -1px;
        }

        /* Right Column: Deep Spacious Content */
        .main-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
         overflow-y: auto; max-height: 100vh; }

        .message-body {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.65rem;
            line-height: 1.8;
            color: var(--text-main);
            font-weight: 300;
        }

        .message-body p {
            margin: 0 0 40px 0;
        }

        .note-text {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            line-height: 1.8;
            color: var(--text-muted);
            max-width: 540px;
            margin-top: 20px;
        }

        .author-signature {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid var(--line-color);
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-style: italic;
            color: var(--accent);
        }

        /* Architectural Modern Utility Button */
        #music-disc {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            z-index: 100;
            font-size: 0.8rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 10px;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #music-disc::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            background: var(--line-color);
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        #music-disc:hover {
            color: var(--text-main);
        }

        #music-disc.playing {
            color: var(--accent);
        }

        #music-disc.playing::before {
            background: var(--accent);
            animation: pulse 1.5s infinite alternate;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }

            100% {
                transform: scale(1.5);
                opacity: 1;
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
            .content-section { padding: 15px 10px !important;  overflow-y: auto; max-height: 100vh; }
        }
    </style>
</head>

<body>

    <div class="ambient-glow" id="ambient"></div>

    <div class="editorial-container">
        <!-- Left Side Layout Elements -->
        <div class="aside-panel">
            <div class="date-stamp">
                {{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('F Y') : '' }}
            </div>
            <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}</h1>
        </div>

        <!-- Main Spacious Text Flow -->
        <div class="main-content">
            <div class="message-body">
                {!! $message->message_display !!}
            </div>

            @if(!empty($message->last_note))
                <div class="note-text">
                    {!! $message->last_note !!}
                </div>
            @endif

            @auth
                <div class="author-signature">
                    — {{ auth()->user()->name }}
                </div>
            @endauth
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <!-- Audio trigger styled as a clean typographic label -->
        <button type="button" id="music-disc" data-idle-label="Audio">Audio</button>
    </x-background-music>

    <script>
        const ambient = document.getElementById('ambient');

        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth) * 100;
            const y = (e.clientY / window.innerHeight) * 100;
            ambient.style.setProperty('--x', `${x}%`);
            ambient.style.setProperty('--y', `${y}%`);
        });
    </script>
</body>

</html>

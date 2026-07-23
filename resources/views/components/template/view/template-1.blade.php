<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- Gem emoji (💎) as favicon -->
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>

    <style>
        :root {
            --primary: #00f0ff;
            --secondary: #ff00f7;
            --bg: #0a0a1a;
            --card-bg: rgba(20, 20, 40, 0.8);
            --new: #3663f5;
            --text: #13133f;
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

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        #mainTitle{
            text-align: center;
            font-size: 30px;
            font-weight: 4rem;
            margin-bottom: 1px;
            position: relative;
            overflow: hidden;
        }
        
        h1 {
            color: #1a1a1a;
            font-family: 'Orbitron', sans-serif;
            font-weight: 400;
            font-size: 3rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-transform: uppercase;
            letter-spacing: 3px;
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
         
        h1:hover::after {
            transform: scaleX(1);
        }

        h2 {
            color: #1a1a1a;
            font-weight: 400;
            font-size: 2rem;
        }
        p {
            font-size: 1rem;
            line-height: 1.5;
            color: #333;
        }
        i {
            color: rgb(83, 81, 81);
        }
        #page {
            margin: 50% 0;
            text-align: center;
        }
        .card {
            width: 90%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 10px;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .content {
            display: inline-block;
        }
        .title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: var(--bg);
            margin: 0;
        }
         .countdown {
            font-size: 0.8rem;
            margin-top: 1px;
            color: rgb(255, 255, 255);
            text-shadow: 0 0 10px rgba(97, 189, 192, 0.5);
        }
         .countdownMessage {
            font-size: 0.8rem; 
            margin-top: 1px;
            color: rgb(255, 255, 255);
            text-shadow: 0 0 10px rgba(97, 189, 192, 0.5);
        }

        strong {
            color: var(--bg);
            font-weight: 600;
        }
         i {
            color: dodgerblue;
            font-style: italic;
        }
         b {
            font-weight: 600;
            background: linear-gradient(90deg, var(--new), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .description {
            font-size: 1rem;
            color: #393737;
            margin-top: 5px;
            line-height: 1.5;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5);
            z-index: 9999;
            display: none;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
            border: 2px solid var(--primary);
            box-shadow: 0 0 15px var(--primary);
            transition: transform 0.3s;
        }
        
        .logo:hover {
            transform: rotate(15deg) scale(1.1);
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Audio player styles */
        #audioPlayer {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        #audioControls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #toggleAudio {
            background: #b2beb5; /* ash color */
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
        /*particles floating*/
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        } 

        .floating-element {
            position: absolute;
            background: rgba(0, 240, 255, 0.1);
            border: 1px solid rgba(0, 240, 255, 0.3);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        } 
         
        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(50px, 50px) rotate(90deg);
            }
            50% {
                transform: translate(0, 100px) rotate(180deg);
            }
            75% {
                transform: translate(-50px, 50px) rotate(270deg);
            }
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 1080px) {
            h1 {
                font-size: 2rem;
            }
            h2 {
                font-size: 1.5rem;
            }
            p {
                font-size: 1.2rem;
            }
            .card {
                max-height: 80vh;
            }
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.8rem;
            }
            h2 {
                font-size: 1.2rem;
            }
            p {
                font-size: 1.1rem;
            }
            .card {
                max-height: 75vh;
            }
            #audioPlayer {
                bottom: 10px;
                right: 10px;
            }
        }

        @media (max-width: 400px) {
            h1 {
                font-size: 1.6rem;
            }
            h2 {
                font-size: 1rem;
            }
            p {
                font-size: 1rem;
            }
            .card {
                max-height: 70vh;
            }
        }
            /* Global Mobile Scroll Fix */
        @media (max-width: 1024px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
            }
                        .visual-anchor, .hero-img-wrapper, .left-panel, .image-portal, .image-section {
                height: auto !important;
                min-height: 40vh;
                position: relative !important;
            }
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
            }
            .viewport, .container, .wrapper, .main-container {
                display: flex !important;
                flex-direction: column !important;
            }
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
            }
            .viewport, .container, .wrapper, .main-container {
                height: auto !important;
                min-height: 100vh;
                overflow: visible !important;
            }
            .content-hub, .message-panel, .message-box, .letter-box, .content-area, .text-panel, .message-wrapper, .message-container, .content-section, .text-section, .message-content, .main-content, .card {
                max-height: none !important;
                overflow-y: visible !important;
                height: auto !important;
            }
        }
    </style>
    <!-- <link rel="stylesheet" href="viewer-template-1.css"> -->
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
        <h1 id="mainTitle"><b>{{ $message->title }}</b></h1> {{-- {{ Auth::user()->wishMessages()->first()?->greeting }} --}}
         <h2 id="currentTime" class="countdown"></h2> {{--{{ 'Y-m-d-H-i-s'}} --}}
        <p id="countdownMessage" class="countdownMessage"></p>
    </div>

    <div class="card">
        <div class="header">
            <div class="logo">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @endif
            </div>
            <div class="content">
                <div class="title"><b>{{ $message->recipient_special_name }}</b></div> {{-- {{ Auth::user()->wishMessages()->first()?->recipient_special_name }} --}}
            </div>
        </div>
            <p class="description"><strong>{{ $message->greeting }}</strong></br> {{-- to be commented --}}
            </br>{!! $message->message_display !!}</br></br></br>
            <b>{{ $message->last_note }}</b></p>

             {{-- {{ Auth::user()->wishMessages()->first()?->message }} --}}
            {{-- {{ Auth::user()->wishMessages()->first()?->last_note }} --}}
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Initialize particles.js
        particlesJS("particles-js", {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#00f0ff" },
                shape: { type: "circle" },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 150, color: "#00f0ff", opacity: 0.4, width: 1 },
                move: { enable: true, speed: 0.5, direction: "none", random: true, straight: false, out_mode: "out" }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" }
                }
            }
        });
        
// Detect Print Screen key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'block';
                alert('Screenshots are not allowed on this page.');
                e.preventDefault();
            }
        });

        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>
</html>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@1,300;1,600&family=Montserrat:wght@100;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --chaos-red: #ff3e3e;
            --aurora-blue: #7c7cf8;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body, html {
            margin: 0; padding: 0;
            height: 100vh; width: 100vw;
            background: #050505;
            font-family: 'Montserrat', sans-serif;
            color: #fff;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        canvas {
            position: fixed;
            top: 0; left: 0;
            z-index: 1;
        }

        .center-frame {
            position: relative;
            z-index: 10;
            text-align: center;
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 60px;
            border: 1px solid rgba(255,255,255,0.1);
            pointer-events: none; /* Allows interaction with background */
        }

        .meta {
            font-size: 0.6rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--aurora-blue);
            margin-bottom: 20px;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-style: italic;
            margin: 0;
            line-height: 1;
        }

        .desc {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            opacity: 0.7;
            margin-top: 20px;
            font-style: italic;
        }

        .kwame-tag {
            margin-top: 40px;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: #af944d;
        }

        #nav-next {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: none;
            border: 1px solid white;
            color: white;
            padding: 10px 25px;
            font-size: 0.7rem;
            letter-spacing: 3px;
            cursor: pointer;
            z-index: 100;
            transition: 0.3s;
        }

        #nav-next:hover {
            background: white;
            color: black;
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
</head>
<body>

    <canvas id="chaosCanvas"></canvas>

    <div class="center-frame">
        <div class="meta">{{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }} // Interaction</div>
        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        <div class="desc">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
        </div>
        @auth
            <div class="kwame-tag">Signed: {{ auth()->user()->name }}</div>
        @endauth
    </div>

    <button id="nav-next" onclick="location.href='page12.html'">CONTINUE JOURNEY</button>

    <script>
        const canvas = document.getElementById('chaosCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let particles = [];
        const mouse = { x: null, y: null, radius: 150 };

        window.addEventListener('mousemove', (e) => {
            mouse.x = e.x;
            mouse.y = e.y;
        });

        class Particle {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.baseX = x;
                this.baseY = y;
                this.size = Math.random() * 3 + 1;
                this.density = (Math.random() * 30) + 1;
                this.color = Math.random() > 0.5 ? '#7c7cf8' : '#ff3e3e';
            }

            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.closePath();
                ctx.fill();
            }

            update() {
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                let forceDirectionX = dx / distance;
                let forceDirectionY = dy / distance;
                let maxDistance = mouse.radius;
                let force = (maxDistance - distance) / maxDistance;
                let directionX = forceDirectionX * force * this.density;
                let directionY = forceDirectionY * force * this.density;

                if (distance < mouse.radius) {
                    this.x -= directionX;
                    this.y -= directionY;
                } else {
                    if (this.x !== this.baseX) {
                        let dx = this.x - this.baseX;
                        this.x -= dx / 10;
                    }
                    if (this.y !== this.baseY) {
                        let dy = this.y - this.baseY;
                        this.y -= dy / 10;
                    }
                }
            }
        }

        function init() {
            particles = [];
            // Create a grid of particles
            for (let y = 0; y < canvas.height; y += 20) {
                for (let x = 0; x < canvas.width; x += 20) {
                    particles.push(new Particle(x, y));
                }
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].draw();
                particles[i].update();
            }
            requestAnimationFrame(animate);
        }

        init();
        animate();

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            init();
        });
    </script>
</body>
</html>





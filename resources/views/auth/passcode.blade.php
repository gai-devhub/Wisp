<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Passcode Verification — WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #F28C76;
            --primary-hover: #E8674A;
            --primary-light: rgba(242, 140, 118, 0.08);
            --secondary: #2B1F3D;
            --accent: #4A3A63;
            --text: #2B1F3D;
            --text-muted: #6E6178;
            --border: rgba(43, 31, 61, 0.10);
            --bg: #ffffff;
            --bg-subtle: #FDF6EC;
            --success-bg: rgba(60, 122, 94, 0.10);
            --success-text: #3C7A5E;
            --error-bg: #fef2f2;
            --error-text: #b91c1c;
            --radius: 12px;
            --radius-lg: 20px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-lg: 0 25px 50px -12px rgba(0,0,0,0.1);
            --transition: 0.2s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image:
                radial-gradient(circle at 5% 10%, rgba(242, 140, 118, 0.07) 0%, transparent 25%),
                radial-gradient(circle at 95% 90%, rgba(43, 31, 61, 0.06) 0%, transparent 25%);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            color: var(--text);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }

        .auth-wrap {
            width: 100%;
            max-width: min(420px, 96vw);
            background: var(--bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 40px 44px 44px;
            text-align: center;
        }

        .auth-header-logo {
            display: inline-flex; align-items: center; gap: 10px;
            text-decoration: none; color: var(--text); font-weight: 700; font-size: 1.35rem;
            margin-bottom: 24px;
            font-family: 'Fraunces', Georgia, serif;
        }
        .auth-header-logo img { width: 40px; height: 40px; object-fit: contain; }

        h3 { font-size: 1.5rem; margin-bottom: 8px; color: var(--text); font-family: 'Fraunces', Georgia, serif; }
        p.hint { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 30px; }

        .form-control {
            width: 100%; padding: 14px; border: 1px solid var(--border);
            border-radius: var(--radius); font-size: 1.1rem; text-align: center;
            letter-spacing: 2px; font-weight: 500; font-family: inherit;
            transition: 0.2s; background: var(--bg);
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        
        .btn {
            width: 100%; padding: 14px 20px; border: none; border-radius: var(--radius);
            font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.2s;
            margin-top: 20px; background: linear-gradient(135deg, var(--primary), var(--primary-hover)); color: #fff;
        }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.05); }

        .alert { padding: 14px; border-radius: var(--radius); margin-bottom: 20px; font-size: 0.9rem; text-align: left;}
        .alert-danger { background: var(--error-bg); color: var(--error-text); border: 1px solid #fecaca; }

        .logout-link {
            display: inline-block; margin-top: 24px; color: var(--text-muted); text-decoration: none; font-size: 0.9rem;
        }
        .logout-link:hover { color: var(--error-text); text-decoration: underline; }
        
        .ambient-grid { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .blob { position: absolute; border-radius: 50%; filter: blur(110px); opacity: 0.2; animation: float 20s infinite alternate; }
        .blob-1 { top: -5%; left: -5%; width: 45vw; height: 45vw; background: var(--primary); }
        .blob-2 { bottom: -10%; right: -5%; width: 55vw; height: 55vw; background: var(--secondary); animation-delay: -5s; }
        @keyframes float { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(40px, -30px) scale(1.1); } }
    </style>
</head>
<body>
    <div style="position: relative; width: 100%; max-width: min(420px, 96vw);">
        <!-- Backdrop decorative color dot at top right corner matching auth page -->
        <div style="position: absolute; top: -60px; right: -40px; width: 190px; height: 190px; background: #F28C76; border-radius: 50%; filter: blur(65px); opacity: 0.15; z-index: -1;"></div>

        <div class="auth-wrap" style="position: relative; z-index: 1;">
            <div class="auth-header-logo">
                <img src="{{ asset('img/logo.png') }}" alt="WISP">
                <span>WISP</span>
            </div>
            
            <h3>Passcode Required</h3>
            <p class="hint">Enter your security passcode to access your dashboard.</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('auth.passcode.verify.post') }}">
                @csrf
                <input type="password" name="passcode" class="form-control" placeholder="••••" required autofocus>
                <button type="submit" class="btn">Unlock Dashboard <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>
            </form>

            <form method="POST" action="{{ route('auth.logout') }}" style="margin-top: 24px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:0.9rem;">
                    <i class="fas fa-sign-out-alt"></i> Sign out
                </button>
            </form>
        </div>
    </div>
</body>
</html>
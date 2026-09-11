<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sign in — WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            --transition: 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image:
                radial-gradient(circle at 5% 10%, rgba(242, 140, 118, 0.07) 0%, transparent 25%),
                radial-gradient(circle at 95% 90%, rgba(43, 31, 61, 0.06) 0%, transparent 25%);
            color: var(--text);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            padding: max(16px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(16px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left));
            line-height: 1.5;
            -webkit-tap-highlight-color: transparent;
        }

        .auth-wrap {
            width: 100%;
            max-width: min(460px, 96vw);
            background: var(--bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            padding: 40px 44px 44px;
        }

        .auth-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .auth-header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            font-size: 1.35rem;
            font-family: 'Fraunces', Georgia, serif;
        }

        .auth-header-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .auth-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color var(--transition);
        }

        .auth-back:hover {
            color: var(--primary);
        }

        .auth-main {
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .auth-tabs {
            display: flex;
            gap: 4px;
            background: var(--bg-subtle);
            padding: 4px;
            border-radius: 10px;
            margin-bottom: 32px;
        }

        .auth-tab {
            flex: 1;
            padding: 12px 16px;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95rem;
            color: var(--text-muted);
            border-radius: 8px;
            transition: var(--transition);
            border: none;
            background: transparent;
        }

        .auth-tab:hover {
            color: var(--text);
        }

        .auth-tab.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #fff;
            box-shadow: 0 4px 14px rgba(242, 140, 118, 0.4);
        }

        .auth-content {
            display: none;
        }

        .auth-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 14px 44px 14px 44px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            font-family: inherit;
            background: var(--bg);
            color: var(--text);
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 1rem;
            transition: color var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }

        .checkbox-wrap input {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-wrap label {
            font-size: 0.9rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: #fff;
            margin-top: 8px;
        }

        .btn-primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        .btn-google {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
            text-decoration: none;
        }

        .btn-google:hover {
            background: var(--bg-subtle);
            border-color: var(--text-muted);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .auth-divider span {
            padding: 0 16px;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer .link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid #fecaca;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-danger li {
            margin-bottom: 4px;
        }

        .alert-danger li:last-child {
            margin-bottom: 0;
        }

        /* Forgot password header */
        .auth-content h3.auth-heading {
            font-size: 1.25rem;
            margin-bottom: 24px;
            color: var(--text);
            font-family: 'Fraunces', Georgia, serif;
        }

        /* Terms link in signup */
        .checkbox-wrap a {
            color: var(--primary);
            text-decoration: none;
        }

        .checkbox-wrap a:hover {
            text-decoration: underline;
        }

        .hint {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }
            
            .auth-wrap {
                padding: 24px 20px 28px;
                border-radius: 16px;
            }

            .auth-tabs {
                margin-bottom: 20px;
            }

            .auth-tab {
                padding: 8px 10px;
                font-size: 0.85rem;
            }
            
            .form-control {
                padding: 12px 40px 12px 40px;
                font-size: 0.95rem;
            }
            
            .btn {
                padding: 12px 16px;
                font-size: 0.95rem;
            }
        }

        .ambient-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }

        .ambient-grid .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.22;
            animation: floatBlob 24s infinite alternate;
        }

        .ambient-grid .blob-1 {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: var(--primary);
            animation-duration: 26s;
        }

        .ambient-grid .blob-2 {
            bottom: -15%;
            right: -10%;
            width: 55vw;
            height: 55vw;
            background: #2B1F3D;
            animation-delay: -6s;
            animation-duration: 32s;
        }

        .ambient-grid .blob-3 {
            top: 35%;
            left: 45%;
            width: 40vw;
            height: 40vw;
            background: #C7502F;
            animation-delay: -10s;
            animation-duration: 28s;
        }

        .ambient-grid .blob-4 {
            top: 65%;
            left: -5%;
            width: 35vw;
            height: 35vw;
            background: #4A3A63;
            animation-delay: -16s;
            animation-duration: 30s;
        }

        .ambient-grid .blob-5 {
            top: 10%;
            right: 5%;
            width: 35vw;
            height: 35vw;
            background: var(--accent);
            animation-delay: -4s;
            animation-duration: 24s;
        }

        @keyframes floatBlob {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(60px, -40px) scale(1.1);
            }
        }
    </style>
</head>

<body>
    <div style="position: relative; width: 100%; max-width: min(460px, 96vw);">
        <!-- Backdrop decorative color dot at top right corner -->
        <div style="position: absolute; top: -60px; right: -40px; width: 190px; height: 190px; background: #F28C76; border-radius: 50%; filter: blur(65px); opacity: 0.15; z-index: -1;"></div>

        <div class="auth-wrap" style="position: relative; z-index: 1;">
        <header class="auth-header">
            <a href="{{ route('home') }}" class="auth-header-logo">
                <img src="{{ asset('img/logo.png') }}" alt="WISP">
                <span>WISP</span>
            </a>
            <button type="button" class="auth-back" id="signup-back-btn" style="display: none; background: transparent; border: none; cursor: pointer; padding: 0; font-family: inherit;" onclick="prevSignupStep()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </header>

        <div class="auth-main">
            <div class="auth-tabs">
                <button type="button" class="auth-tab active" data-tab="login">Login</button>
                <button type="button" class="auth-tab" data-tab="signup">Sign up</button>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($registrationDisabled))
                <div class="alert alert-warning"
                    style="background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);">
                    Login and registration are temporarily disabled. Only administrators can sign in.
                </div>
            @endif

            <!-- Login -->
            <div class="auth-content active" id="login-form">
                <form method="POST" action="{{ route('auth.login.post') }}">
                    @csrf
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" class="form-control" id="login-email" name="email"
                                placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" class="form-control" id="login-password" name="password"
                                placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="password-toggle" data-target="login-password"
                                aria-label="Toggle password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <label class="checkbox-wrap">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <button type="button" class="auth-tab link" data-tab="forgot-password"
                            style="background:none;padding:0;cursor:pointer;border:none;">Forgot password?</button>
                    </div>
                    <button type="submit" class="btn btn-primary">Sign in</button>
                </form>
                <div class="auth-divider"><span>or continue with</span></div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('auth.google') }}" class="btn btn-google"><i class="fab fa-google"></i>
                        Google</a>
                    <a href="{{ route('auth.spotify') }}" class="btn btn-google"><i class="fab fa-spotify"></i>
                        Spotify</a>
                </div>
            </div>

            <!-- Sign up -->
            <div class="auth-content" id="signup-form">
                <form method="POST" action="{{ route('auth.register') }}">
                    @csrf
                    <div id="signup-step-1">
                        <div class="form-group">
                            <label for="signup-name">Full name</label>
                            <div class="input-wrap">
                                <i class="fas fa-user icon"></i>
                                <input type="text" class="form-control" id="signup-name" name="name" placeholder="Jane Doe"
                                    value="{{ old('name') }}" required autocomplete="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-username">Username</label>
                            <div class="input-wrap">
                                <i class="fas fa-at icon"></i>
                                <input type="text" class="form-control" id="signup-username" name="username"
                                    placeholder="jane_doe" value="{{ old('username') }}" required autocomplete="username"
                                    minlength="3" maxlength="255" pattern="[a-zA-Z0-9_.-]+"
                                    title="Letters, numbers, dots, hyphens and underscores only.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope icon"></i>
                                <input type="email" class="form-control" id="signup-email" name="email"
                                    placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="nextSignupStep()">Next Step <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>
                    </div>

                    <div id="signup-step-2" style="display: none;">
                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock icon"></i>
                                <input type="password" class="form-control" id="signup-password" name="password"
                                    placeholder="At least 8 characters" required autocomplete="new-password">
                                <button type="button" class="password-toggle" data-target="signup-password"
                                    aria-label="Toggle password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="hint">Minimum 8 characters.</p>
                        </div>
                        <div class="form-group">
                            <label for="signup-password-confirm">Confirm password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock icon"></i>
                                <input type="password" class="form-control" id="signup-password-confirm"
                                    name="password_confirmation" placeholder="••••••••" required
                                    autocomplete="new-password">
                                <button type="button" class="password-toggle" data-target="signup-password-confirm"
                                    aria-label="Toggle password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-wrap">
                                <input type="checkbox" name="terms" id="terms-agree" required>
                                <span>I agree to the <a href="{{ route('help.terms') }}" target="_blank">Terms of Service</a> and <a href="{{ route('help.policy') }}" target="_blank">Privacy Policy</a></span>
                            </label>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Create account</button>
                        </div>
                    </div>
                </form>

                <script>
                    function nextSignupStep() {
                        const step1 = document.getElementById('signup-step-1');
                        const inputs = step1.querySelectorAll('input');
                        let valid = true;
                        for (let input of inputs) {
                            if (!input.checkValidity()) {
                                input.reportValidity();
                                valid = false;
                                break;
                            }
                        }
                        if (valid) {
                            step1.style.display = 'none';
                            document.getElementById('signup-step-2').style.display = 'block';
                            document.getElementById('signup-back-btn').style.display = 'inline-flex';
                        }
                    }

                    function prevSignupStep() {
                        document.getElementById('signup-step-2').style.display = 'none';
                        document.getElementById('signup-step-1').style.display = 'block';
                        document.getElementById('signup-back-btn').style.display = 'none';
                    }
                </script>
                <div class="auth-divider"><span>or sign up with</span></div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('auth.google') }}" class="btn btn-google"><i class="fab fa-google"></i>
                        Google</a>
                    <a href="{{ route('auth.spotify') }}" class="btn btn-google"><i class="fab fa-spotify"></i>
                        Spotify</a>
                </div>
            </div>

            <!-- Forgot password -->
            <div class="auth-content" id="forgot-password-form">
                <h3 class="auth-heading">Reset your password</h3>

                <!-- Step 1: Request Code -->
                <div id="reset-step-1">
                    <p class="hint" style="margin-bottom: 20px;">Enter your email to receive a verification code.</p>
                    <div class="alert alert-danger hidden" id="error-step-1"></div>
                    <form id="form-reset-step-1" onsubmit="handleResetStep1(event)">
                        @csrf
                        <div class="form-group">
                            <label for="forgot-email">Email</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope icon"></i>
                                <input type="email" class="form-control" id="forgot-email" name="email"
                                    placeholder="you@example.com" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-step-1">
                            <span class="btn-text">Send Code</span>
                            <i class="fas fa-spinner fa-spin hidden btn-spinner"></i>
                        </button>
                    </form>
                </div>

                <!-- Step 2: Verify Code -->
                <div id="reset-step-2" class="hidden">
                    <p class="hint" style="margin-bottom: 20px;">Enter the 6-digit verification code sent to <strong id="display-email"></strong>.</p>
                    <div class="alert alert-danger hidden" id="error-step-2"></div>
                    <form id="form-reset-step-2" onsubmit="handleResetStep2(event)">
                        @csrf
                        <div class="form-group">
                            <label for="forgot-code">Verification Code</label>
                            <div class="input-wrap">
                                <input type="text" class="form-control" id="forgot-code" name="code"
                                    placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autocomplete="one-time-code" 
                                    style="text-align: center; letter-spacing: 16px; font-size: 1.5rem; font-weight: 700; padding: 16px; text-indent: 16px; transition: all 0.2s ease;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-step-2">
                            <span class="btn-text">Verify Code</span>
                            <i class="fas fa-spinner fa-spin hidden btn-spinner"></i>
                        </button>
                    </form>
                </div>

                <!-- Step 3: Update Password -->
                <div id="reset-step-3" class="hidden">
                    <p class="hint" style="margin-bottom: 20px;">Enter your new password below.</p>
                    <div class="alert alert-danger hidden" id="error-step-3"></div>
                    <form id="form-reset-step-3" onsubmit="handleResetStep3(event)">
                        @csrf
                        <div class="form-group">
                            <label for="forgot-password">New password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock icon"></i>
                                <input type="password" class="form-control" id="forgot-password" name="password"
                                    placeholder="••••••••" required minlength="8">
                                <button type="button" class="password-toggle" data-target="forgot-password"
                                    aria-label="Toggle password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="forgot-password-confirm">Confirm new password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock icon"></i>
                                <input type="password" class="form-control" id="forgot-password-confirm"
                                    name="password_confirmation" placeholder="••••••••" required minlength="8">
                                <button type="button" class="password-toggle" data-target="forgot-password-confirm"
                                    aria-label="Toggle password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <p class="hint" style="margin-bottom: 16px;">Make sure both passwords match.</p>
                        <button type="submit" class="btn btn-primary" id="btn-step-3">
                            <span class="btn-text">Update Password</span>
                            <i class="fas fa-spinner fa-spin hidden btn-spinner"></i>
                        </button>
                    </form>
                </div>

                <div class="form-footer" style="margin-top: 24px;">
                    <button type="button" class="auth-tab link" data-tab="login"
                        style="background:none;border:none;cursor:pointer;padding:0;">
                        <i class="fas fa-arrow-left"></i> Back to sign in
                    </button>
                </div>
        </div>
    </div>

    <script>
        (function () {
            const tabs = document.querySelectorAll('.auth-tab');
            const contents = document.querySelectorAll('.auth-content');

            function showTab(tabId) {
                // Only style main tabs (login/signup); forgot-password has no tab pill
                tabs.forEach(t => {
                    const tab = t.getAttribute('data-tab');
                    t.classList.toggle('active', tab === tabId && (tab === 'login' || tab === 'signup'));
                });
                contents.forEach(c => {
                    const contentTabId = c.id.replace('-form', '');
                    c.classList.toggle('active', contentTabId === tabId);
                });
                
                const authTabsContainer = document.querySelector('.auth-tabs');
                if (tabId === 'forgot-password') {
                    authTabsContainer.classList.add('hidden');
                    window.history.pushState({ path: '/forgot-password' }, '', '/forgot-password');
                } else {
                    authTabsContainer.classList.remove('hidden');
                    window.history.pushState({ path: '/' + tabId }, '', '/' + tabId);
                }

                const backBtn = document.getElementById('signup-back-btn');
                if (tabId === 'signup' && document.getElementById('signup-step-2').style.display === 'block') {
                    backBtn.style.display = 'inline-flex';
                } else {
                    backBtn.style.display = 'none';
                }
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    showTab(tab.getAttribute('data-tab'));
                });
            });

            // Open signup tab when coming from welcome "Join" / "Create your wish" links or /signup route
            var params = new URLSearchParams(window.location.search);
            if (params.get('tab') === 'signup' || window.location.pathname === '/signup') {
                showTab('signup');
            }

            // Password visibility toggle
            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-target');
                    const input = document.getElementById(id);
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = 'fas fa-eye-slash';
                    } else {
                        input.type = 'password';
                        icon.className = 'fas fa-eye';
                    }
                });
            });
        /* Password Reset Wizard JS */
        const resetEmailInput = document.getElementById('forgot-email');
        const resetCodeInput = document.getElementById('forgot-code');
        const resetPassInput = document.getElementById('forgot-password');
        const resetPassConfirmInput = document.getElementById('forgot-password-confirm');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let verifiedEmail = '';
        let verifiedCode = '';

        function setBtnLoading(btnId, loading) {
            const btn = document.getElementById(btnId);
            const text = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.btn-spinner');
            if (loading) {
                btn.disabled = true;
                text.classList.add('hidden');
                spinner.classList.remove('hidden');
            } else {
                btn.disabled = false;
                text.classList.remove('hidden');
                spinner.classList.add('hidden');
            }
        }

        function displayError(elId, msg) {
            const el = document.getElementById(elId);
            if (msg) {
                el.innerHTML = `<ul><li>${msg}</li></ul>`;
                el.classList.remove('hidden');
            } else {
                el.innerHTML = '';
                el.classList.add('hidden');
            }
        }

        window.handleResetStep1 = function(e) {
            e.preventDefault();
            const email = resetEmailInput.value;
            displayError('error-step-1', null);
            setBtnLoading('btn-step-1', true);

            fetch('{{ route("auth.password.send-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email })
            })
            .then(res => res.json())
            .then(data => {
                setBtnLoading('btn-step-1', false);
                if (data.success) {
                    verifiedEmail = email;
                    document.getElementById('display-email').innerText = email;
                    document.getElementById('reset-step-1').classList.add('hidden');
                    document.getElementById('reset-step-2').classList.remove('hidden');
                    window.history.pushState({ path: '/password-code-verification' }, '', '/password-code-verification');
                } else {
                    displayError('error-step-1', data.errors?.email?.[0] || data.message || 'Error sending code.');
                }
            })
            .catch(() => {
                setBtnLoading('btn-step-1', false);
                displayError('error-step-1', 'A network error occurred.');
            });
        };

        window.handleResetStep2 = function(e) {
            e.preventDefault();
            const code = resetCodeInput.value;
            displayError('error-step-2', null);
            setBtnLoading('btn-step-2', true);

            fetch('{{ route("auth.password.verify-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: verifiedEmail, code })
            })
            .then(res => res.json())
            .then(data => {
                setBtnLoading('btn-step-2', false);
                if (data.success) {
                    verifiedCode = code;
                    document.getElementById('reset-step-2').classList.add('hidden');
                    document.getElementById('reset-step-3').classList.remove('hidden');
                    window.history.pushState({ path: '/reset-password' }, '', '/reset-password');
                } else {
                    displayError('error-step-2', data.errors?.code?.[0] || data.message || 'Invalid code.');
                }
            })
            .catch(() => {
                setBtnLoading('btn-step-2', false);
                displayError('error-step-2', 'A network error occurred.');
            });
        };

        window.handleResetStep3 = function(e) {
            e.preventDefault();
            const password = resetPassInput.value;
            const password_confirmation = resetPassConfirmInput.value;
            
            if (password !== password_confirmation) {
                displayError('error-step-3', 'Passwords do not match.');
                return;
            }

            displayError('error-step-3', null);
            setBtnLoading('btn-step-3', true);

            fetch('{{ route("auth.password.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: verifiedEmail, code: verifiedCode, password, password_confirmation })
            })
            .then(res => res.json())
            .then(data => {
                setBtnLoading('btn-step-3', false);
                if (data.success) {
                    alert('Password updated successfully. You can now login.');
                    // Reset forms
                    document.getElementById('form-reset-step-1').reset();
                    document.getElementById('form-reset-step-2').reset();
                    document.getElementById('form-reset-step-3').reset();
                    
                    document.getElementById('reset-step-3').classList.add('hidden');
                    document.getElementById('reset-step-1').classList.remove('hidden');
                    
                    // Switch to login tab
                    document.querySelector('.auth-tab[data-tab="login"]').click();
                } else {
                    displayError('error-step-3', data.errors?.password?.[0] || data.message || 'Failed to update password.');
                }
            })
            .catch(() => {
                setBtnLoading('btn-step-3', false);
                displayError('error-step-3', 'A network error occurred.');
            });
        };
    })();
    </script>
</body>

</html>
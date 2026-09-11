<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - WISP</title>
    <!-- Google Fonts: Fraunces + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #F28C76;
            --primary-hover: #E8674A;
            --success: #3C7A5E;
            --bg-color: #FDF6EC;
            --text-dark: #2B1F3D;
            --text-light: #6E6178;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image:
                radial-gradient(circle at 5% 10%, rgba(242, 140, 118, 0.07) 0%, transparent 25%),
                radial-gradient(circle at 95% 90%, rgba(43, 31, 61, 0.06) 0%, transparent 25%);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-dark);
        }

        .verify-container {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(43, 31, 61, 0.08);
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: slideUp 0.5s ease-out;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(60, 122, 94, 0.1);
            color: var(--success);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            margin: 0 auto 20px;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.2s both;
        }

        h2 {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 600;
            font-family: 'Fraunces', Georgia, serif;
            color: var(--text-dark);
        }

        p {
            color: var(--text-light);
            font-size: 15px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-primary {
            display: inline-block;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(242, 140, 118, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(242, 140, 118, 0.3);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes popIn {
            0% { transform: scale(0); }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div style="position: relative; width: 90%; max-width: 400px; display: flex; justify-content: center; align-items: center;">
        <!-- Backdrop decorative color dot at top right corner matching auth page -->
        <div style="position: absolute; top: -60px; right: -40px; width: 190px; height: 190px; background: #F28C76; border-radius: 50%; filter: blur(65px); opacity: 0.15; z-index: -1;"></div>

        <div class="verify-container" style="position: relative; z-index: 1; width: 100%; max-width: none; margin: 0;">
            <div class="icon-circle">
                <i class="fas fa-check"></i>
            </div>
            <h2>Email Verified!</h2>
            <p>Thank you, <strong>{{ $user->username }}</strong>! Your email address has been successfully verified. Your account is now fully secured.</p>
            
            <a href="{{ route('user.page') }}" class="btn-primary">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
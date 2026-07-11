<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - WISP</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --success: #10b981;
            --bg-color: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: slideUp 0.5s ease-out;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.1);
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
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(79, 70, 229, 0.3);
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

    <div class="verify-container">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>
        <h2>Email Verified!</h2>
        <p>Thank you, <strong>{{ $user->username }}</strong>! Your email address has been successfully verified. Your account is now fully secured.</p>
        
        <a href="{{ route('user.page') }}" class="btn-primary">
            Go to Dashboard
        </a>
    </div>

</body>
</html>

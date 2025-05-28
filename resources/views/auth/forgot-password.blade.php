<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SIAkred</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #055FC5;
            --primary-hover: #044a9c;
            --text-color: #2d3748;
            --text-secondary: #718096;
            --error-color: #e53e3e;
            --success-color: #48bb78;
            --input-border: #e2e8f0;
            --input-focus: rgba(5, 95, 197, 0.1);
            --background: #f8fafc;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            background-color: rgb(255, 255, 255);
        }

        .forgot-password-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 4rem;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-in-out;
        }

        .image-section {
            flex: 1.5;
            background: url('{{ asset('assets/images/LoginImage.jpg') }}') center/cover no-repeat;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding-left: 4rem;
        }

        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(5, 95, 197, 0.7) 0%, rgba(2, 48, 102, 0.8) 100%);
        }

        .polinema-brand {
            position: relative;
            z-index: 2;
            color: var(--white);
            max-width: 400px;
        }

        .polinema-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 1.5rem;
        }

        .polinema-title {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        .polinema-subtitle {
            font-size: 1.5rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .logo-header {
            display: flex;
            align-items: center;
            margin-bottom: 3rem;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            margin-right: 1rem;
        }

        .logo-text {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: -0.5px;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.75rem;
        }

        .form-header p {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.75rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 1px solid var(--input-border);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: var(--white);
        }

        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--input-focus);
            outline: none;
        }

        .form-input::placeholder {
            color: #a0aec0;
            opacity: 1;
        }

        .back-home-button {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }

        .submit-button {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            box-shadow: 0 4px 6px rgba(5, 95, 197, 0.1);
        }

        .submit-button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(5, 95, 197, 0.15);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .login-link a:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: block;
            animation: shake 0.3s ease-in-out;
        }

        .success-message {
            color: var(--success-color);
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
            background-color: rgba(72, 187, 120, 0.1);
            border-radius: 6px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .success-message svg {
            margin-right: 0.5rem;
            flex-shrink: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        @media (max-width: 1024px) {
            .forgot-password-section {
                padding: 3rem;
            }

            .image-section {
                padding-left: 3rem;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .image-section {
                min-height: 300px;
                order: -1;
                padding: 2rem;
                flex: 1;
                align-items: center;
                justify-content: flex-start;
            }

            .polinema-brand {
                text-align: center;
                max-width: 100%;
            }

            .polinema-logo {
                margin: 0 auto 1rem;
            }

            .polinema-title {
                font-size: 1.8rem;
            }

            .polinema-subtitle {
                font-size: 1.3rem;
            }

            .forgot-password-section {
                padding: 2.5rem 2rem;
                max-width: 100%;
            }

            .logo-header {
                margin-bottom: 2rem;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
            }

            .logo-text {
                font-size: 1.8rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .forgot-password-section {
                padding: 2rem 1.5rem;
            }

            .logo-header {
                margin-bottom: 1.5rem;
            }

            .logo-text {
                font-size: 1.6rem;
            }

            .form-header h2 {
                font-size: 1.3rem;
            }

            .form-input {
                padding: 0.9rem 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="forgot-password-section">
        <a href="{{ url('/login') }}" class="back-home-button">
            <svg width="24" height="24" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" stroke-width="1.2">
                <rect width="24" height="24" fill="white"></rect>
                <path d="M14.5 17L9.5 12L14.5 7" stroke="#055fc5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            Back to Login
        </a>

        <div class="logo-header">
            <img class="logo-icon" src="{{ asset('assets/images/eyeSearchLogin.png') }}" alt="SIAkred Logo">
            <div class="logo-text">SiAkred</div>
        </div>

        <div class="form-header">
            <h2>Forgot Password</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        @if (session('success'))
        <div class="success-message">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#48bb78" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input"
                    placeholder="Enter your email address" value="{{ old('email') }}" required autocomplete="email"
                    autocapitalize="off">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-button">Send Password Reset Link</button>

            <div class="login-link">
                <p>Remember your password? <a href="{{ route('login') }}">Login here</a></p>
            </div>
        </form>
    </div>

    <div class="image-section">
        <div class="polinema-brand">
            <img class="polinema-logo" src="{{ asset('assets/images/LogoPolinema.png') }}" alt="Polinema Logo">
            <h2 class="polinema-title">POLITEKNIK</h2>
            <p class="polinema-subtitle">NEGERI MALANG</p>
        </div>
    </div>

    <script>
        // Add focus effect when clicking on form inputs
        document.querySelectorAll('.form-input').forEach(input => {
            const formGroup = input.closest('.form-group');
            const label = formGroup.querySelector('.form-label');
            
            input.addEventListener('focus', function() {
                if (label) {
                    label.style.color = '#055FC5';
                }
            });

            input.addEventListener('blur', function() {
                if (label) {
                    label.style.color = '';
                }
            });
        });

        // Prevent form submission animation from being interrupted
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerHTML = 'Sending...';
        });
    </script>
</body>

</html> 
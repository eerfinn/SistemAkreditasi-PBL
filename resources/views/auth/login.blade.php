<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAkred</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --primary-color: #055FC5;
            --primary-hover: #044a9c;
            --text-color: #2d3748;
            --text-secondary: #718096;
            --error-color: #e53e3e;
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

        .login-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 4rem;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-in-out;
        }

        .login-image-section {
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

        .login-image-section::before {
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
            padding: 1rem 3rem 1rem 1.25rem;
            /* Added right padding for eye icon */
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

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            cursor: pointer;
            width: 22px;
            height: 22px;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }



        .password-toggle:hover {
            opacity: 1;
        }

        .login-button {
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

        .login-button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(5, 95, 197, 0.15);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .forgot-password a:hover {
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
            .login-form-section {
                padding: 3rem;
            }

            .login-image-section {
                padding-left: 3rem;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .login-image-section {
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

            .login-form-section {
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
            .login-form-section {
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
                padding: 0.9rem 2.5rem 0.9rem 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-form-section">

        <a href="{{ url('/') }}" class="back-home-button" style="display:inline-flex; align-items: center; gap: 0.3rem; margin-bottom: 1rem; color: var(--primary-color); font-weight: 600; text-decoration: none;">
            <svg width="24" height="24" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" stroke-width="1.2">
                <rect width="24" height="24" fill="white"></rect>
                <path d="M14.5 17L9.5 12L14.5 7" stroke="#055fc5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            Back to Home
        </a>

        <div class="logo-header">
            <img class="logo-icon" src="{{ asset('assets/images/eyeSearchLogin.png') }}" alt="SIAkred Logo">
            <div class="logo-text">SiAkred</div>
        </div>

        <div class="form-header">
            <h2>Member Sign In</h2>
            <p>Enter your username and password to sign in.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" id="username" name="login" class="form-input"
                    placeholder="Enter your username or email" value="{{ old('login') }}" required autocomplete="username"
                    autocapitalize="off">
                @error('login')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-input"
                        placeholder="Enter your password" required autocomplete="current-password">
                    <img id="togglePassword" class="password-toggle" src="{{ asset('assets/images/EyeHidee.png') }}"
                        alt="Toggle Password">
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="login-button">Log In</button>

            <div class="forgot-password">
                {{-- Forgot your password? Please contact <a href="mailto:admin@polinema.ac.id">admin</a> --}}
                <p>Forgot your password? <a href="{{ route('password.request') }}">Reset password</a></p>
            </div>
        </form>
    </div>

    <div class="login-image-section">
        <div class="polinema-brand">
            <img class="polinema-logo" src="{{ asset('assets/images/LogoPolinema.png') }}" alt="Polinema Logo">
            <h2 class="polinema-title">POLITEKNIK</h2>
            <p class="polinema-subtitle">NEGERI MALANG</p>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const isPassword = password.type === 'password';
            password.type = isPassword ? 'text' : 'password';
            this.src = isPassword ?
                "{{ asset('assets/images/EyeUnhide.png') }}" :
                "{{ asset('assets/images/EyeHidee.png') }}";
        });

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
            button.innerHTML = 'Logging in...';
        });
    </script>
</body>

</html>

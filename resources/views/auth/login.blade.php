<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Abhaya+Libre&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body, html {
      width: 100%;
      height: 100%;
      overflow: hidden;
      font-family: 'Poppins', sans-serif;
      background: #f5f5f5;
    }
    .wrapper {
      width: 100vw;
      height: 100vh;
      overflow: hidden;
      position: relative;
    }
    .scaled-content {
      transform: scale(0.7);
      transform-origin: top left;
      width: 1920px;
      height: 1080px;
    }
    .error-message {
      color: #dc3545;
      font-size: 12px;
      margin-top: 5px;
      font-family: Poppins;
      position: absolute;
      left: 303px;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="scaled-content">
      <form method="POST" action="{{ route('login') }}" style="width: 1920px; height: 1080px; position: relative; background: white; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); overflow: hidden; outline: 1px black solid; outline-offset: -1px">
        @csrf
        <img style="width: 860px; height: 910px; left: 956px; top: 24px; position: absolute; opacity: 0.70; border-radius: 25px" src="{{ asset('assets/images/LoginImage.jpg') }}" />
        
        <input type="email" name="login" placeholder="Email" value="{{ old('login') }}" style="width: 330px; height: 45px; left: 303px; top: 498px; position: absolute; background: rgba(217, 217, 217, 0); border-radius: 12px; border: 1px #C9C9C9 solid; padding-left: 10px; font-size: 13px; font-family: Poppins; font-weight: 400; color: #3F3E3E;" />
        @error('login')
        <div class="error-message" style="top: 548px;">{{ $message }}</div>
        @enderror
        
        <button type="submit" style="width: 330px; height: 45px; left: 303px; top: 620px; position: absolute; background: #055FC5; border-radius: 12px; border: none; color: white; font-size: 17px; font-family: Poppins; font-weight: 600; transition: background-color 0.3s ease, transform 0.2s ease; cursor: pointer;"
          onmouseover="this.style.backgroundColor='#044a9c'; this.style.transform='scale(1.05)';"
          onmouseout="this.style.backgroundColor='#055FC5'; this.style.transform='scale(1)';"
        >Log in</button>
        
        <div style="width: 132px; height: 39px; left: 471px; top: 303px; position: absolute; background: #055FC5; border-radius: 12px; border: 1px rgba(200.81, 200.81, 200.81, 0) solid"></div>
        
        <div style="left: 303px; top: 408px; position: absolute; color: #3F3E3E; font-size: 30px; font-family: Poppins; font-weight: 600; word-wrap: break-word">Member Sign In</div>
        
        <div style="left: 303px; top: 460px; position: absolute; color: #828282; font-size: 13px; font-family: Poppins; font-weight: 400; word-wrap: break-word">Enter your email and password to sign in</div>
        
        <div style="left: 325px; top: 683px; position: absolute">
          <span style="color: #828282; font-size: 13px; font-family: Poppins; font-weight: 400;">Forgot your password? please contact </span>
          <span style="color: #055FC5; font-size: 13px; font-family: Poppins; font-weight: 600;">admin</span>
        </div>
        
        <div style="position: relative; width: 330px; height: 45px; left: 303px; top: 559px; position: absolute;">
          <input id="password" type="password" name="password" placeholder="Password" style="width: 100%; height: 100%; background: rgba(217, 217, 217, 0); border-radius: 12px; border: 1px #C9C9C9 solid; padding-left: 10px; font-size: 13px; font-family: Poppins; font-weight: 400; color: #3F3E3E;" />
          <img id="togglePassword" src="{{ asset('assets/images/EyePassword.png') }}" alt="Toggle Password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 20px; height: 20px;" />
        </div>
        @error('password')
        <div class="error-message" style="top: 609px;">{{ $message }}</div>
        @enderror
        
        <script>
          const togglePassword = document.querySelector('#togglePassword');
          const password = document.querySelector('#password');
          togglePassword.addEventListener('click', function () {
            if (password.type === 'password') {
              password.type = 'text';
              this.src = "{{ asset('assets/images/EyePassword.png') }}";
            } else {
              password.type = 'password';
              this.src = "{{ asset('assets/images/EyeHide.png') }}";
            }
          });
        </script>
        <div style="width: 241px; left: 1029px; top: 240px; position: absolute; text-align: center; color: rgba(0, 0, 0, 0.80); font-size: 28px; font-family: 'Abhaya Libre', serif; font-weight: 400; line-height: 33px;">POLITEKNIK NEGERI MALANG</div>
        
        <img style="width: 161px; height: 162px; left: 1066px; top: 69px; position: absolute" src="{{ asset('assets/images/LogoPolinema.png') }}" />
        
        <div style="left: 478px; top: 300px; position: absolute; text-align: center; color: white; font-size: 30px; font-family: Poppins; font-weight: 600;">SiAkred</div>
        
        <img style="width: 137px; height: 108px; left: 293px; top: 271px; position: absolute" src="{{ asset('assets/images/eyeSearchLogin.png') }}" />
      </form>
    </div>
  </div>
</body>
</html>

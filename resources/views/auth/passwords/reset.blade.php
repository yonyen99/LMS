@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <img class="wave" src="{{ asset('img/wave.png') }}" alt="Wave Background">
    <div class="container">
        <div class="img">
            <img src="{{ asset('img/bg.svg') }}" alt="Background Image">
        </div>
        <div class="login-content">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <img src="{{ asset('img/log.png') }}" alt="User Avatar">
                <h2 class="title">{{ __('Reset Password') }}</h2>

                <div class="input-div one @error('email') is-invalid @enderror">
                    <div class="i">
                        <i class='bx bx-envelope'></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('Email Address') }}</h5>
                        <input type="email" class="input" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    </div>
                    <div class="i"></div> <!-- Empty div for grid alignment -->
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="input-div pass @error('password') is-invalid @enderror">
                    <div class="i">
                        <i class='bx bx-lock-alt'></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('New Password') }}</h5>
                        <input type="password" class="input" name="password" id="password" required autocomplete="new-password">
                    </div>
                    <div class="show-password">
                        <i class='bx bx-show' id="toggle-password"></i>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="input-div pass">
                    <div class="i">
                        <i class='bx bx-lock-alt'></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('Confirm Password') }}</h5>
                        <input type="password" class="input" name="password_confirmation" id="password-confirm" required autocomplete="new-password">
                    </div>
                    <div class="show-password">
                        <i class='bx bx-show' id="toggle-password-confirm"></i>
                    </div>
                </div>

                <input type="submit" class="btn" value="{{ __('Reset Password') }}">
            </form>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll(".input");

        function addcl() {
            let parent = this.parentNode.parentNode;
            parent.classList.add("focus");
        }

        function remcl() {
            let parent = this.parentNode.parentNode;
            if (this.value == "") {
                parent.classList.remove("focus");
            }
        }

        inputs.forEach(input => {
            input.addEventListener("focus", addcl);
            input.addEventListener("blur", remcl);
        });

        // Show/Hide Password Toggle for New Password
        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("toggle-password");

        togglePassword.addEventListener("click", () => {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            togglePassword.classList.toggle("bx-show");
            togglePassword.classList.toggle("bx-hide");
        });

        // Show/Hide Password Toggle for Confirm Password
        const passwordConfirmInput = document.getElementById("password-confirm");
        const togglePasswordConfirm = document.getElementById("toggle-password-confirm");

        togglePasswordConfirm.addEventListener("click", () => {
            const type = passwordConfirmInput.getAttribute("type") === "password" ? "text" : "password";
            passwordConfirmInput.setAttribute("type", type);
            togglePasswordConfirm.classList.toggle("bx-show");
            togglePasswordConfirm.classList.toggle("bx-hide");
        });
    </script>
</body>
</html>
@endsection
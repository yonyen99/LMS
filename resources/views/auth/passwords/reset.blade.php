@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
                <h2 class="title">Reset Password</h2>

                {{-- Email --}}
                <div class="input-div one @error('email') is-invalid @enderror">
                    <div class="i">
                        <i class='bx bx-envelope'></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('Email Address') }}</h5>
                        <input type="email" class="input" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert" style="color:red; font-size: 12px;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="input-div pass @error('password') is-invalid @enderror">
                    <div class="i">
                        <i class='bx bx-lock-alt'></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('New Password') }}</h5>
                        <input type="password" class="input" name="password" id="password" required>
                    </div>
                    <div class="show-password">
                        <i class='bx bx-show' id="toggle-password"></i>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert" style="color:red; font-size: 12px;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="input-div pass">
                    <div class="i">
                        <i class='bx bx-lock'></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('Confirm Password') }}</h5>
                        <input type="password" class="input" name="password_confirmation" id="password-confirm" required>
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
        // Floating input labels
        const inputs = document.querySelectorAll(".input");
        inputs.forEach(input => {
            input.addEventListener("focus", () => {
                input.parentNode.parentNode.classList.add("focus");
            });
            input.addEventListener("blur", () => {
                if (input.value === "") {
                    input.parentNode.parentNode.classList.remove("focus");
                }
            });
        });

        // Toggle password visibility
        const togglePassword = document.getElementById("toggle-password");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            togglePassword.classList.toggle("bx-show");
            togglePassword.classList.toggle("bx-hide");
        });

        const toggleConfirm = document.getElementById("toggle-password-confirm");
        const confirmInput = document.getElementById("password-confirm");

        toggleConfirm.addEventListener("click", () => {
            const type = confirmInput.getAttribute("type") === "password" ? "text" : "password";
            confirmInput.setAttribute("type", type);
            toggleConfirm.classList.toggle("bx-show");
            toggleConfirm.classList.toggle("bx-hide");
        });
    </script>
</body>
</html>
@endsection

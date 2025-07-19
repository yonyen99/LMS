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
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
</head>
<body>
    <img class="wave" src="{{ asset('img/wave.png') }}" alt="Wave Background">
    <div class="container">
        <div class="img">
            <img src="{{ asset('img/bg.svg') }}" alt="Background Image">
        </div>
        <div class="reset-content">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <img src="{{ asset('img/log.png') }}" alt="User Avatar">
                <h2 class="title">Reset Password</h2>
                <p>Enter your email to receive a reset link</p>

                @if (session('status'))
                    <div class="alert-success" role="alert">
                        <i class='bx bx-check-circle'></i>
                        {{ session('status') }}
                    </div>
                @endif

                <div class="input-div @error('email') is-invalid @enderror">
                    <div class="i">
                        <i class='bx bx-envelope' style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="div">
                        <h5>{{ __('Email Address') }}</h5>
                        <input type="email" class="input" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn">{{ __('Send Password Reset Link') }}</button>

                <a href="{{ route('login') }}" class="back-to-login">{{ __('Back to Login') }}</a>
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
    </script>
</body>
</html>
@endsection
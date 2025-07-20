<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>List management system</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .bg-auth {
            background-color: #f8f9fa;
        }

       

        .auth-card {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .auth-header {
            background: transparent;
            border-bottom: 0;
            padding: 1.5rem 1.5rem 0;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo img {
            height: 60px;
        }

        .btn-primary {
            background-color: #3f80ea;
            border-color: #3f80ea;
        }

        .btn-primary:hover {
            background-color: #2f6fd8;
            border-color: #2f6fd8;
        }

        /* New navbar styles */
        .navbar-brand img {
            height: 40px;
            border-radius: 5px;
        }

        .navbar-nav .nav-link {
            margin-right: 15px;
            font-weight: 500;
            color: #444;
        }

        .navbar-nav .nav-link:hover {
            color: #3f80ea;
        }

        .navbar .dropdown-menu {
            min-width: 150px;
        }
    </style>
</head>
<body class="bg-auth">
<div id="app">
    @unless(Request::is('login*', 'register*', 'password/*', 'email/verify*', 'verification*'))
    <nav class="navbar navbar-expand-md navbar-light shadow-sm px-4 text-light" style="background-color: #3097D1;">

        <div class="container-fluid d-flex justify-content-between align-items-center">

            <!-- Left: Logo + Navigation -->
            <div class="d-flex align-items-center">
                <a class="navbar-brand me-4 text-light fw-bold d-flex align-items-center" href="/">
                    {{-- <img src="images/logo.png" alt="Logo" class="me-2"> --}}
                    NGO Forum
                </a>

                <ul class="navbar-nav d-flex flex-row">
    @auth
        @canany(['create-role', 'edit-role', 'delete-role'])
            <li class="nav-item dropdown">
                <a class="nav-link text-light dropdown-toggle" href="#" id="permissionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Permission
                </a>
                <ul class="dropdown-menu" aria-labelledby="permissionDropdown">
                    <li><a class="dropdown-item" href="{{ route('roles.index') }}">Manage Roles</a></li>
                    <li><a class="dropdown-item" href="{{ route('users.index') }}">Manage User</a></li>
                    <li><a class="dropdown-item" href="{{ route('departments.index') }}">Manage Department</a></li>
                </ul>
            </li>
        @endcanany

        @canany(['create-user', 'edit-user', 'delete-user'])
            <li class="nav-item dropdown">
                <a class="nav-link text-light dropdown-toggle" href="#" id="requestDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Request
                </a>
                <ul class="dropdown-menu" aria-labelledby="requestDropdown">
                    <li><a class="dropdown-item" href="">User Requests</a></li>
                    <li><a class="dropdown-item" href="">Approved Users</a></li>
                </ul>
            </li>
        @endcanany

        @canany(['create-product', 'edit-product', 'delete-product'])
            <li class="nav-item dropdown">
                <a class="nav-link text-light dropdown-toggle" href="#" id="calendarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Calendars
                </a>
                <ul class="dropdown-menu text-light" aria-labelledby="calendarDropdown">
                    <li><a class="dropdown-item" href="">All Calendars</a></li>
                    <li><a class="dropdown-item" href="">Add Calendar</a></li>
                </ul>
            </li>

            <!-- Button styled as a nav item -->
            <li class="nav-item ms-3">
                <a href="#" class="btn btn-warning text-light">New Request</a>
            </li>
        @endcanany
    @endauth
</ul>

            </div>

            <!-- Right: User Profile -->
<ul class="navbar-nav ms-auto">
    @guest
        @if (Route::has('login'))
            <li class="nav-item">
                <a class="nav-link text-light" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
        @endif
    @else
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-person-circle me-2" style="font-size: 1.4rem;"></i>
                {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a href="#" class="dropdown-item">
                    <i class="bi bi-person-circle me-2"></i>
                    View Profile
                </a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    @endguest
</ul>
        </div>
    </nav>
    @endunless

    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center mt-3">
                <div class="col-md-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success text-center" role="alert">
                            {{ $message }}
                        </div>
                    @endif

                    <h3 class="text-center mt-3 mb-3">Admin Dashboard</h3>
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>List Management System</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.avif') }}" type="image/avif">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])

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

        /* Navbar styles */
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

        /* Ensure content is not hidden under fixed navbar */
        body {
            padding-top: 70px; /* Adjust based on navbar height */
        }
    </style>
</head>

<body class="bg-auth">
    <div id="app">
        @unless (Request::is('login*', 'register*', 'password/*', 'email/verify*', 'verification*'))
            <nav class="navbar navbar-expand-md navbar-light shadow-sm px-4 fixed-top" style="background-color: #fff;">
                <div class="container-fluid">
                    <!-- Brand -->
                    <a class="navbar-brand d-flex align-items-center fw-bold" href="/">
                        <img src="{{ asset('img/logo.avif') }}" alt="Logo" class="me-2"
                            style="height: 40px; border-radius: 5px;">
                    </a>

                    <!-- Toggler -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Collapsible Nav -->
                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <!-- Left Side Navigation -->
                        <ul class="navbar-nav me-auto align-items-center">
                            @auth
                                <!-- Sidebar Icon -->
                                <li class="nav-item me-3">
                                    <i class="bi bi-justify fs-5"></i>
                                </li>

                                <!-- Permissions Dropdown -->
                                @canany(['create-role', 'edit-role', 'delete-role'])
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link dropdown-toggle" href="#" id="permissionDropdown" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Permissions
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="permissionDropdown">
                                            <li><a class="dropdown-item" href="{{ route('roles.index') }}">Manage Roles</a></li>
                                            <li><a class="dropdown-item" href="{{ route('users.index') }}">Manage Users</a></li>
                                            <li><a class="dropdown-item" href="{{ route('departments.index') }}">Manage
                                                    Departments</a></li>
                                        </ul>
                                    </li>
                                @endcanany

                                <!-- Requested Dropdown -->
                                @canany(['create-user', 'edit-user', 'delete-user'])
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link dropdown-toggle" href="#" id="requestDropdown" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Requested
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="requestDropdown">
                                            <li><a class="dropdown-item" href="#">User Requests</a></li>
                                            <li><a class="dropdown-item" href="#">Approved Users</a></li>
                                        </ul>
                                    </li>
                                @endcanany

                                <!-- Calendars Dropdown -->
                                @canany(['create-department', 'edit-department', 'delete-department', 'create-user',
                                    'edit-user', 'delete-user'])
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link dropdown-toggle" href="#" id="calendarDropdown" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Calendars
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="calendarDropdown">
                                            <li><a class="dropdown-item" href="#">All Calendars</a></li>
                                            <li><a class="dropdown-item" href="#">Add Calendar</a></li>
                                        </ul>
                                    </li>

                                    <!-- New Request Button -->
                                    <li class="nav-item me-2">
                                        <a href="#" class="btn btn-warning fw-semibold rounded text-white px-3 py-1"
                                            style="background: #F5811E;">New Request</a>
                                    </li>
                                @endcanany
                            @endauth
                        </ul>

                        <!-- Right Side User Dropdown -->
                        <ul class="navbar-nav ms-auto">
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center"
                                        href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        @if (Auth::user()->images)
                                            <img src="{{ asset('storage/' . Auth::user()->images) }}" alt="Profile"
                                                class="rounded-circle me-2"
                                                style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <i class="bi bi-person-circle me-2 fs-5"></i>
                                        @endif
                                        {{ Auth::user()->name }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a href="#" class="dropdown-item">
                                            <i class="bi bi-person-circle me-2"></i> View Profile
                                        </a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        @endunless

        <main >
            <div class="p-4">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success text-center" role="alert">
                                {{ $message }}
                            </div>
                        @endif
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
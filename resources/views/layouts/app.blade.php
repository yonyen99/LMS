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
    <link rel="icon" href="{{ asset('img/logo.avif') }}" type="image/avif">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

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

        /* Active page styling */
        .nav-link.active {
            color: #3f80ea !important;
            font-weight: 600;
            border-bottom: 2px solid #3f80ea;
        }
    </style>
</head>

<body class="bg-auth">
    <div id="app">
        @unless (Request::is('login*', 'register*', 'password/*', 'email/verify*', 'verification*'))
            <nav class="navbar navbar-expand-md navbar-light shadow-sm px-4 position-sticky w-100 t-0"
                style="background-color: #fff; top: 0; z-index: 1030;">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center fw-bold" href="/">
                        <img src="{{ asset('img/logo.avif') }}" alt="Logo" class="me-2"
                            style="height: 60px; border-radius: 5px;">
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <ul class="navbar-nav me-auto align-items-center">
                            @auth
                                <li class="nav-item me-3">
                                    <i class="bi bi-justify fs-5"></i>
                                </li>

                                <!-- Dashboard (only show if user is not Employee or Manager) -->
                                @unless (Auth::user()->hasRole('Employee') || Auth::user()->hasRole('Manager'))
                                    <li class="nav-item me-3">
                                        <a class="nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}"
                                            href="/">
                                            Dashboard
                                        </a>
                                    </li>
                                @endunless

                                <!-- Permissions Dropdown -->
                                @canany(['create-role', 'edit-role', 'delete-role'])
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['roles.index', 'users.index', 'departments.index', 'leave-types.index']) ? 'active' : '' }}"
                                            href="#" id="permissionDropdown" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Permissions
                                        </a>
                                        <ul class="dropdown-menu card-1 card-2" aria-labelledby="permissionDropdown">
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'roles.index' ? 'active' : '' }}"
                                                    href="{{ route('roles.index') }}">Manage Roles</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'users.index' ? 'active' : '' }}"
                                                    href="{{ route('users.index') }}">Manage Users</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'departments.index' ? 'active' : '' }}"
                                                    href="{{ route('departments.index') }}">Manage Departments</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'leave-types.index' ? 'active' : '' }}"
                                                    href="{{ route('leave-types.index') }}">Manage Leave Types</a></li>
                                        </ul>
                                    </li>
                                @endcanany

                                <!-- Approval Section -->
                                @unless (Auth::user()->hasRole('Employee'))
                                    @canany(['create-user', 'edit-user', 'delete-user', 'create-request', 'edit-request',
                                        'delete-request', 'view-request', 'cancel-request'])
                                        <li class="nav-item dropdown me-3">
                                            <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['notifications.index', 'subordinates.index']) ? 'active' : '' }}"
                                                href="#" id="approvalDropdown" role="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                Approval
                                            </a>
                                            <ul class="dropdown-menu card-1 card-2" aria-labelledby="approvalDropdown">
                                                <li><a class="dropdown-item {{ Route::currentRouteName() === 'delegations.index' ? 'active' : '' }}"
                                                        href="#" disabled>Delegations</a></li>
                                                <li><a class="dropdown-item {{ Route::currentRouteName() === 'subordinates.index' ? 'active' : '' }}"
                                                        href="{{ route('subordinates.index') }}">My Subordinates</a></li>
                                                <li><a class="dropdown-item {{ Route::currentRouteName() === 'leave-balance.index' ? 'active' : '' }}"
                                                        href="#" disabled>Leave Balance</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <h6 class="dropdown-header">APPROVAL</h6>
                                                </li>
                                                <li><a class="dropdown-item {{ Route::currentRouteName() === 'notifications.index' ? 'active' : '' }}"
                                                        href="{{ route('notifications.index') }}">Leave Requests</a></li>
                                                <li><a class="dropdown-item {{ Route::currentRouteName() === 'overtime.index' ? 'active' : '' }}"
                                                        href="#" disabled>Overtime</a></li>
                                            </ul>
                                        </li>
                                    @endcanany
                                @endunless

                                <!-- Requested Dropdown -->
                                @canany(['create-user', 'edit-user', 'delete-user', 'create-request', 'edit-request',
                                    'delete-request', 'view-request', 'cancel-request'])
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['counters.index', 'leave-requests.index']) || (Route::currentRouteName() === 'leave-requests.create' && !in_array(Route::currentRouteName(), ['leave-requests.calendar'])) ? 'active' : '' }}"
                                            href="#" id="requestDropdown" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Requested
                                        </a>
                                        <ul class="dropdown-menu card-1 card-2" aria-labelledby="requestDropdown">
                                            <li>
                                                <h6 class="dropdown-header">LEAVES</h6>
                                            </li>
                                            @php
                                                $allowedRoles = ['Super Admin', 'Admin', 'HR'];
                                                $userRoles = auth()
                                                    ->user()
                                                    ->roles()
                                                    ->pluck('name')
                                                    ->map(fn($r) => strtolower($r))
                                                    ->toArray();
                                                $allowedRolesLower = array_map('strtolower', $allowedRoles);
                                                $isAdmin = count(array_intersect($userRoles, $allowedRolesLower)) > 0;

                                                $routeName = $isAdmin ? 'leave-summaries.index' : 'user-leave.index';
                                                $isActive = $isAdmin
                                                    ? request()->routeIs('leave-summaries.*')
                                                    : request()->routeIs('user-leave.index');
                                            @endphp

                                            <li>
                                                <a class="dropdown-item {{ $isActive ? 'active' : '' }}"
                                                    href="{{ route($routeName) }}">
                                                    Counters
                                                </a>
                                            </li>

                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'leave-requests.index' ? 'active' : '' }}"
                                                    href="{{ route('leave-requests.index') }}">List of leave requests</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'leave-requests.create' && !in_array(Route::currentRouteName(), ['leave-requests.calendar']) ? 'active' : '' }}"
                                                    href="{{ route('leave-requests.create') }}">Request a leave</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <h6 class="dropdown-header">OVERTIME</h6>
                                            </li>
                                            <li><a class="dropdown-item" href="#">List of OT Worked</a></li>
                                            <li><a class="dropdown-item" href="#">Submit an OT Request</a></li>
                                        </ul>
                                    </li>
                                @endcanany

                                <!-- Calendar Dropdown -->
                                @canany(['create-request', 'edit-request', 'delete-request', 'view-request', 'cancel-request',
                                    'create-department', 'edit-department', 'delete-department'])
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['leave-requests.calendar', 'calendars.yearly', 'calendars.workmates', 'calendars.department', 'calendars.global', 'calendars.tabular']) ? 'active' : '' }}"
                                            href="#" id="calendarDropdown" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Calendar
                                        </a>
                                        <ul class="dropdown-menu card-1 card-2" aria-labelledby="calendarDropdown">
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'leave-requests.calendar' ? 'active' : '' }}"
                                                    href="{{ route('leave-requests.calendar') }}"></i>My Calendar</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'calendars.yearly' ? 'active' : '' }}"
                                                    href="#">Yearly Calendar</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'calendars.workmates' ? 'active' : '' }}"
                                                    href="#">My Workmates</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'calendars.department' ? 'active' : '' }}"
                                                    href="#">Department</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'calendars.global' ? 'active' : '' }}"
                                                    href="#">Global</a></li>
                                            <li><a class="dropdown-item {{ Route::currentRouteName() === 'calendars.tabular' ? 'active' : '' }}"
                                                    href="#">Tabular</a></li>
                                        </ul>
                                    </li>
                                @endcanany

                                <!-- New Request Button -->
                                @canany(['create-request', 'edit-request', 'delete-request', 'view-request', 'cancel-request'])
                                    <li class="nav-item me-2">
                                        <a href="{{ route('leave-requests.create') }}"
                                            class="btn btn-warning fw-semibold text-white rounded px-3 py-1 {{ Route::currentRouteName() === 'leave-requests.create' ? 'active' : '' }}"
                                            style="background: #F5811E">New Request</a>
                                    </li>
                                @endcanany
                            @endauth
                        </ul>

                        <!-- Right Side User Dropdown -->
                        <ul class="navbar-nav ms-auto align-items-center">
                            <!-- Notification Bell Dropdown -->
                            @if (!Auth::user()->hasRole('Employee'))
                                <div class="me-3">
                                    <button id="notificationToggle" class="position-relative p-1 bg-transparent border-0" style="font-size: 20px;">
                                        <i class="bi bi-bell-fill text-primary"></i>
                                        <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; padding: 0.4em 0.6em;">
                                            {{ $requests }}
                                        </span>
                                    </button>
                                </div>
                            @endif


                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ Route::currentRouteName() === 'login' ? 'active' : '' }}"
                                            href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown"
                                        class="nav-link dropdown-toggle d-flex align-items-center {{ Route::currentRouteName() === 'users.show' ? 'active' : '' }}"
                                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        @if (Auth::user()->images)
                                            <img src="{{ asset('storage/' . Auth::user()->images) }}" alt="Profile"
                                                class="rounded-circle me-2"
                                                style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <i class="bi bi-person-circle me-2 fs-5"></i>
                                        @endif
                                        <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end card-1 card-2"
                                        aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item {{ Route::currentRouteName() === 'users.show' ? 'active' : '' }}"
                                                href="{{ route('users.show', Auth::user()->id) }}">
                                                @if (Auth::user()->images)
                                                    <img src="{{ asset('storage/' . Auth::user()->images) }}" alt="Profile"
                                                        class="rounded-circle circle me-2"
                                                        style="width: 25px; height: 25px; object-fit: cover;">
                                                @else
                                                    <i class="bi bi-person-circle me-2 fs-5"></i>
                                                @endif View Profile
                                            </a></li>
                                        <li><a class="dropdown-item {{ Route::currentRouteName() === 'logout' ? 'active' : '' }}"
                                                href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                                    class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}</a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">@csrf</form>
                                        </li>
                                    </ul>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            <div id="notificationContainer" style="display: none; position: absolute; top: 60px; right: 100px; width: 400px; z-index: 1050;">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Notifications</h4>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @forelse ($notifications as $request)
                            <a href="{{ route('notifications.index', $request->id) }}" class="text-decoration-none text-dark">
                                <div class="notification-item position-relative" data-id="{{ $request->id }}" style="cursor: pointer;">
                                    <div class="alert shadow-sm rounded-3 {{ $request->is_read ? 'alert-light' : 'alert-primary' }}" 
                                        style="margint: 10px; transition: all 0.3s ease;">

                                        <div>
                                            <strong>{{ $request->user->name }}</strong> requested 
                                            <strong class="text-info">{{ $request->leaveType->name ?? 'Leave' }}</strong> 
                                            from <strong>{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}</strong>
                                            ({{ $request->start_time }}) to 
                                            <strong>{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</strong>
                                            ({{ $request->end_time }}).
                                        </div>

                                        @if ($request->reason)
                                            <div class="mt-2">Reason: <em>{{ $request->reason }}</em></div>
                                        @endif

                                        <div class="mt-2">
                                            Status: 
                                            <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>

                                        <div class="mt-3">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Submitted: {{ \Carbon\Carbon::parse($request->requested_at ?? $request->created_at)->format('d M Y') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Last updated: {{ \Carbon\Carbon::parse($request->last_changed_at ?? $request->updated_at)->format('d M Y') }}
                                            </small>

                                            @if ($request->read_at)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-eye me-1"></i>
                                                    Read: {{ \Carbon\Carbon::parse($request->read_at)->format('d M Y, g:i A') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No leave requests found.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endunless

        <main>
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
    @section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bellButton = document.getElementById('notificationToggle');
            const container = document.getElementById('notificationContainer');

            function toggleNotification() {
                container.style.display = (container.style.display === 'none' || container.style.display === '') 
                    ? 'block' 
                    : 'none';
            }

            // Toggle on bell click
            if (bellButton && container) {
                bellButton.addEventListener('click', function (event) {
                    event.stopPropagation(); // prevent event from bubbling up
                    toggleNotification();
                });

                // Hide when clicking outside
                document.addEventListener('click', function (event) {
                    if (!container.contains(event.target) && event.target !== bellButton && !bellButton.contains(event.target)) {
                        container.style.display = 'none';
                    }
                });
            }
        });
        document.querySelectorAll('.view-request').forEach(button => {
            button.addEventListener('click', function () {
                // Basic fields
                document.getElementById('modalType').textContent = this.dataset.type || '-';
                document.getElementById('modalDuration').textContent = this.dataset.duration || '-';
                document.getElementById('modalReason').textContent = this.dataset.reason || '-';

                // Format start date and time
                const startDate = this.dataset.startDate || '-';
                const startTime = this.dataset.startTime || '';
                document.getElementById('modalStart').innerHTML = `
                    ${startDate}
                    ${startTime ? `<span class="badge bg-info text-white ms-2 text-capitalize">${startTime}</span>` : ''}
                `;

                // Format end date and time
                const endDate = this.dataset.endDate || '-';
                const endTime = this.dataset.endTime || '';
                document.getElementById('modalEnd').innerHTML = `
                    ${endDate}
                    ${endTime ? `<span class="badge bg-info text-white ms-2 text-capitalize">${endTime}</span>` : ''}
                `;

                // Status badge
                const status = (this.dataset.status || '').toLowerCase();
                const statusMap = {
                    planned: 'secondary',
                    accepted: 'success',
                    requested: 'warning',
                    rejected: 'danger',
                    cancellation: 'danger',
                    canceled: 'danger'
                };
                const badgeClass = statusMap[status] || 'light';

                document.getElementById('modalStatus').innerHTML = `
                    <span class="badge bg-${badgeClass} text-white text-capitalize">${status}</span>
                `;
            });
        });
    </script>
</body>

</html>
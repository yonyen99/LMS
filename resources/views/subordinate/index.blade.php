@extends('layouts.app')

@section('content')
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        .table-row {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .table-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border-color: #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            color: #495057;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .badge-manager {
            background-color: #17a2b8;
        }

        .badge-employee {
            background-color: #6c757d;
        }

        .table-loading {
            position: relative;
            opacity: 0.6;
            pointer-events: none;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @media screen and (max-width: 767px) {
            .d-none.d-md-table-cell {
                display: none !important;
            }
        }
    </style>

    <!-- Card -->
    <div class="card card-1 border-0 p-4 p-md-5 rounded-4 shadow-sm bg-white">
        <!-- Header -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
            <h2 class="fw-bold text-primary mb-0 fs-5 fs-md-4 fs-lg-3">
                <i class="bi bi-people-fill me-2 text-gradient"></i>My Subordinates
            </h2>
            <p class="text-muted small mb-0 text-wrap" style="max-width: 100%;">
                @if (Auth::user()->hasRole(['Admin', 'HR']))
                    View all Managers and Employees across departments. Filter by department below.
                @else
                    View your direct report subordinates in your department.
                @endif
            </p>
        </div>

        <!-- Filters -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-4">
            @if (Auth::user()->hasRole(['Admin', 'HR']))
                <form method="GET" action="{{ route('subordinates.index') }}" id="department-filter-form" class="w-100 w-md-auto">
                    <label for="department-select" class="fw-semibold me-2 mb-1">Department</label>
                    <select name="department_id" id="department-select" class="form-select department-select" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <div class="ms-auto d-flex flex-wrap align-items-center gap-2">
                <form method="GET" action="{{ route('subordinates.index') }}" class="d-inline-block">
                    <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()" data-bs-toggle="tooltip" title="Change number of records per page">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
                <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()" data-bs-toggle="tooltip" title="Refresh the list">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive position-relative" id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr style="border: 1px solid #dee2e6;">
                        <th scope="col" class="fw-bold p-3">#</th>
                        <th scope="col" class="fw-bold p-3">First Name</th>
                        <th scope="col" class="fw-bold p-3">Last Name</th>
                        <th scope="col" class="fw-bold p-3 d-none d-md-table-cell">E-mail</th>
                        <th scope="col" class="fw-bold p-3 d-none d-md-table-cell">Status</th>
                        <th scope="col" class="fw-bold p-3">Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subordinates as $subordinate)
                        <tr class="table-row fade-in">
                            <td class="fw-medium p-3">{{ $subordinates->firstItem() + $loop->index }}</td>
                            <td class="p-3">{{ $subordinate->first_name }}</td>
                            <td class="p-3">{{ $subordinate->last_name }}</td>
                            <td class="p-3 d-none d-md-table-cell">{{ $subordinate->email }}</td>
                            <td class="p-3 d-none d-md-table-cell">
                                @if ($subordinate->is_active)
                                    <span class="badge bg-success" data-bs-toggle="tooltip" title="User is active">Active</span>
                                @else
                                    <span class="badge bg-secondary" data-bs-toggle="tooltip" title="User is inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $role = $subordinate->roles->pluck('name')->first() ?? 'N/A';
                                @endphp
                                <span class="badge {{ $role === 'Manager' ? 'badge-manager' : 'badge-employee' }}"
                                    data-bs-toggle="tooltip"
                                    title="{{ $role === 'Manager' ? 'Manages a team' : 'Team member' }}">
                                    <i class="bi {{ $role === 'Manager' ? 'bi-person-gear' : 'bi-person' }} me-1"></i>
                                    {{ $role }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No subordinates found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mt-4 gap-2">
            <span class="text-muted small">
                Showing {{ $subordinates->firstItem() ?? 0 }} to {{ $subordinates->lastItem() ?? 0 }} of {{ $subordinates->total() }} entries
            </span>
            {{ $subordinates->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#department-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select a department',
                allowClear: true,
                minimumResultsForSearch: 3
            });

            $('#department-filter-form').on('submit', function () {
                $('#table-container').addClass('table-loading');
            });

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
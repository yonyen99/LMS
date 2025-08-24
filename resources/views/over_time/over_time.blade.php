@extends('layouts.app')

@section('title', 'Overtime Work List')

@section('content')
    <div class="container-fluid py-4 shadow-lg p-4 round-5">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col">
                <h1 class="h3 mb-0 fw-bold text-primary">
                    <i class="bi bi-clock-history me-2"></i>Overtime Work List
                </h1>
                <p class="text-muted mb-0">Manage and review employee overtime requests</p>
            </div>
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-1" type="button"
                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download"></i>
                        <span class="d-none d-sm-inline">Export</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="exportDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('over-time.exportExcel', request()->query()) }}">
                                <i class="bi bi-file-earmark-excel text-success"></i>
                                <span>Export to Excel</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('over-time.exportPDF', request()->query()) }}">
                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                <span>Export to PDF</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body filter-section">
                <form method="GET" action="{{ route('over-time.list') }}">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label for="search" class="form-label small text-uppercase fw-bold">Search</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}" placeholder="Name, department or reason">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label for="department_id" class="form-label small text-uppercase fw-bold">Department</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">All Departments</option>
                                @foreach ($departments as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ request('department_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label for="status" class="form-label small text-uppercase fw-bold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="rejected_canceled"
                                    {{ request('status') == 'rejected_canceled' ? 'selected' : '' }}>Rejected/Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label for="date" class="form-label small text-uppercase fw-bold">Date</label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ request('date') }}">
                        </div>

                        <div class="col-lg-2 col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 me-2">
                                <i class="bi bi-search me-1"></i> Apply
                            </button>
                            <a href="{{ route('over-time.list') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Section --}}
        @if ($overtimes->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Employee</th>
                            <th scope="col" class="d-none d-md-table-cell">Department</th>
                            <th scope="col" class="d-none d-lg-table-cell">Date</th>
                            <th scope="col" class="d-none d-lg-table-cell text-center">Hours</th>
                            <th scope="col" class="d-none d-xl-table-cell">Reason</th>
                            <th scope="col" class="d-none d-sm-table-cell text-center">Status</th>
                            <th scope="col" class="d-none d-md-table-cell">Action By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overtimes as $ot)
                            <tr class="position-relative">
                                <td class="text-center fw-semibold">
                                    {{ ($overtimes->currentPage() - 1) * $overtimes->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-text bg-primary text-white rounded-circle">
                                                    {{ substr($ot->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-medium">{{ $ot->user->name }}</span>
                                            <small class="text-muted d-md-none d-block">{{ $ot->department->name }}</small>
                                            <small class="text-muted d-lg-none d-block">
                                                {{ \Carbon\Carbon::parse($ot->overtime_date)->format('M d, Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $ot->department->name }}</td>
                                <td class="d-none d-lg-table-cell">
                                    {{ \Carbon\Carbon::parse($ot->overtime_date)->format('M d, Y') }}
                                </td>
                                <td class="d-none d-lg-table-cell text-center">
                                    {{ \Carbon\Carbon::parse($ot->start_time)->diffInHours(\Carbon\Carbon::parse($ot->end_time)) }}
                                    <small class="text-muted">hours</small>
                                </td>
                                <td class="d-none d-xl-table-cell text-truncate" style="max-width: 200px;">
                                    {{ $ot->reason ?: 'No reason provided' }}
                                </td>
                                <td class="d-none d-sm-table-cell text-center">
                                    @if ($ot->status === 'approved')
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Approved
                                        </span>
                                    @elseif($ot->status === 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="bi bi-clock-history me-1"></i>Pending
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
                                            <i class="bi bi-x-circle-fill me-1"></i>{{ ucfirst($ot->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if ($ot->actionBy)
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="avatar avatar-xs">
                                                    <span class="avatar-text bg-secondary text-white rounded-circle">
                                                        {{ substr($ot->actionBy->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                {{ $ot->actionBy->name }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <nav aria-label="Page navigation" class="mt-4 d-flex justify-content-end">
                <ul class="pagination justify-content-center flex-wrap">
                    {{-- First Page Link --}}
                    <li class="page-item {{ $overtimes->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->url(1) }}" aria-label="First">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>

                    {{-- Previous Page Link --}}
                    <li class="page-item {{ $overtimes->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    {{-- Page Numbers --}}
                    @php
                        $start = max(1, $overtimes->currentPage() - 2);
                        $end = min($overtimes->lastPage(), $overtimes->currentPage() + 2);
                    @endphp

                    @if ($start > 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ $overtimes->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $overtimes->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($end < $overtimes->lastPage())
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    <li class="page-item {{ !$overtimes->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>

                    {{-- Last Page Link --}}
                    <li class="page-item {{ !$overtimes->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->url($overtimes->lastPage()) }}" aria-label="Last">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        @else
            <div class="alert alert-info text-center py-4">
                <div class="d-flex flex-column align-items-center">
                    <i class="bi bi-clock-history fs-1 mb-3"></i>
                    <h5 class="mb-1">No overtime records found</h5>
                    <p class="mb-0">There are currently no overtime requests to display</p>
                </div>
            </div>
        @endif
    </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Make table rows clickable for viewing details
            document.querySelectorAll('tbody tr').forEach(row => {
                // Skip if the click is on a button or action cell
                row.addEventListener('click', (e) => {
                    if (!e.target.closest('button') && !e.target.closest('.actions')) {
                        // Handle row click (e.g., view details)
                        console.log('View overtime details for row');
                    }
                });
            });

            // Export functionality
            document.querySelectorAll('.export-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const format = this.getAttribute('data-format');
                    console.log(`Exporting to ${format} format`);
                    // Add your export logic here
                });
            });
        });
    </script>
@endsection

<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }

    .avatar-xs {
        width: 24px;
        height: 24px;
        font-size: 0.75rem;
    }

    .avatar-text {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .dropdown-menu {
        min-width: 200px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        cursor: pointer;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
</style>

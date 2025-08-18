@extends('layouts.app')

@section('title', 'Overtime Requests')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Header Section -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h1 class="h4 mb-0 fw-bold">
                            <i class="bi bi-clock-history me-2"></i> Overtime Requests
                        </h1>
                        <p class="text-muted mb-0">Track and manage employee overtime requests</p>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3">
                        <!-- Search Form -->
                        <form class="d-flex" method="GET" action="{{ route('over-time.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search..."
                                    value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>

                            </div>
                        </form>

                        <!-- Export Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('over-time.exportPDF') . '?' . http_build_query(request()->query()) }}">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('over-time.exportExcel') . '?' . http_build_query(request()->query()) }}">
                                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards (Unfiltered Totals) -->
                <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
                    <div class="col">
                        <div class="card border-start border-primary border-4 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                        <i class="bi bi-clock text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Total Requests</p>
                                        <h3 class="mb-0">{{ $totalRequests }}</h3>
                                        <small class="text-muted">All time</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-start border-success border-4 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                        <i class="bi bi-check-circle text-success fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Approved</p>
                                        <h3 class="mb-0">{{ $approvedRequests }}</h3>
                                        <small class="text-muted">
                                            {{ $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0 }}%
                                            of total
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-start border-warning border-4 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                        <i class="bi bi-hourglass-top text-warning fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Pending</p>
                                        <h3 class="mb-0">{{ $pendingRequests }}</h3>
                                        <small class="text-muted">
                                            {{ $totalRequests > 0 ? round(($pendingRequests / $totalRequests) * 100, 1) : 0 }}%
                                            of total
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-start border-danger border-4 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                                        <i class="bi bi-x-circle text-danger fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Rejected/Canceled</p>
                                        <h3 class="mb-0">{{ $rejectedCancelledRequests }}</h3>
                                        <small class="text-muted">
                                            {{ $totalRequests > 0 ? round(($rejectedCancelledRequests / $totalRequests) * 100, 1) : 0 }}%
                                            of total
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Filter Tabs -->
                <ul class="nav nav-tabs mb-4" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ !request('status') ? 'active' : '' }}"
                            href="{{ route('over-time.index', array_merge(request()->except(['status', 'page']), ['status' => null, 'page' => null])) }}">
                            All Requests
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ request('status') === 'requested' ? 'active' : '' }}"
                            href="{{ route('over-time.index', array_merge(request()->except(['status', 'page']), ['status' => 'requested', 'page' => null])) }}">
                            Pending
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('over-time.index', array_merge(request()->except(['status', 'page']), ['status' => 'approved', 'page' => null])) }}">
                            Approved
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ in_array(request('status'), ['rejected', 'cancelled']) ? 'active' : '' }}"
                            href="{{ route('over-time.index', array_merge(request()->except(['status', 'page']), ['status' => 'rejected_canceled', 'page' => null])) }}">
                            Rejected/Canceled
                        </a>
                    </li>
                </ul>

                <!-- Table (Filtered Results) -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Employee</th>
                                <th class="d-none d-md-table-cell">Department</th>
                                <th class="d-none d-lg-table-cell">Date</th>
                                <th class="d-none d-lg-table-cell">Time Period</th>
                                <th class="d-none d-sm-table-cell text-center">Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($overtimes as $ot)
                                <tr>
                                    <td>{{ ($overtimes->currentPage() - 1) * $overtimes->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-sm rounded-circle me-2">
                                                    <span class="avatar-text bg-primary p-2 text-white rounded-circle">
                                                        {{ substr($ot->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $ot->user->name }}</div>
                                                <small class="text-muted d-md-none">{{ $ot->department->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $ot->department->name }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ \Carbon\Carbon::parse($ot->overtime_date)->format('M d, Y') }}
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ ucwords(str_replace('_', ' ', $ot->time_period)) }}
                                        <small class="text-muted d-block">{{ $ot->duration }} hours</small>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-center">
                                        <span
                                            class="badge rounded-pill bg-{{ $ot->status == 'approved' ? 'success' : ($ot->status == 'requested' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($ot->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('over-time.show', $ot->id) }}">
                                                        <i class="bi bi-eye me-2"></i> View Details
                                                    </a>
                                                </li>

                                                @if (auth()->user()->id === $ot->user_id && $ot->status === 'requested')
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('over-time.edit', $ot->id) }}">
                                                            <i class="bi bi-pencil me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('over-time.destroy', $ot->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item text-danger btn-confirm"
                                                                data-message="Are you sure you want to delete this request?">
                                                                <i class="bi bi-trash me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('over-time.cancel', $ot->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="dropdown-item text-warning btn-confirm"
                                                                data-message="Are you sure you want to cancel this request?">
                                                                <i class="bi bi-slash-circle me-2"></i> Cancel
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif

                                                @if (auth()->user()->hasAnyRole(['Manager', 'Admin']) && $ot->status === 'requested')
                                                    <li>
                                                        <form action="{{ route('over-time.accept', $ot->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="dropdown-item text-success btn-confirm"
                                                                data-message="Approve this overtime request?">
                                                                <i class="bi bi-check-circle me-2"></i> Approve
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('over-time.reject', $ot->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="dropdown-item text-danger btn-confirm"
                                                                data-message="Reject this overtime request?">
                                                                <i class="bi bi-x-circle me-2"></i> Reject
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-clock-history fs-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">No overtime requests found</h5>
                                            @if (request('search') || request('status'))
                                                <a href="{{ route('over-time.index') }}"
                                                    class="btn btn-sm btn-outline-primary mt-2">
                                                    Clear filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $overtimes->firstItem() }} to {{ $overtimes->lastItem() }} of
                        {{ $overtimes->total() }} entries
                    </div>
                    <div>
                        {{ $overtimes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // SweetAlert confirmation for buttons
            document.querySelectorAll('.btn-confirm').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    let form = this.closest('form');
                    let message = this.getAttribute('data-message') || "Are you sure?";

                    Swal.fire({
                        title: 'Confirmation',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

@endsection

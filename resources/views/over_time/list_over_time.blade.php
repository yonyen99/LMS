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
                        <form class="d-flex" method="GET">
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
                                    <a class="dropdown-item" href="{{ route('over-time.exportPDF') }}">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('over-time.exportExcel') }}">
                                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
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
                                        <small
                                            class="text-muted">{{ $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0 }}%
                                            of total</small>
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
                                        <small
                                            class="text-muted">{{ $totalRequests > 0 ? round(($pendingRequests / $totalRequests) * 100, 1) : 0 }}%
                                            of total</small>
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
                                        {{-- <h3 class="mb-0">{{ $rejectedCancelledRequests }}</h3> --}}
                                        {{-- <small class="text-muted">{{ $totalRequests > 0 ? round(($rejectedCancelledRequests/$totalRequests)*100, 1) : 0 }}% of total</small> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Filter Tabs -->
                <ul class="nav nav-tabs mb-4" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('status') === null ? 'active' : '' }}" id="all-tab"
                            data-bs-toggle="tab" type="button" role="tab">
                            All Requests
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('status') === 'requested' ? 'active' : '' }}" id="pending-tab"
                            data-bs-toggle="tab" type="button" role="tab">
                            Pending
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" id="approved-tab"
                            data-bs-toggle="tab" type="button" role="tab">
                            Approved
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ in_array(request('status'), ['rejected', 'canceled']) ? 'active' : '' }}"
                            id="rejected-tab" data-bs-toggle="tab" type="button" role="tab">
                            Rejected/Canceled
                        </button>
                    </li>
                </ul>

                <!-- Table -->
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
                                        {{ \Carbon\Carbon::parse($ot->overtime_date)->format('M d, Y') }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        {{ ucwords(str_replace('_', ' ', $ot->time_period)) }}
                                        <small class="text-muted d-block">{{ $ot->hours }} hours</small>
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
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Are you sure?')">
                                                                <i class="bi bi-trash me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('over-time.cancel', $ot->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-warning"
                                                                onclick="return confirm('Are you sure you want to cancel this request?')">
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
                                                            <button type="submit" class="dropdown-item text-success"
                                                                onclick="return confirm('Approve this overtime request?')">
                                                                <i class="bi bi-check-circle me-2"></i> Approve
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('over-time.reject', $ot->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Reject this overtime request?')">
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
                        {{ $overtimes->links() }}
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
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Status filter tabs functionality
            const statusTabs = document.querySelectorAll('#statusTabs .nav-link');
            statusTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const statusMap = {
                        'all-tab': '',
                        'pending-tab': 'requested',
                        'approved-tab': 'approved',
                        'rejected-tab': 'rejected_canceled'
                    };

                    const status = statusMap[this.id];
                    const url = new URL(window.location.href);

                    if (status) {
                        url.searchParams.set('status', status);
                    } else {
                        url.searchParams.delete('status');
                    }

                    window.location.href = url.toString();
                });
            });
        });
    </script>
@endsection

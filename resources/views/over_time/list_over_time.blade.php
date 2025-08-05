@extends('layouts.app')

@section('title', 'Overtime Requests')

@section('styles')
    <style>
        .transition {
            transition: background-color 0.2s ease-in-out;
        }

        .transition:hover {
            background-color: #f8f9fa;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
@endsection

@section('content')
    <div class="container px-4 py-5 w-100 shadow-sm p-4 rounded bg-white">

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <h1 class="h3 fw-bold mb-3 mb-md-0">Overtime Requests</h1>

            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 w-40 w-md-auto">
                {{-- Search --}}
                <form method="GET" class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search....."
                        value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit" title="Search">
                        Search
                    </button>
                </form>

                <!-- Buttons -->
                <div class="d-flex gap-2 mt-2 mt-md-0">
                    <a href="{{route('over-time.exportPDF')}}" class="btn btn-primary">PDF</a>
                    @if (auth()->user()->hasAnyRole(['Employee', 'Manager', 'Admin']))
                        <a href="{{route('over-time.exportExcel')}}" class="btn btn-success">EXCEL</a>
                    @endif
                </div>
            </div>
        </div>



        <!-- Statistics Cards -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
            <div class="col">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-2 bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                            style="width:50px; height:50px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24" width="24" height="24"
                                class="text-primary">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="card-text small text-muted mb-1">Total Requests</p>
                            <p class="card-title h5 fw-semibold">{{ $totalRequests }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-2 bg-success bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                            style="width:50px; height:50px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24" width="24" height="24"
                                class="text-success">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="card-text small text-muted mb-1">Approved</p>
                            <p class="card-title h5 fw-semibold">{{ $approvedRequests }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-2 bg-warning bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                            style="width:50px; height:50px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24" width="24" height="24"
                                class="text-warning">
                                <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="card-text small text-muted mb-1">Pending</p>
                            <p class="card-title h5 fw-semibold">{{ $pendingRequests }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overtime Requests Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="60px">#</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="d-none d-md-table-cell">Department</th>
                        <th scope="col" class="d-none d-md-table-cell">Request Date</th>
                        <th scope="col" class="d-none d-md-table-cell">Start Time</th>
                        <th scope="col" class="d-none d-md-table-cell">End Time</th>
                        <th scope="col" class="d-none d-md-table-cell">Status</th>
                        <th scope="col" width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($overtimes as $ot)
                        <tr class="transition">
                            <th scope="row">
                                {{ ($overtimes->currentPage() - 1) * $overtimes->perPage() + $loop->iteration }}</th>
                            <td>{{ $ot->user->name }}</td>
                            <td class="d-none d-md-table-cell">{{ $ot->department->name }}</td>
                            <td class="d-none d-md-table-cell">{{ $ot->overtime_date }}</td>
                            <td class="d-none d-md-table-cell">{{ $ot->start_time }}</td>
                            <td class="d-none d-md-table-cell">{{ $ot->end_time }}</td>
                            <td class="d-none d-md-table-cell">
                                <span
                                    class="badge bg-{{ $ot->status == 'approved' ? 'success' : ($ot->status == 'requested' ? 'warning' : ($ot->status == 'rejected' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($ot->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="actionsDropdown{{ $ot->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm"
                                        aria-labelledby="actionsDropdown{{ $ot->id }}">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="{{ route('over-time.show', $ot->id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
                                        @if (auth()->user()->id === $ot->user_id && $ot->status === 'requested')
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2"
                                                    href="{{ route('over-time.edit', $ot->id) }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('over-time.destroy', $ot->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                        type="submit">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('over-time.cancel', $ot->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                    @csrf
                                                    <button
                                                        class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                        type="submit">
                                                        <i class="bi bi-slash-circle"></i> Cancel
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if (auth()->user()->hasAnyRole(['Manager', 'Admin']) &&
                                                $ot->status === 'requested' &&
                                                (auth()->user()->hasRole('Admin') ||
                                                    (auth()->user()->hasRole('Manager') && $ot->user->department_id === auth()->user()->department_id)))
                                            <li>
                                                <form action="{{ route('over-time.accept', $ot->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to approve this request?');">
                                                    @csrf
                                                    <button
                                                        class="dropdown-item d-flex align-items-center gap-2 text-success"
                                                        type="submit">
                                                        <i class="bi bi-check-circle"></i> Accept
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('over-time.reject', $ot->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to reject this request?');">
                                                    @csrf
                                                    <button
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                        type="submit">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('over-time.cancel', $ot->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                    @csrf
                                                    <button
                                                        class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                        type="submit">
                                                        <i class="bi bi-slash-circle"></i> Cancel
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
                            <td colspan="8" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-clock fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No overtime requests found</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            {{ $overtimes->links() }}
        </div>
    </div>
    </div>
@endsection

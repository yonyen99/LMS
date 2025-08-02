@extends('layouts.app')

@section('title', 'Overtime Requests')

@section('content')
    <div class="container px-4 py-5 w-100">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-3 mb-md-0">Overtime Requests</h1>
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-primary">Timeline View</a>
                <a href="{{ route('over-time.create') }}" class="btn btn-success">Add Overtime Request</a>
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
                            <p class="card-title h5 fw-semibold">24</p>
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
                            <p class="card-title h5 fw-semibold">18</p>
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
                            <p class="card-title h5 fw-semibold">4</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overtime Requests List -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">Name</th>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">Department</th>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">Request Date</th>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">Start Time</th>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">End Time</th>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">Status</th>
                                <th class="px-4 py-3 text-uppercase text-muted fw-medium text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overtimes as $ot)
                                <tr>
                                    <td class="px-4 py-4">{{ $ot->user->name }}</td>
                                    <td class="px-4 py-4">{{ $ot->department }}</td>
                                    <td class="px-4 py-4">{{ $ot->overtime_date }}</td>
                                    <td class="px-4 py-4">{{ $ot->start_time }}</td>
                                    <td class="px-4 py-4">{{ $ot->end_time }}</td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="badge bg-{{ $ot->status == 'approved' ? 'success' : ($ot->status == 'pending' ? 'warning' : ($ot->status == 'rejected' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($ot->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('over-time.edit', $ot->id) }}">Edit</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('over-time.show', $ot->id) }}">View</a></li>

                                                <li>
                                                    <form action="{{ route('over-time.destroy', $ot->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger"
                                                            type="submit">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

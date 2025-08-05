@extends('layouts.app')

@section('title', 'Overtime Work List')

@section('styles')
    <style>
        .transition-all {
            transition: all 0.3s ease-in-out;
        }

        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .btn-primary,
        .btn-success {
            font-weight: 500;
        }

        .pagination .page-link {
            border-radius: 0.375rem;
        }

        .pagination .active>.page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .table-footer {
            background-color: #f1f3f5;
            font-weight: 500;
            text-align: center;
        }

        @media (max-width: 768px) {
            .input-group {
                flex-wrap: nowrap;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container py-5 shadow-sm w-full rounded bg-white p-4">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark m-0">Overtime Work List</h2>

            <div class="d-flex flex-column flex-md-row align-items-stretch gap-3 mt-3 mt-md-0 w-40 w-md-auto">
                {{-- Search --}}
                <form method="GET" class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or dept"
                        value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit" title="Search">
                        Search
                    </button>
                </form>

                {{-- Export Buttons --}}
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-success d-flex align-items-center gap-2 transition-all hover-scale">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                    <a href="#" class="btn btn-primary d-flex align-items-center gap-2 transition-all hover-scale">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        @if ($overtimes->count())
            @foreach ($overtimes->chunk(10) as $chunkIndex => $chunk)
                <!-- Overtime Requests Table -->
                <div class="table-responsive mb-4 shadow-sm rounded">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th scope="col" width="60px">#</th>
                                <th scope="col">Employee Name</th>
                                <th scope="col" class="d-none d-md-table-cell">Department</th>
                                <th scope="col" class="d-none d-md-table-cell">Date</th>
                                <th scope="col" class="d-none d-md-table-cell">Total Hours</th>
                                <th scope="col" class="d-none d-md-table-cell">Reason</th>
                                <th scope="col" class="d-none d-md-table-cell">Status</th>
                                <th scope="col" class="d-none d-md-table-cell">Action By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($overtimes as $ot)
                                <tr class="transition">
                                    <td class="text-center fw-semibold">
                                        {{ ($overtimes->currentPage() - 1) * $overtimes->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $ot->user->name }}</td>
                                    <td class="d-none d-md-table-cell text-muted">{{ $ot->department->name }}</td>
                                    <td class="d-none d-md-table-cell">
                                        {{ \Carbon\Carbon::parse($ot->overtime_date)->format('M d, Y') }}</td>
                                    <td class="d-none d-md-table-cell text-center">
                                        {{ \Carbon\Carbon::parse($ot->start_time)->diffInHours(\Carbon\Carbon::parse($ot->end_time)) }}
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $ot->reason ?: 'No reason provided' }}</td>
                                    <td class="d-none d-md-table-cell text-center">
                                        @if ($ot->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($ot->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($ot->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        @if ($ot->actionBy)
                                            <span>{{ $ot->actionBy->name }}</span>
                                        @else
                                            <span class="text-muted fst-italic">Not Assigned</span>
                                        @endif
                                    </td>
                                  
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-clock fs-1 text-muted mb-2"></i>
                                            <h5 class="text-muted">No overtime requests found</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" class="table-footer">Page {{ $overtimes->currentPage() }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endforeach

            {{-- Pagination --}}
            <nav class="mt-4 d-flex justify-content-end">
                <ul class="pagination justify-content-center flex-wrap gap-1">
                    {{-- First --}}
                    <li class="page-item {{ $overtimes->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->url(1) }}" aria-label="First">
                            &laquo;
                        </a>
                    </li>

                    {{-- Prev --}}
                    <li class="page-item {{ $overtimes->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->previousPageUrl() }}" aria-label="Previous">
                            &lsaquo;
                        </a>
                    </li>

                    {{-- Page numbers --}}
                    @for ($i = max(1, $overtimes->currentPage() - 2); $i <= min($overtimes->lastPage(), $overtimes->currentPage() + 2); $i++)
                        <li class="page-item {{ $overtimes->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $overtimes->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    {{-- Next --}}
                    <li class="page-item {{ !$overtimes->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->nextPageUrl() }}" aria-label="Next">
                            &rsaquo;
                        </a>
                    </li>

                    {{-- Last --}}
                    <li class="page-item {{ !$overtimes->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $overtimes->url($overtimes->lastPage()) }}" aria-label="Last">
                            &raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        @else
            <div class="alert alert-info text-center shadow-sm rounded">
                No overtime records found.
            </div>
        @endif
    </div>
@endsection

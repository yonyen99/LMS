@extends('layouts.app')

@section('content')
 {{-- My Summary Section --}}
    <div class="card card-1 border-0 p-4 rounded-3 bg-white shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h2 class="fw-bold text-primary m-0">
                <i class="bi bi-bar-chart-fill me-2"></i>My Leave Summary
            </h2>
            <div class="d-flex align-items-center">
                <label for="dateReport" class="me-2 fw-semibold text-muted small">Report Date</label>
                <input type="date" id="dateReport" value="{{ now()->format('Y-m-d') }}"
                    class="form-control shadow-sm rounded-pill" style="width: 160px;">
            </div>
        </div>
    </div>
    <div class="container-fluid py-2">
        <div class="row">
                  <div class="card card-1 border-0 p-4 rounded-3 bg-white shadow-sm">
                    {{-- Create Button --}}
                    <div class="text-end mb-3">
                        <a href="{{ route('leave-summaries.create') }}" class="btn btn-primary rounded-pill shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Create New
                        </a>
                    </div>

                    {{-- Summary Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Department</th>
                                    <th>Leave Type</th>
                                    <th>Report Date</th>
                                    <th>Entitled Days</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($summaries as $summary)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($summaries->currentPage() - 1) * $summaries->perPage() + $loop->iteration }}
                                        </td>
                                        <td>{{ $summary->department->name ?? '-' }}</td>
                                        <td>{{ $summary->leaveType->name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($summary->report_date)->format('d M, Y') }}</td>
                                        <td class="text-center">{{ $summary->entitled }}</td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button 
                                                    class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill px-3" 
                                                    type="button" 
                                                    id="actionsDropdown{{ $summary->id }}" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false"
                                                >
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $summary->id }}">
                                                    {{-- Edit --}}
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('leave-summaries.edit', $summary->id) }}">
                                                            <i class="bi bi-pencil-square text-warning me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    {{-- Delete --}}
                                                    <li>
                                                        <form action="{{ route('leave-summaries.destroy', $summary->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this summary?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                                                <i class="bi bi-trash me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-info-circle-fill me-2"></i> No leave summaries found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($summaries->hasPages())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-4 gap-2">
                            <div class="text-muted small">
                                Showing {{ $summaries->firstItem() ?? 0 }} to {{ $summaries->lastItem() ?? 0 }} of {{ $summaries->total() }} entries
                            </div>
                            <div>
                                {{ $summaries->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

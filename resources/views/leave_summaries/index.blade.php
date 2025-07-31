@extends('layouts.app')

@section('content')
    {{-- My Summary Section --}}
    <div class="card card-1  border-0 p-4 rounded-3 bg-white" >
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary mb-0" style="font-size: 1.8rem; letter-spacing: 1px;">
                <i class="bi bi-bar-chart-fill me-2"></i>My Leave Summary
            </h2>
            <div class="d-flex align-items-center">
                <label for="dateReport" class="me-2 fw-semibold text-muted small">Report Date</label>
                <input type="date" id="dateReport" value="{{ now()->format('Y-m-d') }}"
                    class="form-control shadow-sm rounded-pill" style="width: 160px; border: 1px solid #ced4da;">
            </div>
        </div>
        @php
            $allowedRoles = ['Super Admin', 'Admin', 'HR'];
        @endphp

        @if(auth()->user()->roles()->pluck('name')->intersect($allowedRoles)->isNotEmpty())
            <div class="mb-3 text-end">
                <a href="{{ route('leave-summaries.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create New
                </a>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Report Date</th>
                        <th>Entitled Days</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($summaries as $summary)
                        <tr>
                            <td>{{ ($summaries->currentPage() - 1) * $summaries->perPage() + $loop->iteration }}</td>
                            <td>{{ $summary->department->name ?? '-' }}</td>
                            <td>{{ $summary->leaveType->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($summary->report_date)->format('d-M-Y') }}</td>
                            <td>{{ $summary->entitled }}</td>
                            <td  class="text-center">
                                <div class="dropdown">
                                    <button 
                                        class="btn btn-sm  btn-outline-secondary dropdown-toggle" 
                                        type="button" 
                                        id="actionsDropdown{{ $summary->id }}" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        aria-label="Actions for summary #{{ $summary->id }}"
                                        style="min-width: 50px;"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu card-1 bg-white dropdown-menu-end" aria-labelledby="actionsDropdown{{ $summary->id }}">
                                        <!-- Edit -->
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('leave-summaries.edit', $summary->id) }}">
                                                <i class="bi bi-pencil-square me-2 text-warning"></i> Edit
                                            </a>
                                        </li>

                                        <!-- Delete -->
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
                            <td colspan="5" class="text-center">No leave summaries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- @if($summaries->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $leaveRequests->firstItem() }} to {{ $leaveRequests->lastItem() }} of {{ $leaveRequests->total() }} entries
                </div>
                <div>
                    {{ $leaveRequests->onEachSide(1)->links() }}
                </div>
            </div>
        @endif --}}
    </div>
@endsection

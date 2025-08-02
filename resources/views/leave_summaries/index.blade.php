@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Leave Summary Management</h5>
                @can('create', App\Models\LeaveSummary::class)
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createLeaveSummaryModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Leave Summary
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            {{-- Dashboard summary card: Total Leave Summaries --}}
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-start border-primary border-3 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-bar-chart-fill text-primary fs-1"></i>
                            <div>
                                <h6 class="text-muted mb-1">Total Leave Summaries</h6>
                                <h4 class="mb-0">{{ $summaries->total() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success message --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="60px">#</th>
                            <th scope="col">Department</th>
                            <th scope="col">Leave Type</th>
                            <th scope="col">Report Date</th>
                            <th scope="col">Entitled Days</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summaries as $summary)
                        <tr>
                            <th scope="row">{{ ($summaries->currentPage() - 1) * $summaries->perPage() + $loop->iteration }}</th>
                            <td>{{ $summary->department->name ?? '-' }}</td>
                            <td>{{ $summary->leaveType->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($summary->report_date)->format('d M, Y') }}</td>
                            <td class="text-center">{{ $summary->entitled }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="actionsDropdown{{ $summary->id }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $summary->id }}">
                                        @can('update', $summary)
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editLeaveSummaryModal{{ $summary->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </li>
                                        @endcan
                                        @can('delete', $summary)
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteLeaveSummaryModal{{ $summary->id }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Leave Summary Modal --}}
                        @can('update', $summary)
                        <div class="modal fade" id="editLeaveSummaryModal{{ $summary->id }}" tabindex="-1" aria-labelledby="editLeaveSummaryModalLabel{{ $summary->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="editLeaveSummaryModalLabel{{ $summary->id }}">Edit Leave Summary</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('leave-summaries.update', $summary->id) }}" method="post" class="needs-validation" novalidate>
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="department_id{{ $summary->id }}" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                                <select name="department_id" id="department_id{{ $summary->id }}" class="form-select @error('department_id') is-invalid @enderror" required>
                                                    <option value="">Select Department</option>
                                                    @if(isset($departments) && $departments->isNotEmpty())
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department->id }}" {{ $summary->department_id == $department->id ? 'selected' : '' }}>
                                                                {{ $department->name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="">No departments available</option>
                                                    @endif
                                                </select>
                                                @error('department_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="leave_type_id{{ $summary->id }}" class="form-label fw-bold">Leave Type <span class="text-danger">*</span></label>
                                                <select name="leave_type_id" id="leave_type_id{{ $summary->id }}" class="form-select @error('leave_type_id') is-invalid @enderror" required>
                                                    <option value="">Select Leave Type</option>
                                                    @if(isset($leaveTypes) && $leaveTypes->isNotEmpty())
                                                        @foreach ($leaveTypes as $type)
                                                            <option value="{{ $type->id }}" {{ $summary->leave_type_id == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="">No leave types available</option>
                                                    @endif
                                                </select>
                                                @error('leave_type_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="report_date{{ $summary->id }}" class="form-label fw-bold">Report Date <span class="text-danger">*</span></label>
                                                <input type="date" name="report_date" id="report_date{{ $summary->id }}" class="form-control @error('report_date') is-invalid @enderror"
                                                       value="{{ old('report_date', \Carbon\Carbon::parse($summary->report_date)->format('Y-m-d')) }}" required>
                                                @error('report_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="entitled{{ $summary->id }}" class="form-label fw-bold">Entitled Days <span class="text-danger">*</span></label>
                                                <input type="number" step="0.5" name="entitled" id="entitled{{ $summary->id }}" class="form-control @error('entitled') is-invalid @enderror"
                                                       value="{{ old('entitled', $summary->entitled) }}" required>
                                                @error('entitled')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Leave Summary</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan

                        {{-- Delete Confirmation Modal --}}
                        @can('delete', $summary)
                        <div class="modal fade" id="deleteLeaveSummaryModal{{ $summary->id }}" tabindex="-1" aria-labelledby="deleteLeaveSummaryModalLabel{{ $summary->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteLeaveSummaryModalLabel{{ $summary->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this leave summary for <strong>{{ $summary->department->name ?? '-' }}</strong>? This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form id="delete-form-{{ $summary->id }}" action="{{ route('leave-summaries.destroy', $summary->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan

                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-bar-chart-fill fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No leave summaries found</h5>
                                    @can('create', App\Models\LeaveSummary::class)
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createLeaveSummaryModal">
                                        <i class="bi bi-plus-circle me-1"></i> Create First Leave Summary
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($summaries->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $summaries->firstItem() ?? 0 }} to {{ $summaries->lastItem() ?? 0 }} of {{ $summaries->total() }} entries
                </div>
                <div>
                    {{ $summaries->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create Leave Summary Modal --}}
    @can('create', App\Models\LeaveSummary::class)
    <div class="modal fade" id="createLeaveSummaryModal" tabindex="-1" aria-labelledby="createLeaveSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createLeaveSummaryModalLabel">Add New Leave Summary</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('leave-summaries.store') }}" method="post" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            This will create leave summaries for all users in the selected department.
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">Select Department</option>
                                @if(isset($departments) && $departments->isNotEmpty())
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">No departments available</option>
                                @endif
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label fw-bold">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" required>
                                <option value="">Select Leave Type</option>
                                @if(isset($leaveTypes) && $leaveTypes->isNotEmpty())
                                    @foreach ($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">No leave types available</option>
                                @endif
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="report_date" class="form-label fw-bold">Report Date <span class="text-danger">*</span></label>
                            <input type="date" name="report_date" id="report_date" class="form-control @error('report_date') is-invalid @enderror"
                                   value="{{ old('report_date', now()->format('Y-m-d')) }}" required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="entitled" class="form-label fw-bold">Entitled Days <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="entitled" id="entitled" class="form-control @error('entitled') is-invalid @enderror"
                                   value="{{ old('entitled') }}" required>
                            @error('entitled')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Leave Summary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .dropdown-toggle::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 10rem;
        border-radius: 0.25rem;
    }
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .modal-content {
        border-radius: 0.5rem;
    }
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>
@endsection

@section('scripts')
<script>
    // Client-side validation
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Add loading state for delete forms
        const deleteForms = document.querySelectorAll('form[id^="delete-form-"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                if (button) {
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...';
                    button.disabled = true;
                }
            });
        });
    });
</script>
@endsection
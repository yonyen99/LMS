@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Leave Type Management</h5>
                @can('create-leave-type')
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createLeaveTypeModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Leave Type
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            {{-- Dashboard summary card: Total Leave Types --}}
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-start border-primary border-3 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-calendar-check text-primary fs-1"></i>
                            <div>
                                <h6 class="text-muted mb-1">Total Leave Types</h6>
                                <h4 class="mb-0">{{ $totalLeaveTypes }}</h4>
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
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaveTypes as $type)
                        <tr>
                            <th scope="row">{{ ($leaveTypes->currentPage() - 1) * $leaveTypes->perPage() + $loop->iteration }}</th>
                            <td>{{ $type->name }}</td>
                            <td>{{ Str::limit($type->description, 50) }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="actionsDropdown{{ $type->id }}" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $type->id }}">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#showLeaveTypeModal{{ $type->id }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </li>
                                        @can('edit-leave-type')
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editLeaveTypeModal{{ $type->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </li>
                                        @endcan
                                        @can('delete-leave-type')
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteLeaveTypeModal{{ $type->id }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- Show Leave Type Modal --}}
                        <div class="modal fade" id="showLeaveTypeModal{{ $type->id }}" tabindex="-1" aria-labelledby="showLeaveTypeModalLabel{{ $type->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="showLeaveTypeModalLabel{{ $type->id }}">Leave Type Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Name:</label>
                                            <p>{{ $type->name }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Description:</label>
                                            <p>{{ $type->description ?: 'No description provided' }}</p>
                                        </div>
                                        @if($type->created_at || $type->updated_at)
                                        <div class="text-muted small">
                                            @if($type->created_at)
                                                <div>Created: {{ $type->created_at->format('M d, Y h:i A') }}</div>
                                            @endif
                                            @if($type->updated_at)
                                                <div>Last Updated: {{ $type->updated_at->format('M d, Y h:i A') }}</div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Leave Type Modal --}}
                        @can('edit-leave-type')
                        <div class="modal fade" id="editLeaveTypeModal{{ $type->id }}" tabindex="-1" aria-labelledby="editLeaveTypeModalLabel{{ $type->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="editLeaveTypeModalLabel{{ $type->id }}">Edit Leave Type</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('leave-types.update', $type->id) }}" method="post" class="needs-validation" novalidate>
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name{{ $type->id }}" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name{{ $type->id }}" name="name" value="{{ old('name', $type->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="description{{ $type->id }}" class="form-label fw-bold">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                                          id="description{{ $type->id }}" name="description" rows="3">{{ old('description', $type->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Leave Type</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan

                        {{-- Delete Confirmation Modal --}}
                        @can('delete-leave-type')
                        <div class="modal fade" id="deleteLeaveTypeModal{{ $type->id }}" tabindex="-1" aria-labelledby="deleteLeaveTypeModalLabel{{ $type->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteLeaveTypeModalLabel{{ $type->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete the leave type <strong>{{ $type->name }}</strong>? This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form id="delete-form-{{ $type->id }}" action="{{ route('leave-types.destroy', $type->id) }}" method="POST" class="d-inline">
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
                            <td colspan="4" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-calendar-check fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No leave types found</h5>
                                    @can('create-leave-type')
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createLeaveTypeModal">
                                        <i class="bi bi-plus-circle me-1"></i> Create First Leave Type
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaveTypes->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $leaveTypes->firstItem() }} to {{ $leaveTypes->lastItem() }} of {{ $leaveTypes->total() }} entries
                </div>
                <div>
                    {{ $leaveTypes->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create Leave Type Modal --}}
    @can('create-leave-type')
    <div class="modal fade" id="createLeaveTypeModal" tabindex="-1" aria-labelledby="createLeaveTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createLeaveTypeModalLabel">Add New Leave Type</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('leave-types.store') }}" method="post" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Leave Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>

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
@endsection
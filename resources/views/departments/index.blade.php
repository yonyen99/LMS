@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Department Management</h5>
                @can('create-department')
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Department
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            {{-- Dashboard summary card: Total Departments --}}
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-start border-primary border-3 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-building text-primary fs-1"></i>
                            <div>
                                <h6 class="text-muted mb-1">Total Departments</h6>
                                <h4 class="mb-0">{{ $totalDepartments }}</h4>
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
                            <th scope="col" class="d-none d-md-table-cell">Description</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                        <tr>
                            <th scope="row">{{ ($departments->currentPage() - 1) * $departments->perPage() + $loop->iteration }}</th>
                            <td>{{ $department->name }}</td>
                            <td class="d-none d-md-table-cell">{{ Str::limit($department->description, 50) }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="actionsDropdown{{ $department->id }}" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $department->id }}">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#showDepartmentModal{{ $department->id }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </li>
                                        @can('edit-department')
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editDepartmentModal{{ $department->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </li>
                                        @endcan
                                        @can('delete-department')
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteDepartmentModal{{ $department->id }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- Show Department Modal --}}
                        <div class="modal fade" id="showDepartmentModal{{ $department->id }}" tabindex="-1" aria-labelledby="showDepartmentModalLabel{{ $department->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="showDepartmentModalLabel{{ $department->id }}">Department Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Name:</label>
                                            <p>{{ $department->name }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Description:</label>
                                            <p>{{ $department->description ?: 'No description provided' }}</p>
                                        </div>
                                        @if($department->created_at || $department->updated_at)
                                        <div class="text-muted small">
                                            @if($department->created_at)
                                                <div>Created: {{ $department->created_at->format('M d, Y h:i A') }}</div>
                                            @endif
                                            @if($department->updated_at)
                                                <div>Last Updated: {{ $department->updated_at->format('M d, Y h:i A') }}</div>
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

                        {{-- Edit Department Modal --}}
                        @can('edit-department')
                        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" aria-labelledby="editDepartmentModalLabel{{ $department->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="editDepartmentModalLabel{{ $department->id }}">Edit Department</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('departments.update', $department->id) }}" method="post" class="needs-validation" novalidate>
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name{{ $department->id }}" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name{{ $department->id }}" name="name" value="{{ old('name', $department->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="description{{ $department->id }}" class="form-label fw-bold">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                                          id="description{{ $department->id }}" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Department</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan

                        {{-- Delete Confirmation Modal --}}
                        @can('delete-department')
                        <div class="modal fade" id="deleteDepartmentModal{{ $department->id }}" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel{{ $department->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteDepartmentModalLabel{{ $department->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete the department <strong>{{ $department->name }}</strong>? This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form id="delete-form-{{ $department->id }}" action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline">
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
                                    <i class="bi bi-building fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No departments found</h5>
                                    @can('create-department')
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                                        <i class="bi bi-plus-circle me-1"></i> Create First Department
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($departments->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} entries
                </div>
                <div>
                    {{ $departments->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create Department Modal --}}
    @can('create-department')
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createDepartmentModalLabel">Add New Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('departments.store') }}" method="post" class="needs-validation" novalidate>
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
                        <button type="submit" class="btn btn-primary">Save Department</button>
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
    @media screen and (max-width: 767px) {
        .d-none.d-md-table-cell {
            display: none !important;
        }
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
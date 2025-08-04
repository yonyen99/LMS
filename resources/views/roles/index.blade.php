@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Role Management</h5>
                @can('create-role')
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Role
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            {{-- Dashboard summary card: Total Roles --}}
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-start border-primary border-3 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-shield-lock text-primary fs-1"></i>
                            <div>
                                <h6 class="text-muted mb-1">Total Roles</h6>
                                <h4 class="mb-0">{{ $roles->total() }}</h4>
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
                            <th scope="col">Role Name</th>
                            <th scope="col">Permissions</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                        <tr>
                            <th scope="row">{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $role->name == 'Admin' ? 'danger' : 'primary' }} me-2">
                                        {{ $role->name == 'Admin' ? 'Admin' : substr($role->name, 0, 1) }}
                                    </span>
                                    {{ $role->name }}
                                </div>
                            </td>
                            <td>
                                @if ($role->name == 'Admin')
                                    <span class="badge bg-success p-2">All Permissions</span>
                                @else
                                    @forelse ($role->permissions->take(3) as $permission)
                                        <span class="badge bg-primary p-2 me-1 mb-1">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-muted">No permissions assigned</span>
                                    @endforelse
                                    @if ($role->permissions->count() > 3)
                                        <span class="text-muted small">+{{ $role->permissions->count() - 3 }} more</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            type="button" 
                                            id="actionsDropdown{{ $role->id }}" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $role->id }}">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#showRoleModal{{ $role->id }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </li>
                                        @if ($role->name != 'Admin')
                                            @can('edit-role')
                                            <li>
                                                <button class="dropdown-item d-flex align-items-center gap-2" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editRoleModal{{ $role->id }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </li>
                                            @endcan
                                            @can('delete-role')
                                            <li>
                                                <button class="dropdown-item d-flex align-items-center gap-2 text-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteRoleModal{{ $role->id }}">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </li>
                                            @endcan
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- Show Role Modal --}}
                        <div class="modal fade" id="showRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="showRoleModalLabel{{ $role->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="showRoleModalLabel{{ $role->id }}">Role Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Role Name:</label>
                                            <p>{{ $role->name }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Permissions:</label>
                                            @if ($role->name == 'Admin')
                                                <p><span class="badge bg-success p-2">All Permissions</span></p>
                                            @else
                                                <p>
                                                    @forelse ($role->permissions as $permission)
                                                        <span class="badge bg-primary p-2 me-1 mb-1">{{ $permission->name }}</span>
                                                    @empty
                                                        <span class="text-muted">No permissions assigned</span>
                                                    @endforelse
                                                </p>
                                            @endif
                                        </div>
                                        @if($role->created_at || $role->updated_at)
                                        <div class="text-muted small">
                                            @if($role->created_at)
                                                <div>Created: {{ $role->created_at->format('M d, Y h:i A') }}</div>
                                            @endif
                                            @if($role->updated_at)
                                                <div>Last Updated: {{ $role->updated_at->format('M d, Y h:i A') }}</div>
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

                        {{-- Edit Role Modal --}}
                        @if ($role->name != 'Admin')
                            @can('edit-role')
                            <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="editRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="editRoleModalLabel{{ $role->id }}">Edit Role</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('roles.update', $role->id) }}" method="post" class="needs-validation" novalidate>
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name{{ $role->id }}" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name{{ $role->id }}" name="name" value="{{ old('name', $role->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Permissions</label>
                                                    <div class="border rounded p-3" style="max-height: 210px; overflow-y: auto;">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input select-all-permissions" type="checkbox" id="selectAllPermissionsEdit{{ $role->id }}">
                                                            <label class="form-check-label fw-bold" for="selectAllPermissionsEdit{{ $role->id }}">
                                                                Select All
                                                            </label>
                                                        </div>
                                                        @if(isset($permissions) && $permissions->count() > 0)
                                                            @forelse ($permissions as $permission)
                                                                <div class="form-check">
                                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                                           name="permissions[]" 
                                                                           id="permission_edit_{{ $permission->id }}_{{ $role->id }}" 
                                                                           value="{{ $permission->id }}"
                                                                           {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="permission_edit_{{ $permission->id }}_{{ $role->id }}">
                                                                        {{ $permission->name }}
                                                                    </label>
                                                                </div>
                                                            @empty
                                                                <div class="text-muted">No permissions available</div>
                                                            @endforelse
                                                        @else
                                                            <div class="text-muted">No permissions available</div>
                                                        @endif
                                                    </div>
                                                    @error('permissions')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Role</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        @endif

                        {{-- Delete Confirmation Modal --}}
                        @if ($role->name != 'Admin')
                            @can('delete-role')
                            <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteRoleModalLabel{{ $role->id }}">Confirm Deletion</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete the role <strong>{{ $role->name }}</strong>? This action cannot be undone.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        @endif

                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-shield-lock fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No roles found</h5>
                                    @can('create-role')
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                        <i class="bi bi-plus-circle me-1"></i> Create First Role
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} entries
                </div>
                <div>
                    {{ $roles->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create Role Modal --}}
    @can('create-role')
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createRoleModalLabel">Add New Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('roles.store') }}" method="post" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Permissions</label>
                            <div class="border rounded p-3" style="max-height: 210px; overflow-y: auto;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input select-all-permissions" type="checkbox" id="selectAllPermissionsCreate">
                                    <label class="form-check-label fw-bold" for="selectAllPermissionsCreate">
                                        Select All
                                    </label>
                                </div>
                                @if(isset($permissions) && $permissions->count() > 0)
                                    @forelse ($permissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" 
                                                   name="permissions[]" 
                                                   id="permission_create_{{ $permission->id }}" 
                                                   value="{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions') ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_create_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <div class="text-muted">No permissions available</div>
                                    @endforelse
                                @else
                                    <div class="text-muted">No permissions available</div>
                                @endif
                            </div>
                            @error('permissions')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
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
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        margin: 0.2rem;
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

        // Handle Select All Permissions using event delegation
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('select-all-permissions')) {
                console.log('Select All triggered for:', event.target.id); // Debug log
                const modal = event.target.closest('.modal');
                if (modal) {
                    const checkboxes = modal.querySelectorAll('.permission-checkbox');
                    console.log('Found checkboxes:', checkboxes.length); // Debug log
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = event.target.checked;
                    });
                } else {
                    console.log('No modal found for Select All'); // Debug log
                }
            }
        });
    });
</script>
@endsection
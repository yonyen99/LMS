@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Department Management</h5>
            @can('create-department')
                <a href="{{ route('departments.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add Department
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        {{-- Dashboard summary card: Total Departments --}}
        <div class="row mb-3 border-bottom pb-4">
            {{-- Total Departments Card --}}
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card border-start border-primary border-1 shadow-sm h-100">
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
                        <th scope="col-6">Description</th>
                        <th scope="col" width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                    <tr>
                        <th scope="row">{{ ($departments->currentPage() - 1) * $departments->perPage() + $loop->iteration }}</th>
                        <td>{{ $department->name }}</td>
                        <td>{{ Str::limit($department->description, 50) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="actionsDropdown{{ $department->id }}" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu card-1 bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $department->id }}">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('departments.show', $department->id) }}">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </li>
                                    @can('edit-department')
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('departments.edit', $department->id) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    @endcan
                                    @can('delete-department')
                                    <li>
                                        <form id="delete-form-{{ $department->id }}" action="{{ route('departments.destroy', $department->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="dropdown-item d-flex align-items-center gap-2 text-danger" 
                                                    onclick="confirmDelete({{ $department->id }})">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-building fs-1 text-muted mb-2"></i>
                                <h5 class="text-muted">No departments found</h5>
                                @can('create-department')
                                <a href="{{ route('departments.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i> Create First Department
                                </a>
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
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .dropdown-toggle::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 10rem;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
        
        // Add loading state for delete buttons
        const deleteForms = document.querySelectorAll('form[method="POST"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                if(button) {
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                    button.disabled = true;
                }
            });
        });
    });
</script>
@endsection
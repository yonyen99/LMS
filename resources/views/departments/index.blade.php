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
                    @forelse ($departments as $department)
                    <tr>
                        <th scope="row">{{ ($departments->currentPage() - 1) * $departments->perPage() + $loop->iteration }}</th>
                        <td>{{ $department->name }}</td>
                        <td>{{ Str::limit($department->description, 50) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        type="button" 
                                        id="actionsDropdown{{ $department->id }}" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $department->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('departments.show', $department->id) }}">
                                            <i class="bi bi-eye me-2"></i> View
                                        </a>
                                    </li>
                                    @can('edit-department')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('departments.edit', $department->id) }}">
                                            <i class="bi bi-pencil me-2"></i> Edit
                                        </a>
                                    </li>
                                    @endcan
                                    @can('delete-department')
                                    <li>
                                        <form action="{{ route('departments.destroy', $department->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="dropdown-item text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this department?')">
                                                <i class="bi bi-trash me-2"></i> Delete
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
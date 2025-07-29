@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Management Dashboard</h5>
            @can('create-user')
                <a href="{{ route('users.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add New User
                </a>
            @endcan
        </div>
    </div>
    
    <!-- Stats Cards Row -->
    <div class="card-body py-3 border-bottom">
        <div class="row g-3">
            <!-- Total Users Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-statistic h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="card-icon bg-primary bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-people-fill text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">Total Users</h6>
                                <h4 class="mb-0 fw-bold">{{ $totalUsers }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-statistic h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="card-icon bg-success bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">Active Users</h6>
                                <h4 class="mb-0 fw-bold">{{ $activeUsers }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Users Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-statistic h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="card-icon bg-info bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-shield-lock-fill text-info fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">Admin Users</h6>
                                <h4 class="mb-0 fw-bold">{{ $adminUsers }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Users Card -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-statistic h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="card-icon bg-warning bg-opacity-10 rounded p-3 me-3">
                                <i class="bi bi-clock-history text-warning fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-muted">New This Month</h6>
                                <h4 class="mb-0 fw-bold">{{ $newUsers }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" class="d-none d-md-table-cell">Profile</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="d-none d-lg-table-cell">Email</th>
                        <th scope="col" class="d-none d-xl-table-cell">Phone</th>
                        <th scope="col" class="d-none d-lg-table-cell">Roles</th>
                        <th scope="col" class="d-none d-md-table-cell">Status</th>
                        <th scope="col" style="width: 50px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <th scope="row">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</th>
                        <td class="d-none d-md-table-cell">
                            @if($user->images)
                                <img src="{{ asset('storage/'.$user->images) }}" alt="Profile" class="rounded-circle" width="40" height="40">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td class="d-none d-lg-table-cell">{{ $user->email }}</td>
                        <td class="d-none d-xl-table-cell">{{ $user->phone ?? 'N/A' }}</td>
                        <td class="d-none d-lg-table-cell">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge bg-{{ $role == 'Admin' ? 'primary' : 'info' }}">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td class="d-none d-md-table-cell">
                            <span class="badge rounded-pill bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                        id="actionsDropdown{{ $user->id }}" data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu card-1 bg-white" aria-labelledby="actionsDropdown{{ $user->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                            <i class="bi bi-eye me-2"></i> View
                                        </a>
                                    </li>
                                    @can('edit-user')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                            <i class="bi bi-pencil me-2"></i> Edit
                                        </a>
                                    </li>
                                    @endcan
                                    @can('delete-user')
                                        @if(!$user->hasRole('Super Admin') && $user->id != auth()->user()->id)
                                        <li>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-people fs-1 text-muted mb-2"></i>
                                <h5 class="text-muted">No users found</h5>
                                @can('create-user')
                                <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i> Create First User
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
            </div>
            <div>
                {{ $users->onEachSide(1)->links() }}
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
        border: none;
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .card-statistic {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .card-statistic:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .card-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .dropdown-toggle::after {
        display: none;
    }
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
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
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Deleting...';
                    button.disabled = true;
                }
            });
        });
    });
</script>
@endsection
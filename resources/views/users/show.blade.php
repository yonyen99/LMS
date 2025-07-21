@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Details</h5>
                    @if (Auth::user()->hasRole('Employee'))
                            <a href="{{ url('/') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        @else
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Profile Header Section -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-2 text-center mb-3 mb-md-0">
                        @if($user->images)
                            <img src="{{ asset('storage/'.$user->images) }}" 
                                 alt="Profile Image" 
                                 class="img-thumbnail rounded-circle" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-secondary rounded-circle mx-auto" 
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-person text-white" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                            <h3 class="mb-0">{{ $user->name }}</h3>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} py-2">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @forelse ($user->getRoleNames() as $role)
                                <span class="badge bg-primary">{{ $role }}</span>
                            @empty
                                <span class="badge bg-secondary">No Roles Assigned</span>
                            @endforelse
                        </div>
                        
                        @if($user->department)
                        <div class="text-muted">
                            <i class="bi bi-building me-1"></i>
                            {{ $user->department->name }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Main Information Sections -->
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="bi bi-person-lines-fill me-2"></i>Basic Information
                                </h6>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Full Name</p>
                                    <p class="fw-medium">{{ $user->name }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Email Address</p>
                                    <p class="fw-medium">
                                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Phone Number</p>
                                    <p class="fw-medium">
                                        @if($user->phone)
                                            <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Account Status</p>
                                    <p class="fw-medium">
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="bi bi-gear-fill me-2"></i>Account Details
                                </h6>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">User Roles</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse ($user->getRoleNames() as $role)
                                            <span class="badge bg-primary">{{ $role }}</span>
                                        @empty
                                            <span class="badge bg-secondary">No Roles</span>
                                        @endforelse
                                    </div>
                                </div>
                                
                                @if($user->department)
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Department</p>
                                    <p class="fw-medium">{{ $user->department->name }}</p>
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Profile Image</p>
                                    <p class="fw-medium">
                                        @if($user->images)
                                            <a href="{{ asset('storage/'.$user->images) }}" target="_blank" class="text-primary">
                                                View Image
                                            </a>
                                        @else
                                            <span class="text-muted">No image uploaded</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Account Created</p>
                                    <p class="fw-medium">
                                        {{ $user->created_at->format('M d, Y \a\t h:i A') }}
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small">Last Updated</p>
                                    <p class="fw-medium">
                                        {{ $user->updated_at->format('M d, Y \a\t h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @canany(['edit-user', 'delete-user'])
                <div class="d-flex justify-content-end gap-2 mt-4">
                    @can('edit-user')
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit User
                    </a>
                    @endcan
                    
                    @can('delete-user')
                        @if(!$user->hasRole('Super Admin') && $user->id != auth()->user()->id)
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="bi bi-trash me-1"></i> Delete User
                            </button>
                        </form>
                        @endif
                    @endcan
                </div>
                @endcanany
            </div>
        </div>
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
    .text-muted.small {
        font-size: 0.8rem;
    }
    .fw-medium {
        font-weight: 500;
    }
    .badge {
        font-size: 0.8em;
        padding: 0.35em 0.65em;
    }
</style>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Cover Section -->
            <div class="profile-cover position-relative" style="height: 180px; overflow: hidden;">
                <img src="{{ asset('img/cv.png') }}" alt="Cover Image" class="cover-image" style="width: 100%; height: 100%; object-fit: cover;">
                <!-- Profile Details Section -->
                <div class="profile-details position-absolute" style="bottom: -5px; left: 20px; z-index: 1;">
                    <div class="d-flex align-items-end gap-3">
                        @if ($user->images)
                            <img src="{{ asset('storage/' . $user->images) }}" alt="Profile Image"
                                 class="shadow-sm" style="width: 140px; height: 140px; object-fit: cover; border: 5px solid #fff;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm"
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-person text-dark" style="font-size: 1.5rem;"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="mb-0 fw-bold text-black">{{ $user->name }}</h3>
                            <div class="text-muted">
                                <span class="text-black">Role: {{ $user->getRoleNames()->first() ?? 'No Role' }}</span> |
                                <span class="text-black">Department: {{ $user->department ? $user->department->name : 'Not specified' }}</span> |
                                <span class="text-black">Joined: {{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spacer Card -->
            <div class="bg-white mb-4 card-1" style="height: 10px; overflow: hidden;"></div>

            <!-- Columns Section -->
            <div class="d-flex flex-column flex-md-row gap-4 align-items-stretch">
                <!-- Left Column -->
                <div class="col-md-6 flex-fill">
                    <div class="card-1 border-0 bg-white  h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title text-primary mb-4 fw-bold">
                                <i class="bi bi-person-lines-fill me-2"></i>Basic Information
                            </h5>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase">Full Name</p>
                                <p class="fw-medium text-dark">{{ $user->name }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase">Email Address</p>
                                <p class="fw-medium">
                                    <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none">{{ $user->email }}</a>
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase">Phone Number</p>
                                <p class="fw-medium">
                                    @if ($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="text-primary text-decoration-none">{{ $user->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                            <div class="info-item mb-0">
                                <p class="mb-1 text-muted small text-uppercase">Account Status</p>
                                <p class="fw-medium">
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} py-2 px-3 rounded-pill">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="col-md-6 flex-fill">
                    <div class="card-1 border-0 bg-white  h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title text-primary mb-4 fw-bold">
                                <i class="bi bi-gear-fill me-2"></i>Account Details
                            </h5>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase">User Roles</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse ($user->getRoleNames() as $role)
                                        <span class="badge bg-primary py-2 px-3 rounded-pill">{{ $role }}</span>
                                    @empty
                                        <span class="badge bg-secondary py-2 px-3 rounded-pill">No Roles</span>
                                    @endforelse
                                </div>
                            </div>
                            @if ($user->department)
                                <div class="info-item mb-3">
                                    <p class="mb-1 text-muted small text-uppercase">Department</p>
                                    <p class="fw-medium text-dark">{{ $user->department->name }}</p>
                                </div>
                            @endif
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase">Account Created</p>
                                <p class="fw-medium text-dark">{{ $user->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                            <div class="info-item mb-0">
                                <p class="mb-1 text-muted small text-uppercase">Last Updated</p>
                                <p class="fw-medium text-dark">{{ $user->updated_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @canany(['edit-user', 'delete-user'])
                <div class="d-flex justify-content-end gap-3 mt-2">
                    @if (Auth::user()->hasAnyRole(['Admin', 'Super Admin']))
                        @can('edit-user')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn  card-1 btn-primary px-4">
                                <i class="bi bi-pencil-square me-2"></i> Edit User
                            </a>
                        @endcan
                    @endif
                    @can('delete-user')
                        @if (!$user->hasRole('Super Admin') && $user->id != auth()->user()->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg rounded-pill px-4"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="bi bi-trash me-2"></i> Delete User
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            @endcanany
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-cover {
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .cover-image {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
    }

    .profile-details {
        position: absolute;
        z-index: 1;
    }

    .profile-details:hover {
        transform: translateY(-2px);
    }
    /* .card:hover, .card-1:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    } */

    .info-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .text-muted.small {
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .fw-medium {
        font-weight: 500;
    }

   

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .col-md-6.flex-fill {
        flex: 1 1 0;
        min-width: 0;
    }

    @media (max-width: 768px) {
        .profile-cover {
            height: 150px;
        }

        .profile-details {
            bottom: -40px;
            left: 10px;
        }

        .profile-details img,
        .profile-details .rounded-circle {
            width: 80px !important;
            height: 80px !important;
        }

        .profile-details .d-flex {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-details .bg-white {
            padding: 1.5rem;
        }

        h3 {
            font-size: 1.25rem;
        }

        .text-muted.small {
            font-size: 0.7rem;
        }

        .d-flex.flex-md-row {
            flex-direction: column !important;
        }

        .card-1 {
            height: auto;
        }
    }
</style>
@endsection

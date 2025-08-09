@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Cover Section -->
            <div class="profile-cover position-relative mb-5" style="height: 200px; overflow: hidden; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="cover-gradient" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;  z-index: 1;"></div>
                <img src="{{ asset('img/cv.png') }}" alt="Cover Image" class="cover-image" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
                <!-- Profile Details Section -->
                <div class="profile-details position-absolute" style="top: 50%; left: 30px; transform: translateY(-50%); z-index: 2; text-align: left; display: flex; align-items: center; gap: 20px;">
                    @if ($user->images)
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $user->images) }}" alt="Profile Image"
                                 class="rounded-circle shadow-lg" style="width: 120px; height: 120px; object-fit: cover; border: 5px solid #fff; display: block;">
                            <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle shadow"
                                    data-bs-toggle="modal" data-bs-target="#editProfileImageModal" title="Edit Image">
                                <i class="bi bi-pencil-square" style="font-size: 1.2rem;"></i>
                            </button>
                        </div>
                    @else
                        <div class="position-relative d-flex align-items-center justify-content-center bg-light rounded-circle shadow-lg"
                             style="width: 120px; height: 120px;">
                            <i class="bx bx-user text-dark" style="font-size: 3rem;"></i>
                            <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle shadow"
                                    data-bs-toggle="modal" data-bs-target="#editProfileImageModal" title="Edit Image">
                                <i class="bi bi-pencil-square" style="font-size: 1.2rem;"></i>
                            </button>
                        </div>
                    @endif
                    <div class="text-white">
                        <h2 class="mb-2 fw-bold">{{ $user->name }}</h2>
                        <div class="text-muted">
                            <span><i class="bx bx-crown me-1"></i>Role: {{ $user->getRoleNames()->first() ?? 'No Role' }}</span><br>
                            <span><i class="bx bx-briefcase me-1"></i>Department: {{ $user->department ? $user->department->name : 'Not specified' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columns Section -->
            <div class="row g-4 mt-5">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="background: #f8f9fa;">
                        <h5 class="card-title text-primary mb-4 fw-bold">
                            <i class="bx bx-user me-2"></i> Basic Information
                        </h5>
                        <div class="info-item mb-3">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-user me-1"></i> Full Name</p>
                            <p class="fw-medium text-dark">{{ $user->name }}</p>
                        </div>
                        <div class="info-item mb-3">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-envelope me-1"></i> Email Address</p>
                            <p class="fw-medium">
                                <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none">{{ $user->email }}</a>
                            </p>
                        </div>
                        <div class="info-item mb-3">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-phone me-1"></i> Phone Number</p>
                            <p class="fw-medium">
                                @if ($user->phone)
                                    <a href="tel:{{ $user->phone }}" class="text-primary text-decoration-none">{{ $user->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                        <div class="info-item mb-0">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-shield me-1"></i> Account Status</p>
                            <p class="fw-medium">
                                <span class="badge bg-success py-2 px-3 rounded-pill">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="background: #f8f9fa;">
                        <h5 class="card-title text-primary mb-4 fw-bold">
                            <i class="bx bx-cog me-2"></i> Account Details
                        </h5>
                        <div class="info-item mb-3">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-crown me-1"></i> User Roles</p>
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
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-briefcase me-1"></i> Department</p>
                                <p class="fw-medium text-dark">{{ $user->department->name }}</p>
                            </div>
                        @endif
                        <div class="info-item mb-3">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-calendar me-1"></i> Account Created</p>
                            <p class="fw-medium text-dark">{{ $user->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <div class="info-item mb-0">
                            <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-time me-1"></i> Last Updated</p>
                            <p class="fw-medium text-dark">{{ $user->updated_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @canany(['edit-user', 'delete-user'])
                <div class="d-flex justify-content-end gap-3 mt-4">
                    @if (Auth::user()->hasAnyRole(['Admin', 'Super Admin']))
                        @can('edit-user')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                                <i class="bx bx-pencil me-2"></i> Edit User
                            </a>
                        @endcan
                    @endif
                    @can('delete-user')
                        @if (!$user->hasRole('Super Admin') && $user->id != auth()->user()->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg px-4 shadow-sm"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="bx bx-trash me-2"></i> Delete User
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            @endcanany
        </div>
    </div>
</div>
<!-- Edit Profile Image Modal -->
<div class="modal fade" id="editProfileImageModal" tabindex="-1" aria-labelledby="editProfileImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('users.updateImage', $user->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileImageModalLabel">Edit Profile Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="image" class="form-label">Choose New Image</label>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
                </div>
                @if ($user->images)
                    <div class="text-center">
                        <small class="text-muted">Current Image:</small><br>
                        <img src="{{ asset('storage/' . $user->images) }}" width="120" class="rounded-circle mt-2 shadow-sm">
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Image</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('#editProfileImageModal form').addEventListener('submit', function () {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.innerHTML = '<span id="btnText">Updating...</span> <span id="spinner" class="spinner-border spinner-border-sm"></span>';
        submitButton.disabled = true;
    });
</script>
@endpush

@section('styles')
<style>
    .profile-cover {
        position: relative;
        border: none;
        width: 100%;
        transition: all 0.3s ease;
    }

    .cover-image {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 0;
    }

    .cover-gradient {
        z-index: 1;
    }

    .profile-details {
        position: absolute;
        transition: all 0.3s ease;
    }

    .profile-details:hover {
        transform: translateY(-5px);
    }

    .card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .text-muted.small {
        font-size: 0.8rem;
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
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .col-md-6 {
        flex: 1 1 0;
    }

    .bx {
        font-size: 1.2rem;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .profile-cover {
            height: 150px;
        }

        .profile-details {
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }

        .profile-details img,
        .profile-details .rounded-circle {
            width: 90px !important;
            height: 90px !important;
            border-width: 3px !important;
        }

        .profile-details .d-flex {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        h2 {
            font-size: 1.5rem;
        }

        .text-muted.small {
            font-size: 0.75rem;
        }

        .row {
            flex-direction: column !important;
        }

        .card {
            height: auto;
        }

        .bx {
            font-size: 1rem;
        }
    }
</style>
@endsection
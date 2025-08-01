@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center ">
        <div class="col-lg-10">
            <!-- Cover Section -->
            <div class="profile-cover position-relative" style="height: 180px; overflow: hidden; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                <img src="{{ asset('img/cv.png') }}" alt="Cover Image" class="cover-image" style="width: 100%; height: 100%; object-fit: cover;">
                <!-- Profile Details Section -->
                <div class="profile-details position-absolute" style="bottom: -3px; left: 20px; z-index: 1;">
                    <div class="d-flex align-items-end gap-3">
                        @if ($user->images)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $user->images) }}" alt="Profile Image"
                                    class="shadow-sm rounded-circle" style="width: 70px; height: 70px; object-fit: cover; border: 5px solid #fff;">
                                <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle shadow"
                                        data-bs-toggle="modal" data-bs-target="#editProfileImageModal"
                                        title="Edit Image">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm position-relative"
                                style="width: 60px; height: 60px;">
                                <i class="bx bx-user text-dark" style="font-size: 1.5rem;"></i>
                                <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle shadow"
                                        data-bs-toggle="modal" data-bs-target="#editProfileImageModal"
                                        title="Edit Image">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        @endif

                        <div>
                            <h3 class="mb-0 fw-bold text-black">{{ $user->name }}</h3>
                            <div class="text-muted">
                                <span class="text-black"><i class="bx bx-crown me-1"></i>Role: {{ $user->getRoleNames()->first() ?? 'No Role' }}</span> |
                                <span class="text-black"><i class="bx bx-briefcase me-1"></i>Department: {{ $user->department ? $user->department->name : 'Not specified' }}</span> |
                                <span class="text-black"><i class="bx bx-calendar me-1"></i>Joined: {{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spacer Card -->
            <div class="bg-white mb-4 card-1"></div>

            <!-- Columns Section -->
            <div class="d-flex flex-column flex-md-row gap-4  align-items-stretch">
                <!-- Left Column -->
                <div class="col-md-6 flex-fill">
                    <div class="card-1 border-0 bg-white  h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title text-primary mb-4 fw-bold">
                                <i class="bx bx-user me-2"></i>Basic Information
                            </h5>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-user me-1"></i>Full Name</p>
                                <p class="fw-medium text-dark">{{ $user->name }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-envelope me-1"></i>Email Address</p>
                                <p class="fw-medium">
                                    <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none">{{ $user->email }}</a>
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-phone me-1"></i>Phone Number</p>
                                <p class="fw-medium">
                                    @if ($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="text-primary text-decoration-none">{{ $user->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                            <div class="info-item mb-0">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-shield me-1"></i>Account Status</p>
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
                                <i class="bx bx-cog me-2"></i>Account Details
                            </h5>
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-crown me-1"></i>User Roles</p>
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
                                    <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-briefcase me-1"></i>Department</p>
                                    <p class="fw-medium text-dark">{{ $user->department->name }}</p>
                                </div>
                            @endif
                            <div class="info-item mb-3">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-calendar me-1"></i>Account Created</p>
                                <p class="fw-medium text-dark">{{ $user->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                            <div class="info-item mb-0">
                                <p class="mb-1 text-muted small text-uppercase"><i class="bx bx-time me-1"></i>Last Updated</p>
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
                            <a href="{{ route('users.edit', $user->id) }}" class="btn card-1 btn-primary btn-lg px-4">
                                <i class="bx bx-pencil me-2"></i> Edit User
                            </a>
                        @endcan
                    @endif
                    @can('delete-user')
                        @if (!$user->hasRole('Super Admin') && $user->id != auth()->user()->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg px-4"
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
                        <img src="{{ asset('storage/' . $user->images) }}" width="80" class="rounded-circle mt-2">
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
        document.getElementById('btnText').textContent = 'Updating...';
        document.getElementById('spinner').classList.remove('d-none');
    });
</script>
@endpush

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

    .bx {
        font-size: 1rem;
        vertical-align: middle;
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

        .bx {
            font-size: 0.8rem;
        }
    }
</style>
@endsection
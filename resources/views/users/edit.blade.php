@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Profile Cover and Header --> 
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
            <!-- Form Card -->
            <div class="card mt-5 card-2 bg-white " style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                <div class="card-body p-4">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="images" class="form-control">
                                @if ($user->images)
                                    <img src="{{ asset('storage/' . $user->images) }}" class="img-thumbnail mt-2" style="max-height: 150px;">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="">-- Select Department --</option>
                                    @foreach ($departments as $id => $name)
                                        <option value="{{ $id }}" {{ old('department_id', $user->department_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @unless (Auth::user()->hasRole('Employee'))
                                <div class="col-12">
                                    <label class="form-label">Roles</label>
                                    <div class="border rounded p-3">
                                        <div class="row">
                                            @foreach ($roles as $role)
                                                @if ($role !== 'Super Admin' || Auth::user()->hasRole('Super Admin'))
                                                    <div class="col-md-3 col-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="roles[]" value="{{ $role }}" id="role_{{ $role }}" class="form-check-input" {{ in_array($role, old('roles', $userRoles ?? [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="role_{{ $role }}">{{ $role }}</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endunless
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

    // Bootstrap validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
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

    .img-thumbnail {
        max-width: 100%;
        height: auto;
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
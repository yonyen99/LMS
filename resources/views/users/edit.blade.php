<!-- This is a partial updated example of edit.blade.php styled like show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Profile Cover and Header --> 
            <div class="profile-cover position-relative" style="height: 180px; overflow: hidden; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                <img src="{{ asset('img/cv.png') }}" alt="Cover Image" class="cover-image" style="width: 100%; height: 100%; object-fit: cover; ">
                <div class="profile-details position-absolute" style="bottom: -5px; left: 20px; z-index: 1;">
                    <div class="d-flex align-items-end gap-3">
                        @if ($user->images)
                            <img src="{{ asset('storage/' . $user->images) }}" alt="Profile Image"
                                 class="shadow-sm" style="width: 140px; height: 140px; object-fit: cover; border: 5px solid #fff; " >
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm"
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-person text-dark" style="font-size: 1.5rem;"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="mb-0 fw-bold text-black">Edit Profile - {{ $user->name }}</h3>
                            <div class="text-muted">
                                <span class="text-black">Role: {{ $user->getRoleNames()->first() ?? 'No Role' }}</span> |
                                <span class="text-black">Department: {{ $user->department ? $user->department->name : 'Not specified' }}</span> |
                                <span class="text-black">Joined: {{ $user->created_at->format('M Y') }}</span>
                            </div>
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
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active User</label>
                                </div>
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
@endsection

@section('styles')
<style>
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
    .img-thumbnail {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection

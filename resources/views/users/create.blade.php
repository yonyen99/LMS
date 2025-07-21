@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New User</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <div class="row g-3">
                        <!-- Personal Information Section -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="images" class="form-label">Profile Image</label>
                                <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                       id="images" name="images" accept="image/jpeg,image/png,image/jpg">
                                <div class="form-text">Max size: 2MB (JPG, JPEG, PNG)</div>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" name="department_id">
                                    <option value="">-- Select Department --</option>
                                    @foreach ($departments as $id => $name)
                                        <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active User</label>
                                </div>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Roles Section (Full Width) -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Roles <span class="text-danger">*</span></label>
                                <div class="border p-3 rounded @error('roles') border-danger @enderror">
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            @if ($role != 'Super Admin')
                                                <div class="col-md-3 col-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="role_{{ $role }}" name="roles[]" 
                                                               value="{{ $role }}"
                                                               {{ in_array($role, old('roles') ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="role_{{ $role }}">
                                                            {{ $role }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @elseif (Auth::user()->hasRole('Super Admin'))
                                                <div class="col-md-3 col-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="role_{{ $role }}" name="roles[]" 
                                                               value="{{ $role }}"
                                                               {{ in_array($role, old('roles') ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="role_{{ $role }}">
                                                            {{ $role }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @error('roles')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-person-plus me-2"></i> Create User
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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
    .form-label {
        font-weight: 500;
    }
    .form-text {
        font-size: 0.8rem;
    }
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.5em;
    }
    .border-danger {
        border-color: #dc3545 !important;
    }
    .form-check-input {
        margin-top: 0.2em;
    }
</style>
@endsection

@section('scripts')
<script>
    // Enable Bootstrap 5 form validation
    (function () {
        'use strict'
        
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')
        
        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
@endsection
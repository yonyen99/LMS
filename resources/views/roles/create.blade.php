@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Role</h5>
                        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">&larr; Back to Roles</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="post">
                        @csrf

                        <div class="mb-4 row">
                            <label for="name" class="col-md-4 col-form-label text-md-end text-start fw-bold">Role Name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter role name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 row">
                            <label class="col-md-4 col-form-label text-md-end text-start fw-bold">Permissions</label>
                            <div class="col-md-6">
                                <div class="border rounded p-3" style="max-height: 210px; overflow-y: auto;">
                                    @forelse ($permissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="permissions[]" 
                                                   id="permission_{{ $permission->id }}" 
                                                   value="{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions') ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <div class="text-muted">No permissions available</div>
                                    @endforelse
                                </div>
                                @error('permissions')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Save Role
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
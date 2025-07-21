@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Role Details</h5>
                        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <label class="col-md-4 fw-bold text-md-end">Role Name:</label>
                        <div class="col-md-8">
                            <div class="p-2 bg-light rounded">
                                {{ $role->name }}
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-md-4 fw-bold text-md-end">Permissions:</label>
                        <div class="col-md-8">
                            @if ($role->name == 'Super Admin')
                                <span class="badge bg-success fs-6 p-2 mb-2">
                                    <i class="fas fa-shield-alt me-1"></i> All Permissions
                                </span>
                            @else
                                @forelse ($rolePermissions as $permission)
                                    <span class="badge bg-primary me-1 mb-1 p-2">
                                        <i class="fas fa-key me-1"></i> {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">No permissions assigned</span>
                                @endforelse
                            @endif
                        </div>
                    </div>

                    @if(auth()->user()->can('role-edit') || auth()->user()->can('role-delete'))
                    <div class="row">
                        <div class="col-md-8 offset-md-4">
                            <div class="d-flex gap-2">
                                @can('role-edit')
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit Role
                                </a>
                                @endcan

                                @can('role-delete')
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">
                                        <i class="fas fa-trash me-1"></i> Delete Role
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
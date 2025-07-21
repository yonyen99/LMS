@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Leave Type Details</h5>
                        <div>
                            <a href="{{ route('leave-types.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back to List
                            </a>
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Leave Type Name</h6>
                            <p class="lead">{{ $leaveType->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Created Date</h6>
                            <p>{{ $leaveType->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Description</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $leaveType->description ?? 'No description available' }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Last Updated</h6>
                        <p>{{ $leaveType->updated_at->diffForHumans() }}</p>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        @can('edit-leave-type')
                        <a href="{{ route('leave-types.edit', $leaveType->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        @endcan

                        @can('delete-leave-type')
                        <form action="{{ route('leave-types.destroy', $leaveType->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this leave type? This action cannot be undone.')">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
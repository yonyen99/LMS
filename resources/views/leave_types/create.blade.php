@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Leave Type</h5>
                    <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">&larr; Back to Roles</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('leave-types.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Leave Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="form-control" 
                                placeholder="Enter leave type name" 
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Optional description"
                            ></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i> Create Leave Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

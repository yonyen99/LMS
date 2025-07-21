@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Department Details</h5>
                    <a href="{{ route('departments.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end fw-bold">Name:</label>
                    <div class="col-md-6">
                        <div class="form-control-plaintext border-bottom pb-2">
                            {{ $department->name }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="description" class="col-md-4 col-form-label text-md-end fw-bold">Description:</label>
                    <div class="col-md-6">
                        <div class="form-control-plaintext border-bottom pb-2">
                            {{ $department->description }}
                        </div>
                    </div>
                </div>

                @if($department->created_at || $department->updated_at)
                <div class="row">
                    <div class="col-md-8 offset-md-4">
                        <div class="text-muted small">
                            @if($department->created_at)
                                <div>Created: {{ $department->created_at->format('M d, Y h:i A') }}</div>
                            @endif
                            @if($department->updated_at)
                                <div>Last Updated: {{ $department->updated_at->format('M d, Y h:i A') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
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
    .form-control-plaintext {
        padding-left: 0;
        padding-right: 0;
    }
</style>
@endsection
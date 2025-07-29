@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Leave Type Management</h5>
            @can('create-leave-type')
                <a href="{{ route('leave-types.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add Leave Type
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3 border-bottom pb-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="card border-start border-primary border-1 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-check text-primary fs-1"></i>
                        <div>
                            <h6 class="text-muted mb-1">Total Leave Types</h6>
                            <h4 class="mb-0">{{ $totalLeaveTypes }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="60px">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col" width="120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaveTypes as $type)
                    <tr>
                        <th scope="row">{{ ($leaveTypes->currentPage() - 1) * $leaveTypes->perPage() + $loop->iteration }}</th>
                        <td>
                            <a href="{{ route('leave-types.show', $type->id) }}" class="text-decoration-none">
                                {{ $type->name }}
                            </a>
                        </td>
                        <td>{{ Str::limit($type->description, 50) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="actionsDropdown{{ $type->id }}" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu card-1 bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $type->id }}">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('leave-types.show', $type->id) }}">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </li>
                                    @can('edit-leave-type')
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('leave-types.edit', $type->id) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    @endcan
                                    @can('delete-leave-type')
                                    <li>
                                        <form id="delete-form-{{ $type->id }}" action="{{ route('leave-types.destroy', $type->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="dropdown-item d-flex align-items-center gap-2 text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this leave type?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-calendar-check fs-1 text-muted mb-2"></i>
                                <h5 class="text-muted">No leave types found</h5>
                                @can('create-leave-type')
                                <a href="{{ route('leave-types.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i> Create First Leave Type
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaveTypes->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $leaveTypes->firstItem() }} to {{ $leaveTypes->lastItem() }} of {{ $leaveTypes->total() }} entries
            </div>
            <div>
                {{ $leaveTypes->onEachSide(1)->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Leave Types</h1>

    <a href="{{ route('leave-types.create') }}" class="btn btn-primary mb-3">Add Leave Type</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="col-1">#</th>
                <th class="col-3">Name</th>
                <th colspan="row-6">Description</th>
                <th class="col-1 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaveTypes as $type)
                <tr>
                    <td class="col-1">{{ $loop->iteration }}</td>
                    <td class="col-3">{{ $type->name }}</td>
                    <td colspan="row-6">{{ $type->description }}</td>
                    <td  class="col-1 text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                    type="button" 
                                    id="actionsDropdown{{ $type->id }}" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $type->id }}">
                                <li>
                                    <a class="dropdown-item" href="{{ route('leave-types.edit', $type->id) }}">
                                        <i class="bi bi-pencil me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('leave-types.destroy', $type->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="dropdown-item text-danger" 
                                                onclick="return confirm('Are you sure you want to delete this leave type?')">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $leaveTypes->links() }}
</div>
@endsection

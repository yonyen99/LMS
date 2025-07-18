@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Department List</div>
    <div class="card-body">
        @can('create-department')
            <a href="{{ route('departments.create') }}" class="btn btn-success btn-sm my-2"><i class="bi bi-plus-circle"></i> Add New Department</a>
        @endcan
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                <th scope="col">S#</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $department)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->description }}</td>
                    <td>
                        <form action="{{ route('departments.destroy', $department->id) }}" method="post">
                            @csrf
                            @method('DELETE')

                            <a href="{{ route('departments.show', $department->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> Show</a>

                            @can('edit-department')
                                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                            @endcan

                            @can('delete-department')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete this department?');"><i class="bi bi-trash"></i> Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @empty
                    <td colspan="4">
                        <span class="text-danger">
                            <strong>No department Found!</strong>
                        </span>
                    </td>
                @endforelse
            </tbody>
        </table>

        {{ $departments->links() }}

    </div>
</div>
@endsection
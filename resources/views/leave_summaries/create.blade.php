@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Create Leave Summary</div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('leave-summaries.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="entitled" class="form-label">Entitled Days</label>
                        <input type="number" step="0.5" name="entitled" id="entitled" class="form-control"
                            value="{{ old('entitled') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="leave_type_id" class="form-label">Leave Type</label>
                        <select name="leave_type_id" id="leave_type_id" class="form-select" required>
                            <option value="">Select Leave Type</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="report_date" class="form-label">Report Date</label>
                        <input type="date" name="report_date" id="report_date" class="form-control"
                            value="{{ old('report_date', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('leave-summaries.index') }}" class="btn btn-warning text-white">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

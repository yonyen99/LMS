@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-2 "  style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
        <div class="card-header card-2 fw-bold">Edit Leave Summary</div>

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

            <form action="{{ route('leave-summaries.update', $leaveSummary->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ $leaveSummary->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="entitled" class="form-label">Entitled Days</label>
                        <input type="number" name="entitled" id="entitled" class="form-control" value="{{ old('entitled', $leaveSummary->entitled) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="leave_type_id" class="form-label">Leave Type</label>
                        <select name="leave_type_id" id="leave_type_id" class="form-select" required>
                            <option value="">Select Leave Type</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ $leaveSummary->leave_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="report_date" class="form-label">Report Date</label>
                        <input type="date" name="report_date" id="report_date" class="form-control"
                            value="{{ old('report_date', \Carbon\Carbon::parse($leaveSummary->report_date)->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('leave-summaries.index') }}" class="btn btn-warning text-white">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Submit a leave request</div>

        <div class="card-body">
            {{-- Show validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('leave-requests.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="leave_type_id" class="form-label">Leave type</label>
                        <select name="leave_type_id" id="leave_type_id" class="form-select" required>
                            <option value="">Select leave type</option>
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="duration" class="form-label">Duration</label>
                        <input type="text" name="duration" id="duration" value="{{ old('duration') }}" class="form-control" placeholder="e.g. 1.5 days" required>
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Start Time</label>
                        <select name="start_time" id="start_time" class="form-select" required>
                            <option value="">Select start time</option>
                            <option value="morning" {{ old('start_time') == 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ old('start_time') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label">End Time</label>
                        <select name="end_time" id="end_time" class="form-select" required>
                            <option value="">Select end time</option>
                            <option value="morning" {{ old('end_time') == 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ old('end_time') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Enter reason">{{ old('reason') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="status" value="planned" class="btn btn-primary btn-sm">
                        <i class="bi bi-calendar2"></i> Planned
                    </button>
                    <button type="submit" name="status" value="requested" class="btn btn-info btn-sm text-white">
                        <i class="bi bi-check2-circle"></i> Requested
                    </button>
                    <a href="{{ route('leave-requests.index') }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

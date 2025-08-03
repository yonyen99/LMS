@extends('layouts.app')

@section('title', 'Create New Overtime Request')

@section('content')
    <div class="container px-4 py-5">
        <h1 class="mb-4">Create New Overtime Request</h1>

        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('over-time.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            <!-- Employee Name (readonly) -->
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <input type="text" class="form-control">
            </div>

            <!-- Department (dynamic) -->
            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id"
                    name="department_id" required>
                    <option value="">-- Select Department --</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Overtime Date -->
            <div class="mb-3">
                <label for="overtime_date" class="form-label">Overtime Date</label>
                <input type="date" class="form-control @error('overtime_date') is-invalid @enderror" id="overtime_date"
                    name="overtime_date" value="{{ old('overtime_date') }}" required>
                @error('overtime_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Time Period -->
            <div class="mb-3">
                <label for="time_period" class="form-label">Time Period</label>
                <select class="form-select @error('time_period') is-invalid @enderror" id="time_period" name="time_period"
                    required>
                    <option value="" disabled selected>Select Time Period</option>
                    <option value="before_shift" {{ old('time_period') == 'before_shift' ? 'selected' : '' }}>Before Shift
                    </option>
                    <option value="after_shift" {{ old('time_period') == 'after_shift' ? 'selected' : '' }}>After Shift
                    </option>
                    <option value="weekend" {{ old('time_period') == 'weekend' ? 'selected' : '' }}>Weekend</option>
                    <option value="holiday" {{ old('time_period') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                </select>
                @error('time_period')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Start Time and End Time -->
            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                        name="start_time" value="{{ old('start_time') }}" required>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                        name="end_time" value="{{ old('end_time') }}" required>
                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Duration -->
            <div class="mb-3">
                <label for="duration" class="form-label">Duration (in hours)</label>
                <input type="number" step="0.5" min="0.5" max="24"
                    class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration"
                    value="{{ old('duration') }}" required>
                @error('duration')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reason -->
            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Overtime</label>
                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3"
                    required>{{ old('reason') }}</textarea>
                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mb-3">
                <a href="{{ route('over-time.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap validation script -->
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection

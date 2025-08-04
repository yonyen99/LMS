@extends('layouts.app')

@section('title', 'Edit Overtime Request')

@section('content')
    <div class="container px-4 py-5">
        <h1 class="mb-4">Edit Overtime Request</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('over-time.update', $overtime->id) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            @method('PATCH')

            <!-- Overtime Date -->
            <div class="mb-3">
                <label for="overtime_date" class="form-label">Overtime Date</label>
                <input type="date" class="form-control @error('overtime_date') is-invalid @enderror" id="overtime_date"
                    name="overtime_date" value="{{ old('overtime_date', $overtime->overtime_date) }}" required>
                @error('overtime_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Time Period -->
            <div class="mb-3">
                <label for="time_period" class="form-label">Time Period</label>
                <select class="form-select @error('time_period') is-invalid @enderror" id="time_period" name="time_period"
                    required>
                    <option value="" disabled>Select Time Period</option>
                    <option value="before_shift"
                        {{ old('time_period', $overtime->time_period) == 'before_shift' ? 'selected' : '' }}>Before Shift
                    </option>
                    <option value="after_shift"
                        {{ old('time_period', $overtime->time_period) == 'after_shift' ? 'selected' : '' }}>After Shift
                    </option>
                    <option value="weekend"
                        {{ old('time_period', $overtime->time_period) == 'weekend' ? 'selected' : '' }}>Weekend</option>
                    <option value="holiday"
                        {{ old('time_period', $overtime->time_period) == 'holiday' ? 'selected' : '' }}>Holiday</option>
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
                        name="start_time" value="{{ old('start_time', $overtime->start_time) }}" required>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                        name="end_time" value="{{ old('end_time', $overtime->end_time) }}" required>
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
                    value="{{ old('duration', $overtime->duration) }}" required>
                @error('duration')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reason -->
            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Overtime</label>
                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3"
                    required>{{ old('reason', $overtime->reason) }}</textarea>
                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- cancel button --}}
            <div class="mb-3">
                <a href="{{ route('over-time.index') }}" class="btn btn-secondary">Cancel</a>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Update Request</button>
            </div>

        </form>
    </div>

    <!-- Duration calculation script -->
    <script>
        document.getElementById('end_time').addEventListener('change', function() {
            const start = document.getElementById('start_time').value;
            const end = this.value;
            if (start && end) {
                const startTime = new Date(`1970-01-01T${start}:00`);
                const endTime = new Date(`1970-01-01T${end}:00`);
                const diff = (endTime - startTime) / (1000 * 60 * 60);
                if (diff > 0) {
                    document.getElementById('duration').value = diff.toFixed(1);
                } else {
                    document.getElementById('duration').value = '';
                    alert('End time must be after start time.');
                }
            }
        });

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

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-2 shadow-sm">
        <div class="card-header card-2 fw-bold">Submit an Overtime Request</div>

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

            <form action="{{ route('over-time.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row g-3 mb-3">
                    <!-- Overtime Date -->
                    <div class="col-md-6">
                        <label for="overtime_date" class="form-label">Overtime Date <span style="color: red;">*</span></label>
                        <input type="text"
                               class="form-control @error('overtime_date') is-invalid @enderror"
                               id="overtime_date"
                               name="overtime_date"
                               value="{{ old('overtime_date') }}"
                               placeholder="YYYY-MM-DD"
                               required
                               style="cursor: pointer;">
                        @error('overtime_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Time Period -->
                    <div class="col-md-6">
                        <label for="time_period" class="form-label">Time Period <span style="color: red;">*</span></label>
                        <select name="time_period" id="time_period"
                                class="form-select @error('time_period') is-invalid @enderror"
                                required style="cursor: pointer;">
                            <option value="" disabled selected>Select Time Period</option>
                            <option value="before_shift" {{ old('time_period') == 'before_shift' ? 'selected' : '' }}>Before Shift</option>
                            <option value="after_shift" {{ old('time_period') == 'after_shift' ? 'selected' : '' }}>After Shift</option>
                            <option value="weekend" {{ old('time_period') == 'weekend' ? 'selected' : '' }}>Weekend</option>
                            <option value="holiday" {{ old('time_period') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                        </select>
                        @error('time_period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Time -->
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Start Time <span style="color: red;">*</span></label>
                        <input type="text"
                               class="form-control @error('start_time') is-invalid @enderror"
                               id="start_time"
                               name="start_time"
                               value="{{ old('start_time') }}"
                               required
                               placeholder="HH:MM"
                               style="cursor: pointer;">
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div class="col-md-6">
                        <label for="end_time" class="form-label">End Time <span style="color: red;">*</span></label>
                        <input type="text"
                               class="form-control @error('end_time') is-invalid @enderror"
                               id="end_time"
                               name="end_time"
                               value="{{ old('end_time') }}"
                               required
                               placeholder="HH:MM"
                               style="cursor: pointer;">
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div class="col-md-6">
                        <label for="duration" class="form-label">Duration (hours)</label>
                        <input type="number"
                               name="duration"
                               id="duration"
                               class="form-control @error('duration') is-invalid @enderror"
                               value="{{ old('duration') }}"
                               step="0.5"
                               min="0.5"
                               max="24"
                               readonly
                               style="cursor: not-allowed;">
                        <div class="form-text">Automatically calculated based on time</div>
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Reason -->
                    <div class="col-12">
                        <label for="reason" class="form-label">Reason <span style="color: red;">*</span></label>
                        <textarea name="reason"
                                  id="reason"
                                  class="form-control @error('reason') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Explain your reason for overtime"
                                  required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check2-circle"></i> Submit Request
                    </button>
                    <a href="{{ route('over-time.index') }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Overtime Date (only today and future allowed, default today)
    flatpickr('#overtime_date', {
        dateFormat: 'Y-m-d',
        allowInput: true,
        minDate: "today",
        defaultDate: "today"
    });

    flatpickr('#start_time', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
        allowInput: true
    });

    flatpickr('#end_time', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
        allowInput: true
    });

    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const durationInput = document.getElementById('duration');

    function calculateDuration() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (startTime && endTime) {
            const start = new Date(`1970-01-01T${startTime}:00`);
            const end = new Date(`1970-01-01T${endTime}:00`);

            if (end < start) {
                end.setDate(end.getDate() + 1);
            }

            const diffInMs = end - start;
            const hours = diffInMs / (1000 * 60 * 60);
            const rounded = Math.round(hours * 2) / 2;

            if (rounded >= 0.5 && rounded <= 24) {
                durationInput.value = rounded;
            } else {
                durationInput.value = '';
            }
        } else {
            durationInput.value = '';
        }
    }

    startTimeInput.addEventListener('change', calculateDuration);
    endTimeInput.addEventListener('change', calculateDuration);
});
</script>
@endsection
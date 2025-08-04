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

            <!-- Overtime Date -->
            <div class="mb-3">
                <label for="overtime_date" class="form-label">Overtime Date</label>
                <input type="text" class="form-control @error('overtime_date') is-invalid @enderror" id="overtime_date"
                    name="overtime_date" value="{{ old('overtime_date') }}" required placeholder="YYYY-MM-DD">
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
                    <input type="text" class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                        name="start_time" value="{{ old('start_time') }}" required placeholder="HH:MM">
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="text" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                        name="end_time" value="{{ old('end_time') }}" required placeholder="HH:MM">
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
                    value="{{ old('duration') }}" required readonly>
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

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Bootstrap validation, duration calculation, and Flatpickr initialization script -->
    <script>
        (() => {
            'use strict';

            // Bootstrap form validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Initialize Flatpickr for date and time inputs
            flatpickr('#overtime_date', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                placeholder: 'YYYY-MM-DD',
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.setAttribute('placeholder', 'YYYY-MM-DD');
                }
            });

            flatpickr('#start_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                allowInput: true,
                placeholder: 'HH:MM',
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.setAttribute('placeholder', 'HH:MM');
                }
            });

            flatpickr('#end_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                allowInput: true,
                placeholder: 'HH:MM',
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.setAttribute('placeholder', 'HH:MM');
                }
            });

            // Auto-calculate duration based on start_time and end_time
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const durationInput = document.getElementById('duration');

            function calculateDuration() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                if (startTime && endTime) {
                    const start = new Date(`1970-01-01T${startTime}:00`);
                    const end = new Date(`1970-01-01T${endTime}:00`);

                    // Handle case where end time is on the next day
                    if (end < start) {
                        end.setDate(end.getDate() + 1);
                    }

                    const diffInMs = end - start;
                    const hours = diffInMs / (1000 * 60 * 60);

                    // Round to nearest 0.5
                    const roundedHours = Math.round(hours * 2) / 2;
                    if (roundedHours >= 0.5 && roundedHours <= 24) {
                        durationInput.value = roundedHours;
                        durationInput.classList.remove('is-invalid');
                    } else {
                        durationInput.value = '';
                        durationInput.classList.add('is-invalid');
                        durationInput.nextElementSibling.textContent = 'Duration must be between 0.5 and 24 hours.';
                    }
                } else {
                    durationInput.value = '';
                }
            }

            startTimeInput.addEventListener('change', calculateDuration);
            endTimeInput.addEventListener('change', calculateDuration);
        })();
    </script>

    <!-- Optional CSS for better UX -->
    <style>
        input[type="text"].flatpickr-input {
            cursor: pointer;
        }
        .flatpickr-calendar {
            z-index: 9999 !important;
        }
        .flatpickr-input.form-control {
            background-color: #fff;
        }
        .flatpickr-input.form-control.is-invalid + .flatpickr-calendar {
            border: 1px solid #dc3545;
        }
    </style>
@endsection
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
                    name="overtime_date" value="{{ old('overtime_date', $overtime->overtime_date->format('Y-m-d')) }}" required>
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
                        name="start_time" value="{{ old('start_time', date('H:i', strtotime($overtime->start_time))) }}" required>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                        name="end_time" value="{{ old('end_time', date('H:i', strtotime($overtime->end_time))) }}" required>
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
                    value="{{ old('duration', $overtime->duration) }}" required readonly>
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

            <!-- Cancel and Submit Buttons -->
            <div class="mb-3">
                <a href="{{ route('over-time.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Request</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap validation and duration calculation script -->
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

            // Auto-calculate duration based on start_time and end_time
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const durationInput = document.getElementById('duration');

            function convertTo24Hour(timeStr) {
                const [time, modifier] = timeStr.split(' ');
                let [hours, minutes] = time.split(':');
                if (modifier && modifier.toUpperCase() === 'PM' && hours !== '12') hours = (parseInt(hours) + 12).toString();
                if (modifier && modifier.toUpperCase() === 'AM' && hours === '12') hours = '00';
                return `${hours.padStart(2, '0')}:${minutes}`;
            }

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
                        if (durationInput.nextElementSibling) {
                            durationInput.nextElementSibling.textContent = '';
                        }
                    } else {
                        durationInput.value = '';
                        durationInput.classList.add('is-invalid');
                        if (durationInput.nextElementSibling) {
                            durationInput.nextElementSibling.textContent = 'Duration must be between 0.5 and 24 hours.';
                        }
                    }
                } else {
                    durationInput.value = '';
                }
            }

            startTimeInput.addEventListener('change', calculateDuration);
            endTimeInput.addEventListener('change', calculateDuration);

            // Trigger initial calculation if values are pre-filled
            calculateDuration();
        })();
    </script>

    <!-- Modernized CSS for better UX -->
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            background-color: #fff;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #64748b;
            border-color: #64748b;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #475569;
            border-color: #475569;
            transform: translateY(-1px);
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #ef4444;
            border-radius: 8px;
            padding: 1rem;
            color: #b91c1c;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        @media (max-width: 576px) {
            .container {
                padding: 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .btn {
                padding: 0.6rem 1.2rem;
            }
        }
    </style>
@endsection
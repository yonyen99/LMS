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
                        <select name="leave_type_id" id="leave_type_id" class="form-select" style="cursor: pointer;" required>
                            <option value="">Select leave type</option>
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                    {{ $leaveType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="duration" class="form-label">Duration (days)</label>
                        <input type="number" name="duration" id="duration" class="form-control" style="cursor: pointer;" value="{{ old('duration') }}" placeholder="e.g. 1.5" step="0.5" readonly>
                    </div>

                    <!-- Start Date/Time Row -->
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            id="start_date" 
                            class="form-control" 
                            value="{{ old('start_date') }}" 
                            style="cursor: pointer;"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Start Time</label>
                        <select name="start_time" id="start_time" class="form-select" style="cursor: pointer;" required>
                            <option value="">Select time</option>
                            <option value="morning" {{ old('start_time') == 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ old('start_time') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>

                    <!-- End Date/Time Row -->
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            id="end_date" 
                            class="form-control" 
                            value="{{ old('end_date') }}" 
                            style="cursor: pointer;"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label">End Time</label>
                        <select name="end_time" id="end_time" class="form-select" style="cursor: pointer;" required>
                            <option value="">Select time</option>
                            <option value="morning" {{ old('end_time') == 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ old('end_time') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>

                    <!-- Visual Day Range Indicator -->
                    <div class="col-12 mt-2" id="day-range-container" style="display: none;">
                        <label class="form-label">Leave Range</label>
                        <div class="d-flex flex-wrap gap-2" id="day-range-visual">
                            <!-- Squares will be added here by JavaScript -->
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea name="reason" id="reason" class="form-control" style="cursor: pointer;" rows="3" placeholder="Enter reason">{{ old('reason') }}</textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const durationInput = document.getElementById('duration');
    const dayRangeContainer = document.getElementById('day-range-container');
    const dayRangeVisual = document.getElementById('day-range-visual');

    function calculateDuration() {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const startT = startTime.value.toLowerCase();
        const endT = endTime.value.toLowerCase();

        if (isNaN(start) || isNaN(end) || !startT || !endT) {
            durationInput.value = '';
            dayRangeContainer.style.display = 'none';
            return;
        }

        let daysBetween = (end - start) / (1000 * 60 * 60 * 24);
        if (daysBetween < 0) {
            durationInput.value = '';
            dayRangeContainer.style.display = 'none';
            return;
        }

        let duration = daysBetween + 1; // Add 1 because the difference between same dates is 0 but it's 1 day

        if (startT === 'afternoon') duration -= 0.5;
        if (endT === 'afternoon') duration += 0.5;
        else if (endT === 'morning') duration -= 0.5;

        // Ensure duration is at least 0.5 (half day)
        duration = Math.max(0.5, duration);

        durationInput.value = duration.toFixed(1);
        updateDayRangeVisual(start, end, startT, endT);
    }

    function updateDayRangeVisual(start, end, startT, endT) {
        dayRangeVisual.innerHTML = '';
        
        if (start.toDateString() === end.toDateString()) {
            // Single day
            dayRangeContainer.style.display = 'block';
            const dayDiv = document.createElement('div');
            dayDiv.className = 'day-square';
            if (startT === 'morning' && endT === 'afternoon') {
                dayDiv.classList.add('full-day');
            } else {
                dayDiv.classList.add('half-day');
            }
            dayRangeVisual.appendChild(dayDiv);
        } else {
            // Multiple days
            dayRangeContainer.style.display = 'block';
            const currentDate = new Date(start);
            
            while (currentDate <= end) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'day-square';
                
                if (currentDate.toDateString() === start.toDateString()) {
                    // First day
                    dayDiv.classList.add(startT === 'morning' ? 'full-day' : 'half-day');
                } else if (currentDate.toDateString() === end.toDateString()) {
                    // Last day
                    dayDiv.classList.add(endT === 'afternoon' ? 'full-day' : 'half-day');
                } else {
                    // Middle days
                    dayDiv.classList.add('full-day');
                }
                
                dayRangeVisual.appendChild(dayDiv);
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }
    }

    startDate.addEventListener('change', calculateDuration);
    endDate.addEventListener('change', calculateDuration);
    startTime.addEventListener('change', calculateDuration);
    endTime.addEventListener('change', calculateDuration);
});
</script>

<style>
.day-square {
    width: 30px;
    height: 30px;
    border: 1px solid #333;
    display: inline-block;
}

.full-day {
    background-color: black;
}

.half-day {
    background: linear-gradient(to right, black 50%, white 50%);
}
</style>
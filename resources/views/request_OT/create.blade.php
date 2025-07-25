@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add New OT Request</h5>
                            <a href="{{ route('ot.index') }}" class="btn btn-light btn-sm">← Back to OT List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('ot.store') }}" method="POST">
                            @csrf

                            <div class="mb-4 row">
                                <label for="employee_name" class="col-md-4 col-form-label text-md-end text-start fw-bold">Employee Name</label>
                                <div class="col-md-6">
                                    <input type="text" id="employee_name" name="employee_name" class="form-control @error('employee_name') is-invalid @enderror" value="{{ old('employee_name') }}" required>
                                    @error('employee_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="department" class="col-md-4 col-form-label text-md-end text-start fw-bold">Department</label>
                                <div class="col-md-6">
                                    <select id="department" name="department" class="form-select @error('department') is-invalid @enderror" required>
                                        <option value="">Select Department</option>
                                        <option value="IT" {{ old('department') == 'IT' ? 'selected' : '' }}>IT</option>
                                        <option value="Finance" {{ old('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                        <option value="HR" {{ old('department') == 'HR' ? 'selected' : '' }}>HR</option>
                                        <option value="Operations" {{ old('department') == 'Operations' ? 'selected' : '' }}>Operations</option>
                                        <option value="Marketing" {{ old('department') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    </select>
                                    @error('department')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="date" class="col-md-4 col-form-label text-md-end text-start fw-bold">Date</label>
                                <div class="col-md-6">
                                    <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                    @error('date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="start_time" class="col-md-4 col-form-label text-md-end text-start fw-bold">Start Time</label>
                                <div class="col-md-6">
                                    <input type="time" id="start_time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="end_time" class="col-md-4 col-form-label text-md-end text-start fw-bold">End Time</label>
                                <div class="col-md-6">
                                    <input type="time" id="end_time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                                    @error('end_time')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="reason" class="col-md-4 col-form-label text-md-end text-start fw-bold">Reason for Overtime</label>
                                <div class="col-md-6">
                                    <textarea id="reason" name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-paper-plane me-1"></i> Submit Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
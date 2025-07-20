@extends('layouts.app')

@section('content')
    @canany(['create-product', 'edit-product', 'delete-product'])
        <div class="">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="">
                        <div class="card-header custom-header">{{ __('Welcome Admin Dashboard') }}</div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card card-1 p-3">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <a class="btn btn-primary w-100" href="{{ route('roles.index') }}"
                                                    style="color: white;">
                                                    <i class="bi bi-person-fill-gear"></i> Manage Roles
                                                </a>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-success w-100" href="{{ route('users.index') }}"
                                                    style="color: white;">
                                                    <i class="bi bi-people"></i> Manage Users
                                                </a>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-warning w-100" href="{{ route('departments.index') }}"
                                                    style="color: white;">
                                                    <i class="bi bi-building"></i> Manage Departments
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-primary text-white mb-2">
                                                                <i class="bi bi-person-fill" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Manager</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">5</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-success text-white mb-2">
                                                                <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Employee</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">30
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-warning text-white mb-2">
                                                                <i class="bi bi-building-fill" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Department</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">10
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-danger text-white mb-2">
                                                                <i class="bi bi-calendar-x" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Leave</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">20
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-info text-white mb-2">
                                                                <i class="bi bi-clipboard-check" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Request</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">30
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-secondary text-white mb-2">
                                                                <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Approved </h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">15
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-1 p-3">
                                        <h5>Employee Request</h5>
                                        <canvas id="employeeChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card  card-1 p-3 d-flex justify-content-center">
                                        <h5>Department Request</h5>
                                        <canvas id="departmentChart" width="400" height="200"
                                            style="max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card card-1 p-4  rounded-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-box bg-primary text-white d-flex justify-content-center align-items-center rounded-circle"
                                                style="width: 50px; height: 50px;">
                                                <i class="bi bi-person-fill" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <h5 class="ms-3 mb-0">User Login</h5>
                                        </div>
                                        <div class="ps-1">
                                            <p class="mb-2 d-flex align-items-center">
                                                <span class="me-2"><strong>Active:</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcanany
        @endsection

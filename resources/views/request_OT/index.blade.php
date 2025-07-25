@extends('layouts.app')

@section('title', 'OT Work Management Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Header Bar -->
        <div class="bg-primary text-white p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">OT Work Management Dashboard</h1>
                <a href="{{ route('ot.create') }}" class="btn btn-outline-light">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New OT Request
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded p-3 me-3">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h2 mb-0">24</h3>
                            <p class="text-muted mb-0">Total Requests</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white rounded p-3 me-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h2 mb-0">18</h3>
                            <p class="text-muted mb-0">Approved Requests</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded p-3 me-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h2 mb-0">4</h3>
                            <p class="text-muted mb-0">Pending Requests</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded p-3 me-3">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <h3 class="h2 mb-0">156</h3>
                            <p class="text-muted mb-0">Total Hours This Month</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Overtime Requests</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Request ID</th>
                            <th>Date</th>
                            <th>Time Period</th>
                            <th>Hours</th>
                            <th>Department</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px;">
                                        <small class="fw-bold">JS</small>
                                    </div>
                                    <span>John Smith</span>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="text-primary fw-bold text-decoration-none">OT-2024-001</a>
                            </td>
                            <td>Jan 15, 2024</td>
                            <td>18:00 - 22:00</td>
                            <td><span class="badge bg-secondary">4.0h</span></td>
                            <td><span class="badge bg-info">IT</span></td>
                            <td>System maintenance</td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Approved
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit
                                                Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i
                                                    class="bi bi-download me-2"></i>Download Report</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell me-2"></i>Send
                                                Notification</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i>Delete Request</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px;">
                                        <small class="fw-bold">MJ</small>
                                    </div>
                                    <span>Mary Johnson</span>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="text-primary fw-bold text-decoration-none">OT-2024-002</a>
                            </td>
                            <td>Jan 16, 2024</td>
                            <td>17:30 - 20:30</td>
                            <td><span class="badge bg-secondary">3.0h</span></td>
                            <td><span class="badge bg-info">Finance</span></td>
                            <td>Month-end closing</td>
                            <td>
                                <span class="badge bg-warning">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Pending
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit
                                                Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-success" href="#"><i
                                                    class="bi bi-check-circle me-2"></i>Approve Request</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-x-circle me-2"></i>Reject Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell me-2"></i>Send
                                                Notification</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i>Delete Request</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px;">
                                        <small class="fw-bold">RB</small>
                                    </div>
                                    <span>Robert Brown</span>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="text-primary fw-bold text-decoration-none">OT-2024-003</a>
                            </td>
                            <td>Jan 17, 2024</td>
                            <td>19:00 - 23:00</td>
                            <td><span class="badge bg-secondary">4.0h</span></td>
                            <td><span class="badge bg-info">Operations</span></td>
                            <td>Emergency repair</td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Approved
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit
                                                Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i
                                                    class="bi bi-download me-2"></i>Download Report</a></li>
                                        <li><a class="dropdown-item" href="#"><i
                                                    class="bi bi-files me-2"></i>Duplicate Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell me-2"></i>Send
                                                Notification</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i>Delete Request</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px;">
                                        <small class="fw-bold">SD</small>
                                    </div>
                                    <span>Sarah Davis</span>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="text-primary fw-bold text-decoration-none">OT-2024-004</a>
                            </td>
                            <td>Jan 18, 2024</td>
                            <td>18:30 - 21:00</td>
                            <td><span class="badge bg-secondary">2.5h</span></td>
                            <td><span class="badge bg-info">HR</span></td>
                            <td>Recruitment drive</td>
                            <td>
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Rejected
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit
                                                Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-success" href="#"><i
                                                    class="bi bi-check-circle me-2"></i>Reopen & Approve</a></li>
                                        <li><a class="dropdown-item" href="#"><i
                                                    class="bi bi-info-circle me-2"></i>View Rejection Reason</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell me-2"></i>Send
                                                Notification</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i>Delete Request</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px;">
                                        <small class="fw-bold">TW</small>
                                    </div>
                                    <span>Tom Wilson</span>
                                </div>
                            </td>
                            <td>
                                <a href="#" class="text-primary fw-bold text-decoration-none">OT-2024-005</a>
                            </td>
                            <td>Jan 19, 2024</td>
                            <td>20:00 - 02:00</td>
                            <td><span class="badge bg-secondary">6.0h</span></td>
                            <td><span class="badge bg-info">IT</span></td>
                            <td>Server migration</td>
                            <td>
                                <span class="badge bg-warning">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Pending
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit
                                                Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-success" href="#"><i
                                                    class="bi bi-check-circle me-2"></i>Approve Request</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-x-circle me-2"></i>Reject Request</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell me-2"></i>Send
                                                Notification</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i
                                                    class="bi bi-trash me-2"></i>Delete Request</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing 1 to 5 of 24 entries
            </div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>



    <!-- Success Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Operation completed successfully!
            </div>
        </div>
    </div>
@endsection

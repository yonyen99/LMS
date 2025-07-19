<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave History - Leave Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg bg-primary text-white" aria-label="Main navigation">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-white me-2 fs-4" aria-hidden="true"></i>
                <a class="navbar-brand text-white" href="#">Leave Management</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Request
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">New Request</a></li>
                            <li><a class="dropdown-item" href="#">My Requests</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Calendars
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Team Calendar</a></li>
                            <li><a class="dropdown-item" href="#">Company Calendar</a></li>
                        </ul>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <a href="#" class="btn btn-warning me-2">
                        <i class="fas fa-plus me-1" aria-hidden="true"></i> New Request
                    </a>
                    <div class="d-flex align-items-center text-white">
                        <span class="d-none d-md-inline">Chhea</span>
                        <img src="https://via.placeholder.com/32x32/007bff/ffffff?text=C" 
                             alt="User Avatar" class="rounded-circle ms-2" width="32" height="32">
                        <i class="fas fa-sign-out-alt ms-2" aria-label="Log out"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Leave History Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <h4 class="mb-3 mb-md-0">Leave History</h4>
                    <!-- Status Filter Buttons -->
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-secondary">
                            <i class="fas fa-calendar me-1" aria-hidden="true"></i> Plans
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-check me-1" aria-hidden="true"></i> Accepted
                        </button>
                        <button class="btn btn-warning">
                            <i class="fas fa-clock me-1" aria-hidden="true"></i> Requested
                        </button>
                        <button class="btn btn-danger">
                            <i class="fas fa-times me-1" aria-hidden="true"></i> Rejected
                        </button>
                        <button class="btn btn-danger">
                            <i class="fas fa-ban me-1" aria-hidden="true"></i> Cancellation
                        </button>
                        <button class="btn btn-dark">
                          <i class="fas fa-times-circle me-1" aria-hidden="true"></i> Cancelled
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row mb-3 align-items-center">
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <select class="form-select" aria-label="Select number of entries">
                    <option>Show</option>
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>
            <div class="col-12 col-md-4 offset-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." aria-label="Search leave history">
                    <button class="btn btn-primary" type="button" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Leave History Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Start Date</th>
                            <th scope="col" class="d-none d-md-table-cell">End Date</th>
                            <th scope="col">Reason</th>
                            <th scope="col" class="d-none d-md-table-cell">Duration</th>
                            <th scope="col" class="d-none d-md-table-cell">Type</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>16/1/2025</td>
                            <td class="d-none d-md-table-cell">17/1/2025</td>
                            <td>Medical Leave</td>
                            <td class="d-none d-md-table-cell">1</td>
                            <td class="d-none d-md-table-cell">Paid</td>
                            <td><span class="badge bg-success">Accepted</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>16/1/2025</td>
                            <td class="d-none d-md-table-cell">16/1/2025</td>
                            <td>Personal Leave</td>
                            <td class="d-none d-md-table-cell">2</td>
                            <td class="d-none d-md-table-cell">Paid</td>
                            <td><span class="badge bg-success">Accepted</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>16/1/2025</td>
                            <td class="d-none d-md-table-cell">16/1/2025</td>
                            <td>Medical Leave</td>
                            <td class="d-none d-md-table-cell">1</td>
                            <td class="d-none d-md-table-cell">Paid</td>
                            <td><span class="badge bg-success">Accepted</span></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>16/1/2025</td>
                            <td class="d-none d-md-table-cell">16/1/2025</td>
                            <td>Family Emergency</td>
                            <td class="d-none d-md-table-cell">1</td>
                            <td class="d-none d-md-table-cell">Paid</td>
                            <td><span class="badge bg-success">Accepted</span></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>16/1/2025</td>
                            <td class="d-none d-md-table-cell">16/1/2025</td>
                            <td>Medical Leave</td>
                            <td class="d-none d-md-table-cell">1</td>
                            <td class="d-none d-md-table-cell">Paid</td>
                            <td><span class="badge bg-success">Accepted</span></td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>16/1/2025</td>
                            <td class="d-none d-md-table-cell">16/1/2025</td>
                            <td>Vacation</td>
                            <td class="d-none d-md-table-cell">1</td>
                            <td class="d-none d-md-table-cell">Paid</td>
                            <td><span class="badge bg-success">Accepted</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-3">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active" aria-current="page">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
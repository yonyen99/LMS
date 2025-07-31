@extends('layouts.app')

@section('content')
    @canany(['create-department', 'edit-department', 'delete-department'])
        <div class="">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="">
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card card-1 bg-white p-3">
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
                                            <div class="col-md-2">
                                                <a class="btn bg-danger w-100" href="{{ route('leave-summaries.index') }}"
                                                    style="color: white;">
                                                    <i class="bi bi-calculator"></i> Manage Counter Request
                                                </a>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn bg-info w-100" href="{{ route('leave-types.index') }}"
                                                    style="color: white;">
                                                    <i class="bi bi-clipboard-check"></i> Manage Leave Types
                                                </a>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn bg-secondary w-100" href="{{ route('notifications.index') }}"
                                                    style="color: white;">
                                                    <i class="bi bi-bell-fill"></i> All Leave Request
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body bg-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-primary text-white mb-2">
                                                                <i class="bi bi-person-fill" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Manager</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">
                                                                {{ $totalManagers }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body bg-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-success text-white mb-2">
                                                                <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Employee</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">
                                                                {{ $totalEmployees }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body bg-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-warning text-white mb-2">
                                                                <i class="bi bi-building-fill" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Department</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">
                                                                {{ $totalDepartments }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body bg-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-danger text-white mb-2">
                                                                <i class="bi bi-calendar-x" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Leave</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">
                                                                {{ $totalLeaves }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body  bg-white ">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-info text-white mb-2">
                                                                <i class="bi bi-clipboard-check" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Request</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">
                                                                {{ $totalRequests }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <div class="card custom-card h-100">
                                                    <div class="card-body bg-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-box bg-secondary text-white mb-2">
                                                                <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                                                            </div>
                                                            <h6 class="card-title mb-1 p-2" style="font-size: 0.9rem;">Total
                                                                Approved</h6>
                                                            <p class="card-text display-6 mb-0" style="font-size: 1.5rem;">
                                                                {{ $totalApproved }}</p>
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
                                    <div class="card bg-white card-1 p-3">
                                        <h5>Employee Request</h5>
                                        <canvas id="employeeChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-white card-1 p-3">
                                        <h5>Members per Department</h5>
                                        <div class="card-3">
                                            @if (!empty($departmentData))
                                                <canvas id="departmentChart" width="400" height="200"
                                                    style="max-width: 100%;"></canvas>
                                            @else
                                                <p class="text-muted text-center">No department data available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card bg-white card-1 p-4 rounded-3">
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
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    @endcanany
    @if ((auth()->user()->hasRole('Employee') || auth()->user()->hasRole('Manager')) &&
                    !auth()->user()->hasRole('Admin') &&
                    !auth()->user()->hasRole('Super Admin'))
                <div class="m-2">
                    <div class="card card-1 p-4 mb-4">
                        <form method="GET" action="{{ route('leave-requests.index') }}">
                            <div>
                                <div class="d-flex align-items-center justify-content-start flex-wrap gap-4">
                                    <h2 class="fw-bold mb-0 me-2">My leave requests</h2>
                                </div>
                                <div class="d-flex align-items-center justify-content-start flex-wrap gap-4 mt-4">
                                    <div class="d-flex align-items-center border rounded px-2" style="width:30%;">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="form-control border-0" placeholder="Search request...">
                                        <i class="bi bi-search text-primary"></i>
                                    </div>

                                    @php
                                        $statuses = [
                                            'Planned',
                                            'Accepted',
                                            'Requested',
                                            'Rejected',
                                            'Cancellation',
                                            'Canceled',
                                        ];
                                        $colors = [
                                            'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
                                            'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
                                            'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
                                            'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
                                            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
                                            'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
                                        ];
                                    @endphp

                                    @foreach ($statuses as $status)
                                        @php
                                            $textColor = $colors[$status]['text'];
                                            $bgColor = $colors[$status]['bg'];
                                        @endphp
                                        <div>
                                            <label for="status_{{ $status }}"
                                                class="d-flex align-items-center fw-semibold"
                                                style="color: {{ $textColor }}; background-color: {{ $bgColor }};
                                        padding: 0.25em 0.7em; border-radius: 0.3rem; cursor: pointer;">
                                                <input type="checkbox" name="statuses[]" value="{{ $status }}"
                                                    id="status_{{ $status }}"
                                                    {{ !request()->has('statuses') || in_array($status, request()->input('statuses', [])) ? 'checked' : '' }}
                                                    onchange="this.form.submit()" class="form-check-input me-2 mb-1"
                                                    style="width: 1.2em; height: 1.1em;">
                                                {{ $status }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-4 align-items-end mt-4 mb-2">

                                <div class="d-flex align-items-center gap-2" style="width:20%;">
                                    <label for="statusRequest" class="fw-semibold mb-0" style="width:80%;">Status
                                        Request</label>
                                    <select class="form-select" id="statusRequest" name="status_request"
                                        onchange="this.form.submit()">
                                        <option value="">All</option>
                                        @foreach ($statusRequestOptions as $status)
                                            <option value="{{ $status }}"
                                                {{ request('status_request') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex align-items-center gap-2" style="width:15%;">
                                    <label for="type" class="fw-semibold mb-0" style="width:30%;">Type</label>
                                    <select class="form-select flex-grow-1" id="type" name="type"
                                        onchange="this.form.submit()">
                                        <option value="">All</option>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type }}"
                                                {{ request('type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex align-items-center gap-2 mt-2" style="width:20%;">
                                    <label for="showRequest" class="fw-semibold mb-0" style="width:75%;">Show
                                        Request</label>
                                    <select class="form-select" id="showRequest" name="sort_order"
                                        onchange="this.form.submit()">
                                        <option value="new" {{ request('sort_order') == 'new' ? 'selected' : '' }}>
                                            Newest</option>
                                        <option value="last" {{ request('sort_order') == 'last' ? 'selected' : '' }}>
                                            Oldest</option>
                                    </select>
                                </div>

                                @can('export', \App\Models\LeaveRequest::class)
                                    <a href="{{ route('leave-requests.exportPDF', [
                                        'statuses' => request('statuses', []),
                                        'type' => request('type'),
                                        'status_request' => request('status_request'),
                                        'search' => request('search'),
                                        'sort_order' => request('sort_order', 'new'),
                                    ]) }}"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                                    </a>

                                    <a href="{{ route('leave-requests.exportExcel', [
                                        'statuses' => request('statuses', []),
                                        'type' => request('type'),
                                        'status_request' => request('status_request'),
                                        'search' => request('search'),
                                        'sort_order' => request('sort_order', 'new'),
                                    ]) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                                    </a>
                                    <a href="{{ route('leave-requests.print', [
                                        'statuses' => request('statuses', []),
                                        'type' => request('type'),
                                        'status_request' => request('status_request'),
                                        'search' => request('search'),
                                        'sort_order' => request('sort_order', 'new'),
                                    ]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-printer me-1"></i> Print
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled
                                        title="You don't have permission to export">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                                    </button>

                                    <button class="btn btn-sm btn-secondary" disabled
                                        title="You don't have permission to export">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                                    </button>

                                    <button class="btn btn-sm btn-secondary" disabled
                                        title="You don't have permission to print">
                                        <i class="bi bi-printer me-1"></i> Print
                                    </button>
                                @endcan
                            </div>
                        </form>
                    </div>

                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Duration</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Last Change</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($leaveRequests->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No leave requests found.</td>
                                </tr>
                            @else
                                @foreach ($leaveRequests as $request)
                                    @php
                                        $startDate = $request->start_date
                                            ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y')
                                            : '-';
                                        $endDate = $request->end_date
                                            ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y')
                                            : '-';
                                        $requestedAt = $request->requested_at
                                            ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y')
                                            : '-';
                                        $lastChangedAt = $request->last_changed_at
                                            ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y')
                                            : '-';
                                        $displayStatus = ucfirst(strtolower($request->status));
                                        $colors = $statusColors[$displayStatus] ?? [
                                            'text' => '#000000',
                                            'bg' => '#e0e0e0',
                                        ];
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + $loop->iteration }}
                                        </td>
                                        <td>{{ optional($request->start_date)->format('d/m/Y') }}
                                            ({{ ucfirst($request->start_time) }})
                                        </td>
                                        <td>{{ optional($request->end_date)->format('d/m/Y') }}
                                            ({{ ucfirst($request->end_time) }})</td>
                                        <td>{{ $request->reason ?? '-' }}</td>
                                        <td>{{ number_format($request->duration, 2) }}</td>
                                        <td>{{ optional($request->leaveType)->name ?? '-' }}</td>
                                        <td>
                                            <span
                                                style="
                                                color: {{ $colors['text'] }};
                                                background-color: {{ $colors['bg'] }};
                                                padding: 2px 8px;
                                                border-radius: 10px;
                                                font-weight: 500;
                                                display: inline-block;
                                            ">
                                                {{ $displayStatus }}
                                            </span>
                                        </td>
                                        <td>{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>{{ $request->last_changed_at ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button" id="actionsDropdown{{ $request->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true"
                                                    aria-label="Actions for request #{{ $request->id }}"
                                                    style="min-width: 50px;">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="actionsDropdown{{ $request->id }}">
                                                    <li>
                                                        <button type="button" class="dropdown-item d-flex align-items-center view-request"
                                                            data-bs-toggle="modal" data-bs-target="#leaveRequestModal"
                                                            data-type="{{ $request->leaveType->name }}"
                                                            data-duration="{{ $request->duration }}"
                                                            data-start-date="{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}"
                                                            data-start-time="{{ $request->start_time }}"
                                                            data-end-date="{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}"
                                                            data-end-time="{{ $request->end_time }}"
                                                            data-reason="{{ $request->reason }}"
                                                            data-status="{{ $request->status }}">
                                                            <i class="bi bi-eye me-2 text-primary"></i> View
                                                        </button>
                                                    </li>

                                                    @php
                                                        $showHistoryStatuses = [
                                                            'accepted',
                                                            'rejected',
                                                            'canceled',
                                                            'cancellation',
                                                        ];
                                                    @endphp

                                                    @if (in_array(strtolower($request->status), $showHistoryStatuses))
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('leave-requests.history', $request->id) }}">
                                                                <i
                                                                    class="bi bi-arrow-counterclockwise me-2 text-primary"></i>
                                                                History
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @php
                                                        $showStatuses = ['accepted', 'rejected', 'canceled', 'cancellation', 'requested'];
                                                    @endphp

                                                    @if (!in_array(strtolower($request->status), $showStatuses))

                                                        <li>
                                                            <form action="{{ route('leave-requests.update-status', $request->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" name="status" value="requested" class="btn d-flex align-items-center" style="color: orange;">
                                                                    <i class="bi bi-check2-circle me-2"></i> Requested
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    
                                                    @php
                                                        $showHistoryStatuses = [
                                                            'accepted',
                                                            'rejected',
                                                            'canceled',
                                                            'cancellation',
                                                        ];
                                                    @endphp
                                                    @if (!in_array(strtolower($request->status), $showHistoryStatuses))
                                                        <li>
                                                            <form
                                                                action="{{ route('leave-requests.destroy', $request->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center text-danger">
                                                                    <i class="bi bi-trash me-2"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>

                                                        <li>
                                                            <form
                                                                action="{{ route('leave-requests.cancel', $request->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center text-secondary">
                                                                    <i class="bi bi-x-circle me-2"></i> Cancel
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if ($leaveRequests->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $leaveRequests->firstItem() }} to {{ $leaveRequests->lastItem() }} of
                                {{ $leaveRequests->total() }} entries
                            </div>
                            <div>
                                {{ $leaveRequests->onEachSide(1)->links() }}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" style="width: 40%;">
                        <div class="modal-content shadow rounded border-0">
                            <div class="modal-header text-white" style="background-color: green">
                                <h5 class="modal-title">
                                    <i class="bi bi-file-text me-2"></i> Leave Request Details
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body px-4 pb-4">
                                <dl class="row mb-0" style="margin-left: 10px;">
                                    <dt class="col-sm-5 text-lg">Leave Type</dt>
                                    <dd class="col-sm-7 fs-6" id="modalType">-</dd>

                                    <dt class="col-sm-5 text-lg">Duration (days)</dt>
                                    <dd class="col-sm-7 fs-6" id="modalDuration">-</dd>

                                    <dt class="col-sm-5 text-lg">Start Date & Time</dt>
                                    <dd class="col-sm-7 fs-6" id="modalStart">-</dd>

                                    <dt class="col-sm-5 text-lg">End Date & Time</dt>
                                    <dd class="col-sm-7 fs-6" id="modalEnd">-</dd>

                                    <dt class="col-sm-5 text-lg">Reason</dt>
                                    <dd class="col-sm-7 fs-6">
                                        <pre id="modalReason" class="mb-0" style="white-space: pre-wrap;">-</pre>
                                    </dd>

                                    <dt class="col-sm-5 text-lg">Status</dt>
                                    <dd class="col-sm-7 fs-6" id="modalStatus">
                                        <span class="badge bg-secondary text-white">-</span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <script>
                const departmentData = @json($departmentData);
            </script>
 </section>
@endsection

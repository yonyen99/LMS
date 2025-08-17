@extends('layouts.app')

@section('content')
    @canany(['create-department', 'edit-department', 'delete-department'])
        <div class="container-fluid ">
            <div class="card  card-2 border-1 shadow">
                <div class="card-body">

                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    {{-- Quick Action Buttons --}}
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-6 g-3 mb-4 ">
                        <div class="col">
                            <a href="{{ route('roles.index') }}" class="btn btn-primary w-100 text-white">
                                <i class="bi bi-person-fill-gear me-1"></i> <span class="d-none d-md-inline">Manage Roles</span>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('users.index') }}" class="btn btn-success w-100 text-white">
                                <i class="bi bi-people me-1"></i> <span class="d-none d-md-inline">Manage Users</span>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('departments.index') }}" class="btn btn-warning w-100 text-white">
                                <i class="bi bi-building me-1"></i> <span class="d-none d-md-inline">Departments</span>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('leave-summaries.index') }}" class="btn btn-danger w-100 text-white">
                                <i class="bi bi-calculator me-1"></i> <span class="d-none d-md-inline">Counter</span>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('leave-types.index') }}" class="btn btn-info w-100 text-white">
                                <i class="bi bi-clipboard-check me-1"></i> <span class="d-none d-md-inline">Leave Types</span>
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('notifications.index') }}" class="btn btn-secondary w-100 text-white">
                                <i class="bi bi-bell-fill me-1"></i> <span class="d-none d-md-inline">All Requests</span>
                            </a>
                        </div>
                    </div>

                    {{-- Statistics Cards --}}
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-6 g-3 mb-4">
                        @php
                            $cards = [
                                [
                                    'label' => 'Manager',
                                    'count' => $totalManagers,
                                    'icon' => 'person-fill',
                                    'color' => 'primary',
                                ],
                                [
                                    'label' => 'Employee',
                                    'count' => $totalEmployees,
                                    'icon' => 'people-fill',
                                    'color' => 'success',
                                ],
                                [
                                    'label' => 'Department',
                                    'count' => $totalDepartments,
                                    'icon' => 'building-fill',
                                    'color' => 'warning',
                                ],
                                [
                                    'label' => 'Leave',
                                    'count' => $totalLeaves,
                                    'icon' => 'calendar-x',
                                    'color' => 'danger',
                                ],
                                [
                                    'label' => 'Request',
                                    'count' => $totalRequests,
                                    'icon' => 'clipboard-check',
                                    'color' => 'info',
                                ],
                                [
                                    'label' => 'Approved',
                                    'count' => $totalApproved,
                                    'icon' => 'check-circle',
                                    'color' => 'secondary',
                                ],
                            ];
                        @endphp
                        @foreach ($cards as $card)
                            <div class="col">
                                <div class="card  card-2 text-center h-100 "
                                    style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <div class="bg-{{ $card['color'] }} text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-{{ $card['icon'] }}" style="font-size: 1.2rem;"></i>
                                            </div>
                                        </div>
                                        <h6 class="text-muted mb-1">{{ $card['label'] }}</h6>
                                        <h5 class="fw-bold mb-0">{{ $card['count'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Charts --}}
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="card card-2 h-100"
                                style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Employee Request</h5>
                                    <div class="ratio ratio-16x9">
                                        <canvas id="employeeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card card-2 h-100 "
                                style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Members per Department</h5>
                                    @if (!empty($departmentData))
                                        <div class="ratio ratio-16x9">
                                            <canvas id="departmentChart"></canvas>
                                        </div>
                                    @else
                                        <p class="text-center text-muted">No department data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endcanany

    @if (
        (auth()->user()->hasRole('Employee') || auth()->user()->hasRole('Manager')) &&
            !auth()->user()->hasRole('Admin') &&
            !auth()->user()->hasRole('HR'))
        <div class="m-2">
            <div class="card card-1 card-2 p-3 p-md-4 mb-4">
                <form method="GET" action="{{ route('home') }}">
                    <div class="row align-items-center justify-content-start flex-wrap g-3 g-md-4">
                        <div class="col-auto">
                            <h2 class="fw-bold mb-0">My leave requests</h2>
                        </div>
                    </div>

                    <div class="row align-items-center flex-wrap g-3 g-md-4 mt-2 mt-md-1">
                        {{-- Search Box --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control border" placeholder="Search request..."
                                    aria-label="Search leave requests">
                                <button type="submit" class="input-group-text bg-white border">
                                    <i class="bi bi-search text-primary"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Status Filter --}}
                        @php
                            $statuses = ['Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'];
                            $statusColors = [
                                'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
                                'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
                                'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
                                'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
                                'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
                                'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
                            ];
                        @endphp

                        <div class="col-12 col-md-6 col-lg-8">
                            <div class="d-flex flex-wrap gap-2 gap-md-3">
                                @foreach ($statuses as $status)
                                    @php
                                        $textColor = $statusColors[$status]['text'];
                                        $bgColor = $statusColors[$status]['bg'];
                                    @endphp
                                    <div>
                                        <label for="status_{{ $status }}"
                                            class="d-flex align-items-center fw-semibold px-2 py-1 rounded"
                                            style="color: {{ $textColor }}; background-color: {{ $bgColor }}; cursor: pointer;">
                                            <input type="checkbox" name="statuses[]" value="{{ $status }}"
                                                id="status_{{ $status }}"
                                                {{ !request()->has('statuses') || in_array($status, request()->input('statuses', [])) ? 'checked' : '' }}
                                                onchange="this.form.submit()" class="form-check-input me-2"
                                                style="width: 1.2em; height: 1.1em;">
                                            {{ $status }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4 align-items-end mt-3 mt-md-1 mb-2">
                        {{-- Status Request --}}
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="statusRequest" class="fw-semibold mb-2">Status Request</label>
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

                        {{-- Type --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <label for="type" class="fw-semibold mb-2">Type</label>
                            <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort Order --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <label for="showRequest" class="fw-semibold mb-2">Show Request</label>
                            <select class="form-select" id="showRequest" name="sort_order"
                                onchange="this.form.submit()">
                                <option value="new" {{ request('sort_order') == 'new' ? 'selected' : '' }}>Newest
                                </option>
                                <option value="last" {{ request('sort_order') == 'last' ? 'selected' : '' }}>Oldest
                                </option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            @can('export', \App\Models\LeaveRequest::class)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle w-100" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download me-1"></i> Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                        <li>
                                            <a href="{{ route('leave-requests.exportPDF', [
                                                'statuses' => request('statuses', []),
                                                'type' => request('type'),
                                                'status_request' => request('status_request'),
                                                'search' => request('search'),
                                                'sort_order' => request('sort_order', 'new'),
                                            ]) }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('leave-requests.exportExcel', [
                                                'statuses' => request('statuses', []),
                                                'type' => request('type'),
                                                'status_request' => request('status_request'),
                                                'search' => request('search'),
                                                'sort_order' => request('sort_order', 'new'),
                                            ]) }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('leave-requests.print', [
                                                'statuses' => request('statuses', []),
                                                'type' => request('type'),
                                                'status_request' => request('status_request'),
                                                'search' => request('search'),
                                                'sort_order' => request('sort_order', 'new'),
                                            ]) }}"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="bi bi-printer me-2 text-primary"></i> Print
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle w-100" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled
                                        title="You don't have permission to export">
                                        <i class="bi bi-download me-1"></i> Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center" disabled>
                                                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center" disabled>
                                                <i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center" disabled>
                                                <i class="bi bi-printer me-2 text-primary"></i> Print
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @endcan
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="60px">ID</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                            <th scope="col" class="d-none d-md-table-cell">Reason</th>
                            <th scope="col">Duration</th>
                            <th scope="col" class="d-none d-md-table-cell">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="d-none d-md-table-cell">Requested</th>
                            <th scope="col" class="d-none d-md-table-cell">Last Change</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaveRequests as $request)
                            @php
                                $displayStatus = ucfirst(strtolower($request->status));
                                $badgeColor = match (strtolower($request->status)) {
                                    'approved' => 'success',
                                    'accepted' => 'success',
                                    'requested' => 'warning',
                                    'rejected' => 'danger',
                                    'canceled' => 'secondary',
                                    'cancellation' => 'secondary',
                                    default => 'primary',
                                };
                            @endphp
                            <tr class="transition">
                                <th scope="row">
                                    {{ ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + $loop->iteration }}
                                </th>
                                <td>{{ optional($request->start_date)->format('d/m/Y') }}
                                    ({{ ucfirst($request->start_time) }})
                                </td>
                                <td>{{ optional($request->end_date)->format('d/m/Y') }}
                                    ({{ ucfirst($request->end_time) }})</td>
                                <td class="d-none d-md-table-cell">{{ $request->reason ?? '-' }}</td>
                                <td>{{ number_format($request->duration, 2) }}</td>
                                <td class="d-none d-md-table-cell">{{ optional($request->leaveType)->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $badgeColor }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ optional($request->requested_at)->format('d/m/Y') ?? '-' }}</td>
                                <td class="d-none d-md-table-cell">
                                    {{ optional($request->last_changed_at)->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="actionsDropdown{{ $request->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm"
                                            aria-labelledby="actionsDropdown{{ $request->id }}">
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item d-flex align-items-center gap-2 view-request"
                                                    data-bs-toggle="modal" data-bs-target="#leaveRequestModal"
                                                    data-type="{{ $request->leaveType->name }}"
                                                    data-duration="{{ $request->duration }}"
                                                    data-start-date="{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}"
                                                    data-start-time="{{ $request->start_time }}"
                                                    data-end-date="{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}"
                                                    data-end-time="{{ $request->end_time }}"
                                                    data-reason="{{ $request->reason }}"
                                                    data-status="{{ $request->status }}">
                                                    <i class="bi bi-eye"></i> View
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
                                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                                        href="{{ route('leave-requests.history', $request->id) }}">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        History
                                                    </a>
                                                </li>
                                            @endif

                                            @php
                                                $showStatuses = [
                                                    'accepted',
                                                    'rejected',
                                                    'canceled',
                                                    'cancellation',
                                                    'requested',
                                                ];
                                            @endphp

                                            @if (!in_array(strtolower($request->status), $showStatuses))
                                                <li>
                                                    <form
                                                        action="{{ route('leave-requests.update-status', $request->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" name="status" value="requested"
                                                            class="dropdown-item d-flex align-items-center gap-2 text-warning">
                                                            <i class="bi bi-check2-circle"></i> Request
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            @if (!in_array(strtolower($request->status), $showHistoryStatuses))
                                                <li>
                                                    <form action="{{ route('leave-requests.destroy', $request->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('leave-requests.cancel', $request->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                                        @csrf
                                                        <button type="submit"
                                                            class="dropdown-item d-flex align-items-center gap-2 text-secondary">
                                                            <i class="bi bi-x-circle"></i> Cancel
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-calendar-x fs-1 text-muted mb-2"></i>
                                        <h5 class="text-muted">No leave requests found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaveRequests->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                    <div class="text-muted">
                        Showing {{ $leaveRequests->firstItem() }} to {{ $leaveRequests->lastItem() }} of
                        {{ $leaveRequests->total() }} entries
                    </div>
                    <div>
                        {{ $leaveRequests->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif
    @endif

    <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm modal-md modal-lg">
            <div class="modal-content shadow rounded border-0">
                <div class="modal-header text-white" style="background-color: green">
                    <h5 class="modal-title">
                        <i class="bi bi-file-text me-2"></i> Leave Request Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-3 px-md-4 pb-4">
                    <dl class="row mb-0 g-3">
                        <dt class="col-5 col-md-5 text-lg fw-semibold">Leave Type</dt>
                        <dd class="col-7 col-md-7 fs-6" id="modalType">-</dd>

                        <dt class="col-5 col-md-5 text-lg fw-semibold">Duration (days)</dt>
                        <dd class="col-7 col-md-7 fs-6" id="modalDuration">-</dd>

                        <dt class="col-5 col-md-5 text-lg fw-semibold">Start Date & Time</dt>
                        <dd class="col-7 col-md-7 fs-6" id="modalStart">-</dd>

                        <dt class="col-5 col-md-5 text-lg fw-semibold">End Date & Time</dt>
                        <dd class="col-7 col-md-7 fs-6" id="modalEnd">-</dd>

                        <dt class="col-5 col-md-5 text-lg fw-semibold">Reason</dt>
                        <dd class="col-7 col-md-7 fs-6">
                            <pre id="modalReason" class="mb-0" style="white-space: pre-wrap;">-</pre>
                        </dd>

                        <dt class="col-5 col-md-5 text-lg fw-semibold">Status</dt>
                        <dd class="col-7 col-md-7 fs-6" id="modalStatus">
                            <span class="badge bg-secondary text-white">-</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script>
        const departmentData = @json($departmentData);
    </script>
    </section>
@endsection

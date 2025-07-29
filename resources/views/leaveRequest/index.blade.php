@extends('layouts.app')

@section('content')
    <div class="container-fluid px-2 px-md-4 py-3">
        <div class="card card-1 p-3 p-md-4 mb-4">
            <form method="GET" action="{{ route('leave-requests.index') }}">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3 mb-md-4">
                    <h2 class="fw-bold mb-0">My leave requests</h2>
                </div>

                <!-- Search and Status Filters -->
                <div class="row g-3 g-md-4 mb-4">
                    <!-- Search Box -->
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control border-end-0" placeholder="Search request..." aria-label="Search">
                            <span class="input-group-text bg-white border-start-0">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $statuses = [
                                    'Planned',
                                    'Accepted',
                                    'Requested',
                                    'Rejected',
                                    'Cancellation',
                                    'Canceled',
                                ];
                                $statusColors = [
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
                                    $textColor = $statusColors[$status]['text'];
                                    $bgColor = $statusColors[$status]['bg'];
                                @endphp
                                <label for="status_{{ $status }}"
                                    class="d-flex align-items-center fw-semibold px-2 py-1 rounded"
                                    style="color: {{ $textColor }}; background-color: {{ $bgColor }}; border: none; cursor: pointer;">
                                    <input type="checkbox" name="statuses[]" value="{{ $status }}"
                                        id="status_{{ $status }}"
                                        {{ !request()->has('statuses') || in_array($status, request()->input('statuses', [])) ? 'checked' : '' }}
                                        onchange="this.form.submit()" class="form-check-input me-2"
                                        style="width: 1.2em; height: 1.2em;">
                                    {{ $status }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Additional Filters and Export -->
                <div class="row g-3 g-md-4 align-items-end">
                    <!-- Status Request -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="statusRequest" class="fw-semibold mb-1">Status Request</label>
                        <select class="form-select" id="statusRequest" name="status_request" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach ($statusRequestOptions as $status)
                                <option value="{{ $status }}"
                                    {{ request('status_request') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="type" class="fw-semibold mb-1">Type</label>
                        <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="showRequest" class="fw-semibold mb-1">Show Request</label>
                        <select class="form-select" id="showRequest" name="sort_order" onchange="this.form.submit()">
                            <option value="new" {{ request('sort_order') == 'new' ? 'selected' : '' }}>Newest</option>
                            <option value="last" {{ request('sort_order') == 'last' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>

                    <!-- Export Dropdown -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex justify-content-end">
                            @can('export', \App\Models\LeaveRequest::class)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="exportDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Export Options
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('leave-requests.exportPDF', [
                                                    'statuses' => request('statuses', []),
                                                    'type' => request('type'),
                                                    'status_request' => request('status_request'),
                                                    'search' => request('search'),
                                                    'sort_order' => request('sort_order', 'new'),
                                                ]) }}">
                                                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> Export PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('leave-requests.exportExcel', [
                                                    'statuses' => request('statuses', []),
                                                    'type' => request('type'),
                                                    'status_request' => request('status_request'),
                                                    'search' => request('search'),
                                                    'sort_order' => request('sort_order', 'new'),
                                                ]) }}">
                                                <i class="bi bi-file-earmark-excel me-2 text-success"></i> Export Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('leave-requests.print', [
                                                    'statuses' => request('statuses', []),
                                                    'type' => request('type'),
                                                    'status_request' => request('status_request'),
                                                    'search' => request('search'),
                                                    'sort_order' => request('sort_order', 'new'),
                                                ]) }}">
                                                <i class="bi bi-printer me-2 text-primary"></i> Print
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                        id="exportDropdownDisabled" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                        Export Options
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdownDisabled">
                                        <li><span class="dropdown-item text-muted" title="You don't have permission to export">
                                                <i class="bi bi-file-earmark-pdf me-2"></i> Export PDF
                                            </span></li>
                                        <li><span class="dropdown-item text-muted" title="You don't have permission to export">
                                                <i class="bi bi-file-earmark-excel me-2"></i> Export Excel
                                            </span></li>
                                        <li><span class="dropdown-item text-muted" title="You don't have permission to print">
                                                <i class="bi bi-printer me-2"></i> Print
                                            </span></li>
                                    </ul>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Leave Requests Table -->
        <div class="table-responsive">
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
                    @forelse ($leaveRequests as $request)
                        @php
                            $displayStatus = ucfirst(strtolower($request->status));
                            $colors = $statusColors[$displayStatus] ?? ['text' => '#000000', 'bg' => '#e0e0e0'];
                        @endphp
                        <tr class="text-center">
                            <td>{{ ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + $loop->iteration }}
                            </td>
                            <td>{{ optional($request->start_date)->format('d/m/Y') }}
                                ({{ ucfirst($request->start_time) }})</td>
                            <td>{{ optional($request->end_date)->format('d/m/Y') }} ({{ ucfirst($request->end_time) }})
                            </td>
                            <td>{{ $request->reason ?? '-' }}</td>
                            <td>{{ number_format($request->duration, 2) }}</td>
                            <td>{{ optional($request->leaveType)->name ?? '-' }}</td>
                            <td>
                                <span
                                    style="color: {{ $colors['text'] }}; background-color: {{ $colors['bg'] }};
                                         padding: 2px 8px; border-radius: 10px; font-weight: 500;">
                                    {{ $displayStatus }}
                                </span>
                            </td>
                            <td>{{ optional($request->requested_at)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ optional($request->last_changed_at)->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="actionsDropdown{{ $request->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button type="button"
                                                class="dropdown-item d-flex align-items-center view-request"
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
                                            $showHistoryStatuses = ['accepted', 'rejected', 'canceled', 'cancellation'];
                                        @endphp
                                        @if (in_array(strtolower($request->status), $showHistoryStatuses))
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('leave-requests.history', $request->id) }}">
                                                    <i class="bi bi-arrow-counterclockwise me-2 text-primary"></i> History
                                                </a>
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
                                                        class="dropdown-item d-flex align-items-center text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('leave-requests.cancel', $request->id) }}"
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
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No leave requests found.</td>
                        </tr>
                    @endforelse
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

        <!-- View Modal -->
        <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestModalLabel"
            aria-hidden="true">
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
    @endsection

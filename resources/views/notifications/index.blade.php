@extends('layouts.app')

@section('content')
    <div class="m-2">
        <div class="card card-1 p-3 p-md-4 mb-4">
            <form method="GET" action="{{ route('notifications.index') }}">
                <div class="row align-items-center justify-content-start flex-wrap g-3 g-md-4">
                    <div class="col-auto">
                        <h2 class="fw-bold mb-0">My leave requests</h2>
                    </div>
                </div>

                <div class="row align-items-center flex-wrap g-3 g-md-4 mt-2 mt-md-1">
                    {{-- Search Box --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border"
                                placeholder="Search request..." aria-label="Search leave requests">
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

                <div class="row g-3 g-md-4 align-items-end mt-1 mt-md-1 mb-2">
                    {{-- Status Request --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="statusRequest" class="fw-semibold mb-2">Status Request</label>
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

                    {{-- Type --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="type" class="fw-semibold mb-2">Type</label>
                        <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->name }}"
                                    {{ request('type') == $type->name ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Order --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="showRequest" class="fw-semibold mb-2">Show Request</label>
                        <select class="form-select" id="showRequest" name="sort_order" onchange="this.form.submit()">
                            <option value="new" {{ request('sort_order') == 'new' ? 'selected' : '' }}>Newest</option>
                            <option value="last" {{ request('sort_order') == 'last' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-2">
                        @can('export', \App\Models\LeaveRequest::class)
                            <div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle w-100" type="button" id="exportDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
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

        {{-- Leave Requests Table --}}
        <div class="card card-1 card-2 p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="60px">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                            <th scope="col" class="d-none d-lg-table-cell">Reason</th>
                            <th scope="col">Duration</th>
                            <th scope="col" class="d-none d-md-table-cell">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="d-none d-md-table-cell">Requested</th>
                            <th scope="col" class="d-none d-lg-table-cell">Last Change</th>
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
                            @endphp
                            <tr class="transition">
                                <th scope="row">
                                    {{ ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + $loop->iteration }}
                                </th>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $startDate }} ({{ ucfirst($request->start_time) }})</td>
                                <td>{{ $endDate }} ({{ ucfirst($request->end_time) }})</td>
                                <td class="d-none d-lg-table-cell">{{ $request->reason ?? '-' }}</td>
                                <td>{{ number_format($request->duration, 2) }}</td>
                                <td class="d-none d-md-table-cell">{{ optional($request->leaveType)->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $badgeColor }} rounded-pill">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $requestedAt }}</td>
                                <td class="d-none d-lg-table-cell">{{ $lastChangedAt }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="actionsDropdown{{ $request->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false" aria-label="Actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm"
                                            aria-labelledby="actionsDropdown{{ $request->id }}">
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item d-flex align-items-center gap-2 view-request"
                                                    data-bs-toggle="modal" data-bs-target="#leaveRequestModal"
                                                    data-request-id="{{ $request->id }}">
                                                    <i class="bi bi-eye"></i> View Details
                                                </button>
                                            </li>

                                            @if (auth()->check())
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li class="dropdown-header">Change Status</li>
                                                @foreach (['Accepted', 'Rejected', 'Cancellation', 'Canceled'] as $newStatus)
                                                    @php
                                                        $statusIcon = match ($newStatus) {
                                                            'Accepted' => 'bi-check-circle',
                                                            'Rejected' => 'bi-x-circle',
                                                            'Cancellation' => 'bi-slash-circle',
                                                            'Canceled' => 'bi-dash-circle',
                                                            default => 'bi-arrow-repeat',
                                                        };
                                                        $statusColor = match ($newStatus) {
                                                            'Accepted' => 'text-success',
                                                            'Rejected' => 'text-danger',
                                                            default => 'text-secondary',
                                                        };
                                                    @endphp
                                                    <li>
                                                        <form
                                                            action="{{ route('notifications.update-status', $request->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Change status to {{ $newStatus }}?');">
                                                            @csrf
                                                            <input type="hidden" name="status"
                                                                value="{{ strtolower($newStatus) }}">
                                                            <button type="submit"
                                                                class="dropdown-item d-flex align-items-center gap-2 {{ $statusColor }}">
                                                                <i class="bi {{ $statusIcon }}"></i>
                                                                {{ $newStatus }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            @endif

                                            @if (in_array(strtolower($request->status), ['requested', 'pending']))
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('leave-requests.destroy', $request->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                            <i class="bi bi-trash"></i> Delete Request
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
                                <td colspan="11" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-calendar-x fs-1 text-muted mb-2"></i>
                                        <h5 class="text-muted">No leave requests found</h5>
                                        @if (auth()->user()->can('create', App\Models\LeaveRequest::class))
                                            <a href="{{ route('leave-requests.create') }}" class="btn btn-primary mt-3">
                                                <i class="bi bi-plus-circle me-2"></i> Create New Request
                                            </a>
                                        @endif
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
        </div>

        {{-- @if ($leaveRequests->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                <div class="text-muted">
                    Showing {{ $leaveRequests->firstItem() }} to {{ $leaveRequests->lastItem() }} of
                    {{ $leaveRequests->total() }} entries
                </div>
                <div>
                    {{ $leaveRequests->onEachSide(1)->links() }}
                </div>
            </div>
        @endif --}}
    </div>
@endsection

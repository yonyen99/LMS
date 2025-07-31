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
                                <option value="{{ $type->name }}" {{ request('type') == $type->name ? 'selected' : '' }}>
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
        <div class="card card-1 p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Name</th>
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
                                <td colspan="11" class="text-center text-muted">No leave requests found.</td>
                            </tr>
                        @else
                            @foreach ($leaveRequests as $request)
                                @php
                                    $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') : '-';
                                    $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') : '-';
                                    $requestedAt = $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y') : '-';
                                    $lastChangedAt = $request->last_changed_at ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y') : '-';
                                    $displayStatus = ucfirst(strtolower($request->status));
                                    $colors = $statusColors[$displayStatus] ?? ['text' => '#000000', 'bg' => '#e0e0e0'];
                                @endphp
                                <tr class="text-center">
                                    <td>{{ ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + $loop->iteration }}</td>
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ optional($request->start_date)->format('d/m/Y') }} ({{ ucfirst($request->start_time) }})</td>
                                    <td>{{ optional($request->end_date)->format('d/m/Y') }} ({{ ucfirst($request->end_time) }})</td>
                                    <td>{{ $request->reason ?? '-' }}</td>
                                    <td>{{ number_format($request->duration, 2) }}</td>
                                    <td>{{ optional($request->leaveType)->name ?? '-' }}</td>
                                    <td>
                                        <span style="
                                            color: {{ $colors['text'] }};
                                            background-color: {{ $colors['bg'] }};
                                            padding: 2px 8px;
                                            border-radius: 10px;
                                            font-weight: 500;
                                            display: inline-block;">
                                            {{ $displayStatus }}
                                        </span>
                                    </td>
                                    <td>{{ $requestedAt }}</td>
                                    <td>{{ $lastChangedAt }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                id="actionsDropdown{{ $request->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false" aria-haspopup="true"
                                                aria-label="Actions for request #{{ $request->id }}" style="min-width: 50px;">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="actionsDropdown{{ $request->id }}">
                                                @if(auth()->check())
                                                    <li class="dropdown-header">Change Status</li>
                                                    @foreach(['Accepted', 'Rejected', 'Cancellation', 'Canceled'] as $newStatus)
                                                        <li>
                                                            <form action="{{ route('notifications.update-status', $request->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Change status to {{ $newStatus }}?');">
                                                                @csrf
                                                                <input type="hidden" name="status" value="{{ $newStatus }}">
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center">
                                                                    <i class="bi bi-arrow-repeat me-2"></i> {{ $newStatus }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if($leaveRequests->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                <div class="text-muted">
                    Showing {{ $leaveRequests->firstItem() }} to {{ $leaveRequests->lastItem() }} of {{ $leaveRequests->total() }} entries
                </div>
                <div>
                    {{ $leaveRequests->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

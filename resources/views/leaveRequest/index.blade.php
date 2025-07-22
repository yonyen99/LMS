@extends('layouts.app')

@section('content')
<div class="m-2">
    <div class="card card-1 p-3 mb-4">
        <form method="GET" action="{{ route('leave-requests.index') }}">
            <div>
                <div class="d-flex align-items-center justify-content-start flex-wrap gap-4">
                    <h2 class="fw-bold mb-0 me-2">My leave requests</h2>

                    @php
                        $statuses = ['Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'];
                        $colors = [
                            'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
                            'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
                            'Requested' => ['text' => '#ffffff', 'bg' => '#FC9A1D'],
                            'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
                            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
                            'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
                        ];
                    @endphp

                    @foreach($statuses as $status)
                        @php
                            $textColor = $colors[$status]['text'];
                            $bgColor = $colors[$status]['bg'];
                        @endphp

                        <div>
                            <label for="status_{{ $status }}" 
                                class="d-flex align-items-center fw-semibold"
                                style="color: {{ $textColor }}; background-color: {{ $bgColor }};
                                       padding: 0.25em 0.7em; border-radius: 0.3rem; cursor: pointer;">
                                <input type="checkbox" name="statuses[]" value="{{ $status }}" id="status_{{ $status }}" 
                                    {{ !request()->has('statuses') || in_array($status, request()->input('statuses', [])) ? 'checked' : '' }}
                                    onchange="this.form.submit()" 
                                    class="form-check-input me-2 mb-1"
                                    style="width: 1.1em; height: 1.1em;">
                                {{ $status }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Search & Filters --}}
            <div class="d-flex flex-wrap gap-3 align-items-end mt-3 mb-2">
                <div class="d-flex align-items-center border rounded px-2" style="width:20%;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="Search request...">
                    <i class="bi bi-search text-primary"></i>
                </div>

                <div class="d-flex align-items-center gap-2 mt-2" style="width:27%;">
                    <label for="showRequest" class="fw-semibold small mb-0" style="width:50%;">Show Request</label>
                    <select class="form-select" id="showRequest" name="sort_order" onchange="this.form.submit()">
                        <option value="new" {{ request('sort_order') == 'new' ? 'selected' : '' }}>Newest</option>
                        <option value="last" {{ request('sort_order') == 'last' ? 'selected' : '' }}>Oldest</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2" style="width:23%;">
                    <label for="type" class="fw-semibold small mb-0" style="width:20%;">Type</label>
                    <select class="form-select flex-grow-1" id="type" name="type" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2" style="width:26%;">
                    <label for="statusRequest" class="fw-semibold small mb-0" style="width:50%;">Status Request</label>
                    <select class="form-select" id="statusRequest" name="status_request" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($statusRequestOptions as $status)
                            <option value="{{ $status }}" {{ request('status_request') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Leave Requests Table --}}
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
                        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') : '-';
                        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') : '-';
                        $requestedAt = $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y') : '-';
                        $lastChangedAt = $request->last_changed_at ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y') : '-';
                        $displayStatus = ucfirst(strtolower($request->status));
                        $colors = $statusColors[$displayStatus] ?? ['text' => '#000000', 'bg' => '#e0e0e0'];
                    @endphp
                    <tr class="text-center">
                        <td>{{ ($leaveRequests->currentPage() - 1) * $leaveRequests->perPage() + $loop->iteration }}</td>
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
                                display: inline-block;
                            ">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td>{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $request->last_changed_at ? \Carbon\Carbon::parse($request->last_changed_at)->format('d/m/Y') : '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button 
                                    class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                    type="button" 
                                    id="actionsDropdown{{ $request->id }}" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                    aria-label="Actions for request #{{ $request->id }}"
                                    style="min-width: 50px;"
                                    >
                                <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $request->id }}">
                                
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('leave-requests.show', $request->id) }}">
                                    <i class="bi bi-eye me-2 text-primary"></i> View
                                    </a>
                                </li>

                                <li>
                                    <form action="{{ route('leave-requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                        <i class="bi bi-trash me-2"></i> Delete
                                    </button>
                                    </form>
                                </li>

                                <li>
                                    <form action="{{ route('leave-requests.cancel', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center text-secondary">
                                            <i class="bi bi-x-circle me-2"></i> Cancel
                                        </button>
                                    </form>
                                </li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    @if($leaveRequests->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
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

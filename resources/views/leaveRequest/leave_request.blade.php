@extends('layouts.app')

@section('content')
<div class="m-2">
    <div class="card p-3 mb-4">
        {{-- Status Filters --}}
        <div>
            <div class="d-flex align-items-center justify-end flex-wrap gap-4">
                <h2 class="fw-bold mb-0 me-4">My leave requests</h2>

                @foreach(['Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'] as $status)
                    @php
                        // Define colors
                        $colors = [
                            'Planned' => ['text' => '#ffffff', 'bg' => '#A59F9F'],
                            'Accepted' => ['text' => '#ffffff', 'bg' => '#447F44'],
                            'Requested' => ['text' => '#ffffff', 'bg' => '#F5811E'],
                            'Rejected' => ['text' => '#ffffff', 'bg' => '#F80300'],
                            'Cancellation' => ['text' => '#ffffff', 'bg' => '#F80300'],
                            'Canceled' => ['text' => '#ffffff', 'bg' => '#F80300'],
                        ];
                        $textColor = $colors[$status]['text'];
                        $bgColor = $colors[$status]['bg'];
                    @endphp

                    <div>
                        <label for="status_{{ $status }}" 
                            class="d-flex align-items-center fw-semibold"
                            style="
                                    color: {{ $textColor }};
                                    background-color: {{ $bgColor }};
                                    padding: 0.25em 0.7em;
                                    border-radius: 0.3rem;
                                    cursor: pointer;
                                    user-select: none;
                                ">
                            <input class="form-check-input me-2 mb-1" type="checkbox" id="status_{{ $status }}" checked
                                style="
                                    width: 1.1em;
                                    height: 1.1em;
                                    accent-color: {{ $textColor }};
                                ">
                            {{ $status }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- Search & Filters --}}
        <div class="d-flex flex-wrap gap-3 align-items-end mt-3 mb-2">
            <!-- Search with icon -->
            <div class="d-flex align-items-center border rounded px-2" style="width:20%;">
                <input type="text" class="form-control border-0" placeholder="Search request...">
                <i class="bi bi-search text-primary"></i>
            </div>

             <div class="d-flex align-items-center gap-2 mt-2" style="width:27%;">
                <label for="showRequest" class="fw-semibold small mb-0" style="width:50%;">Show Request</label>
                <select class="form-select" id="showRequest">
                <option>All</option>
                <option>My Requests</option>
                </select>
            </div>

            <!-- Type -->
            <div class="d-flex align-items-center gap-2" style="width:23%;">
                <label for="type" class="fw-semibold small mb-0" style="width:20%;">Type</label>
                <select class="form-select flex-grow-1" id="type">
                <option>Leave</option>
                <option>Work From Home</option>
                </select>
            </div>

            <!-- Status Request -->
            <div class="d-flex align-items-center gap-2" style="width:26%;">
                <label for="status" class="fw-semibold small mb-0" style="width:50%;">Status Request</label>
                <select class="form-select" id="status">
                <option>Pending</option>
                <option>Approved</option>
                <option>Rejected</option>
                </select>
            </div>
        </div>
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
        {{-- <tbody>
            @foreach($leaveRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} (Morning)</td>
                <td>{{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }} (Morning)</td>
                <td>{{ $request->reason ?? '-' }}</td>
                <td>{{ number_format($request->duration, 3) }}</td>
                <td>{{ $request->type }}</td>
                <td>
                    <span class="badge 
                        @if($request->status == 'Accepted') bg-success
                        @elseif($request->status == 'Requested') bg-warning text-dark
                        @elseif($request->status == 'Canceled' || $request->status == 'Rejected') bg-danger
                        @elseif($request->status == 'Cancellation') bg-danger-subtle text-danger
                        @else bg-secondary
                        @endif
                    ">
                        {{ $request->status }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($request->requested_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($request->last_change)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody> --}}
    </table>
</div>
@endsection
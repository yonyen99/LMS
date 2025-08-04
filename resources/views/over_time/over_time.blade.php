@extends('layouts.app')

@section('title', 'Overtime Work List')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Overtime Work List</h2>

    @if($overtimes->count())
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Total Hours</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overtimes as $index => $ot)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ot->user->name }}</td>
                            <td>{{ $ot->department->name }}</td>
                            <td>{{ $ot->overtime_date }}</td>
                            <td class="text-center">
                                {{ 
                                    \Carbon\Carbon::parse($ot->start_time)
                                        ->diffInHours(\Carbon\Carbon::parse($ot->end_time)) 
                                }} 
                            </td>
                            <td>{{ $ot->reason }}</td>
                            <td>
                                @if($ot->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($ot->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($ot->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ot->actionBy)
                                    {{ $ot->actionBy->name }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">No overtime records found.</div>
    @endif
</div>
@endsection
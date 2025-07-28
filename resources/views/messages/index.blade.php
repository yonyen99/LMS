
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-1 p-4" style="width: 55%">
        <h3 class="mb-4">Notifications</h3>
        
        @forelse ($leaveRequests as $request)
          <a href="{{ route('notifications.index', $request->id) }}" class="text-decoration-none text-dark">
            <div class="notification-item position-relative" 
                 data-id="{{ $request->id }}" 
                 style="cursor: pointer;">
                
                <div class="alert shadow-sm rounded-3 {{ $request->is_read ? 'alert-light' : 'alert-primary' }}" 
                     style="margin-left: 20px; transition: all 0.3s ease;">
                    
                    <div>
                        <strong>{{ $request->user->name }}</strong> requested 
                        <strong class="text-info">{{ $request->leaveType->name ?? 'Leave' }}</strong> 
                        from <strong>{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}</strong>
                        ({{ $request->start_time }})
                        to <strong>{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</strong>
                        ({{ $request->end_time }}).
                    </div>
                    
                    @if ($request->reason)
                        <div class="mt-2">Reason: <em>{{ $request->reason }}</em></div>
                    @endif
                    
                    <div class="mt-2">
                        Status: 
                        <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted d-block">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Submitted: {{ \Carbon\Carbon::parse($request->requested_at ?? $request->created_at)->format('d M Y, g:i A') }}
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Last updated: {{ \Carbon\Carbon::parse($request->last_changed_at ?? $request->updated_at)->format('d M Y, g:i A') }}
                        </small>
                        
                        @if ($request->read_at)
                            <small class="text-muted d-block">
                                <i class="fas fa-eye me-1"></i>
                                Read: {{ \Carbon\Carbon::parse($request->read_at)->format('d M Y, g:i A') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
          </a>
        @empty
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No leave requests found.
            </div>
        @endforelse
    </div>
</div>

@endsection
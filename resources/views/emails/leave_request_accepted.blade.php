@component('mail::message')
@if($notificationType === 'department')
# Leave Approval Notice
**{{ $leaveRequest->user->name }}**'s leave has been approved.
@else
# Leave Request Approved
Your leave request has been approved!
@endif

**Details:**
- **Type:** {{ $leaveRequest->leaveType->name }}
- **Dates:** {{ $leaveRequest->start_date->format('M d, Y') }} to {{ $leaveRequest->end_date->format('M d, Y') }}
- **Duration:** {{ $leaveRequest->duration }} day(s)
- **Status:** Approved

@component('mail::button', ['url' => route('leave-requests.index')])
View Leave Calendar
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
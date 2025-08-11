@component('mail::message')
# Leave Request {{ $leaveRequest->status }}

**Employee:** {{ $leaveRequest->user->name }}  
**Department:** {{ $leaveRequest->user->department->name }}  
**Leave Type:** {{ $leaveRequest->leaveType->name }}  
**Dates:** {{ $leaveRequest->start_date }} to {{ $leaveRequest->end_date }}  
**Duration:** {{ $leaveRequest->duration }} day(s)  
**Status Changed By:** {{ $actionUser->name }}  
**Changed At:** {{ $leaveRequest->last_changed_at->format('Y-m-d H:i') }}  

@component('mail::button', ['url' => route('leave-requests.show', $leaveRequest->id)])
View Leave Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
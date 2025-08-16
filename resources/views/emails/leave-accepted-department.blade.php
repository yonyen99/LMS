@component('mail::message')
# Leave Request Approved in {{ $departmentName }}

**{{ $employeeName }}** ({{ $employeeEmail }}) has had their leave request approved by **{{ $approverName }}**.

## Leave Details
@component('mail::panel')
- **Type:** {{ $leaveRequest->leaveType->name ?? 'N/A' }}
- **Start Date:** {{ $leaveRequest->start_date->format('M d, Y') }}
- **End Date:** {{ $leaveRequest->end_date->format('M d, Y') }}
- **Duration:** {{ $leaveRequest->duration }} day(s)
- **Reason:** {{ $leaveRequest->reason }}
- **Status:** Approved
@endcomponent

@component('mail::button', ['url' => route('leave-requests.show', $leaveRequest->id), 'color' => 'primary'])
View Leave Details
@endcomponent

@component('mail::subcopy')
You're receiving this notification because you're a member of the {{ $departmentName }} department.
If this doesn't concern you, please ignore this email.
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
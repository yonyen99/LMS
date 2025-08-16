@component('mail::message')
# Leave Request Approved

Dear {{ $leaveRequest->user->name }},

We are pleased to inform you that your leave request has been approved.

**Leave Details:**
- Type: {{ $leaveRequest->leaveType->name ?? 'N/A' }}
- Dates: {{ $leaveRequest->start_date->format('M d, Y') }} to {{ $leaveRequest->end_date->format('M d, Y') }}
- Duration: {{ $leaveRequest->duration }} days
- Approved By: {{ $approverName }}
- Reason: {{ $leaveRequest->reason }}

@component('mail::button', ['url' => route('leave-requests.show', $leaveRequest->id), 'color' => 'success'])
View Leave Details
@endcomponent

If you have any questions, please contact your manager.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
@extends('layouts.app')

   @section('content')
   <div class="m-2">
       <div class="card p-3 mb-4">
           <h2 class="fw-bold mb-4">Leave Calendar</h2>

           <!-- Calendar Container -->
           <div id="calendar"></div>

           <!-- Modal for Viewing Leave Request -->
           <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
               <div class="modal-dialog">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title" id="viewRequestModalLabel">Leave Request Details</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                           <p><strong>ID:</strong> <span id="requestId"></span></p>
                           <p><strong>Type:</strong> <span id="requestType"></span></p>
                           <p><strong>Start Date:</strong> <span id="requestStart"></span></p>
                           <p><strong>End Date:</strong> <span id="requestEnd"></span></p>
                           <p><strong>Duration:</strong> <span id="requestDuration"></span></p>
                           <p><strong>Reason:</strong> <span id="requestReason"></span></p>
                           <p><strong>Status:</strong> <span id="requestStatus"></span></p>
                           <p><strong>Requested:</strong> <span id="requestRequested"></span></p>
                           <p><strong>Last Changed:</strong> <span id="requestLastChanged"></span></p>
                       </div>
                       <div class="modal-footer">
                           @can('cancel-request')
                               <form id="cancelRequestForm" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                   @csrf
                                   <button type="submit" class="btn btn-danger btn-sm">
                                       <i class="bi bi-x-circle"></i> Cancel Request
                                   </button>
                               </form>
                           @endcan
                           <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                               <i class="bi bi-x"></i> Close
                           </button>
                       </div>
                   </div>
               </div>
           </div>

           <!-- Modal for Creating New Leave Request -->
           <div class="modal fade" id="createRequestModal" tabindex="-1" aria-labelledby="createRequestModalLabel" aria-hidden="true">
               <div class="modal-dialog">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title" id="createRequestModalLabel">New Leave Request</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                           <form id="createRequestForm" action="{{ route('leave-requests.store') }}" method="POST">
                               @csrf
                               <div class="mb-3">
                                   <label for="leave_type_id" class="form-label">Leave Type</label>
                                   <select name="leave_type_id" id="leave_type_id" class="form-select" required>
                                       <option value="">Select leave type</option>
                                       @foreach($leaveTypes as $leaveType)
                                           <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                                       @endforeach
                                   </select>
                               </div>
                               <div class="mb-3">
                                   <label for="start_date" class="form-label">Start Date</label>
                                   <input type="date" name="start_date" id="start_date" class="form-control" required>
                               </div>
                               <div class="mb-3">
                                   <label for="end_date" class="form-label">End Date</label>
                                   <input type="date" name="end_date" id="end_date" class="form-control" required>
                               </div>
                               <div class="mb-3">
                                   <label for="duration" class="form-label">Duration</label>
                                   <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g. 1.5 days" required>
                               </div>
                               <div class="mb-3">
                                   <label for="start_time" class="form-label">Start Time</label>
                                   <select name="start_time" id="start_time" class="form-select" required>
                                       <option value="">Select start time</option>
                                       <option value="morning">Morning</option>
                                       <option value="afternoon">Afternoon</option>
                                   </select>
                               </div>
                               <div class="mb-3">
                                   <label for="end_time" class="form-label">End Time</label>
                                   <select name="end_time" id="end_time" class="form-select" required>
                                       <option value="">Select end time</option>
                                       <option value="morning">Morning</option>
                                       <option value="afternoon">Afternoon</option>
                                   </select>
                               </div>
                               <div class="mb-3">
                                   <label for="reason" class="form-label">Reason</label>
                                   <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Enter reason"></textarea>
                               </div>
                               <div class="d-flex gap-2">
                                   <button type="submit" name="status" value="planned" class="btn btn-primary btn-sm">
                                       <i class="bi bi-calendar2"></i> Planned
                                   </button>
                                   <button type="submit" name="status" value="requested" class="btn btn-info btn-sm text-white">
                                       <i class="bi bi-check2-circle"></i> Requested
                                   </button>
                               </div>
                           </form>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <!-- FullCalendar CSS -->
   <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
   <!-- FullCalendar JS -->
   <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

   <script>
   document.addEventListener('DOMContentLoaded', function() {
       var calendarEl = document.getElementById('calendar');
       var calendar = new FullCalendar.Calendar(calendarEl, {
           initialView: 'dayGridMonth',
           headerToolbar: {
               left: 'prev,next today',
               center: 'title',
               right: 'dayGridMonth,timeGridWeek,timeGridDay'
           },
           events: [
               @foreach($leaveRequests as $request)
               {
                   title: "{{ optional($request->leaveType)->name ?? 'Leave' }} ({{ ucfirst($request->status) }})",
                   start: "{{ $request->start_date }}",
                   end: "{{ \Carbon\Carbon::parse($request->end_date)->addDay() }}", // Add 1 day to include end date
                   id: "{{ $request->id }}",
                   color: @php
                       $statusColors = [
                           'Planned' => '#A59F9F',
                           'Accepted' => '#447F44',
                           'Requested' => '#FC9A1D',
                           'Rejected' => '#F80300',
                           'Cancellation' => '#F80300',
                           'Canceled' => '#F80300',
                       ];
                       $status = ucfirst(strtolower($request->status)); // Normalize status case
                       echo "'" . ($statusColors[$status] ?? '#e0e0e0') . "'"; // Fallback color
                   @endphp
               },
               @endforeach
           ],
           dateClick: function(info) {
               @can('create-request')
                   // Open create request modal with pre-filled date
                   document.getElementById('start_date').value = info.dateStr;
                   document.getElementById('end_date').value = info.dateStr;
                   new bootstrap.Modal(document.getElementById('createRequestModal')).show();
               @endcan
           },
           eventClick: function(info) {
               // Fetch request details via AJAX or pre-loaded data
               var request = @json($leaveRequests).find(r => r.id == info.event.id);
               if (request) {
                   document.getElementById('requestId').textContent = request.id;
                   document.getElementById('requestType').textContent = request.leave_type?.name ?? '-';
                   document.getElementById('requestStart').textContent = request.start_date + ' (' + request.start_time + ')';
                   document.getElementById('requestEnd').textContent = request.end_date + ' (' + request.end_time + ')';
                   document.getElementById('requestDuration').textContent = request.duration;
                   document.getElementById('requestReason').textContent = request.reason ?? '-';
                   document.getElementById('requestStatus').textContent = request.status;
                   document.getElementById('requestRequested').textContent = request.requested_at ?? '-';
                   document.getElementById('requestLastChanged').textContent = request.last_changed_at ?? '-';
                   @can('cancel-request')
                       document.getElementById('cancelRequestForm').action = "{{ route('leave-requests.cancel', ':id') }}".replace(':id', request.id);
                   @endcan
                   new bootstrap.Modal(document.getElementById('viewRequestModal')).show();
               }
           },
           editable: false,
           selectable: true,
           selectHelper: true,
           eventTimeFormat: {
               hour: 'numeric',
               minute: '2-digit',
               meridiem: 'short'
           }
       });

       calendar.render();
   });
   </script>
   @endsection
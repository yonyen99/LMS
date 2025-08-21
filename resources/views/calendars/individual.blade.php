@extends('layouts.app')

@section('content')
<div class="container-fluid px-0 px-md-2">
    <div class="card card-2 bg-white"  style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
        <div class="card-body p-3 p-md-4">
            <h2 class="fw-bold mb-0 text-primary fs-4">My Calendar</h2>

            <div class="row align-items-center mb-4 mt-3 gy-2">
                <!-- Status Badges -->
                <div class="col-12 col-md-8 mb-2">
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $statuses = ['Planned', 'Accepted', 'Requested', 'Rejected', 'Cancellation', 'Canceled'];
                            $statusColors = [
                                'Planned' => ['text' => '#fff', 'bg' => '#A59F9F'],
                                'Accepted' => ['text' => '#fff', 'bg' => '#447F44'],
                                'Requested' => ['text' => '#fff', 'bg' => '#FC9A1D'],
                                'Rejected' => ['text' => '#fff', 'bg' => '#F80300'],
                                'Cancellation' => ['text' => '#fff', 'bg' => '#F80300'],
                                'Canceled' => ['text' => '#fff', 'bg' => '#F80300'],
                            ];
                        @endphp

                        @foreach ($statuses as $status)
                            @php
                                $textColor = $statusColors[$status]['text'];
                                $bgColor = $statusColors[$status]['bg'];
                            @endphp
                            <span class="fw-semibold px-2 py-1 rounded"
                                style="color: {{ $textColor }}; background-color: {{ $bgColor }};">
                                {{ $status }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 col-md-4 text-md-end text-start">
                    @can('create-request')
                        <button class="btn btn-primary btn-sm me-2 mb-1" onclick="showCreateModal()">
                            <i class="bi bi-plus-circle me-1"></i> New Request
                        </button>
                    @endcan

                    @canany(['create-non-working-day', 'Admin'])
                        <button class="btn btn-secondary btn-sm mb-1" onclick="showNonWorkingDayModal()">
                            <i class="bi bi-calendar-x me-1"></i> Add Non-Working Day
                        </button>
                    @endcanany
                </div>
            </div>

            <!-- Calendar Container -->
            <div id="calendar" class="fc-responsive"></div>

            <!-- View Request Modal -->
            <div class="modal" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white py-2 py-md-3">
                            <h5 class="modal-title fw-bold fs-6 fs-md-5">Leave Details</h5>
                            <button type="button" class="btn-close btn-close-white m-0" onclick="hideModal('viewRequestModal')" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-2 p-md-3">
                            <div class="row g-2 g-md-3">
                                <div class="col-6 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">Type</label>
                                        <p class="mb-0 fw-semibold fs-6 fs-md-5" id="requestType"></p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">Status</label>
                                        <p class="mb-0"><span id="requestStatus" class="badge rounded-pill fs-6 fs-md-5"></span></p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">Start</label>
                                        <p class="mb-0 fw-semibold fs-6 fs-md-5" id="requestStart"></p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">End</label>
                                        <p class="mb-0 fw-semibold fs-6 fs-md-5" id="requestEnd"></p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">Reason</label>
                                        <p class="mb-0 fw-semibold fs-6 fs-md-5" id="requestReason"></p>
                                    </div>
                                </div>
                                <div class="col-12" id="nwdDepartmentContainer" style="display: none;">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">Department</label>
                                        <p class="mb-0 fw-semibold fs-6 fs-md-5" id="nwdDepartment"></p>
                                    </div>
                                </div>
                                <div class="col-12" id="nwdCreatorContainer" style="display: none;">
                                    <div class="mb-2 mb-md-3">
                                        <label class="form-label text-muted small mb-0">Added By</label>
                                        <p class="mb-0 fw-semibold fs-6 fs-md-5" id="nwdCreator"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2 py-md-3">
                            @can('cancel-request')
                                <form id="cancelRequestForm" method="POST" onsubmit="return confirm('Cancel this request?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Cancel
                                    </button>
                                </form>
                            @endcan
                            @can('edit-non-working-day')
                                <form id="editNonWorkingDayForm" method="GET" style="display: none;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                </form>
                            @endcan
                            @can('delete-non-working-day')
                                <form id="deleteNonWorkingDayForm" method="POST" onsubmit="return confirm('Delete this non-working day?');" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            @endcan
                            <button type="button" class="btn btn-secondary btn-sm" onclick="hideModal('viewRequestModal')">
                                <i class="bi bi-x me-1"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Request Modal -->
            <div class="modal" id="createRequestModal" tabindex="-1" aria-labelledby="createRequestModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-global text-white py-2 py-md-3">
                            <h5 class="modal-title fw-bold fs-6 fs-md-5">New Request</h5>
                            <button type="button" class="btn-close btn-close-white m-0" onclick="hideModal('createRequestModal')" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-2 p-md-3">
                            <form id="createRequestForm" action="{{ route('leave-requests.store') }}" method="POST">
                                @csrf
                                <div class="row g-2 g-md-3">
                                    <div class="col-12 col-md-6">
                                        <label for="leave_type_id" class="form-label small mb-0">Leave Type <span class="text-danger">*</span></label>
                                        <select name="leave_type_id" id="leave_type_id" class="form-select form-select-sm" required>
                                            <option value="">Select type</option>
                                            @foreach($leaveTypes as $leaveType)
                                                <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="duration" class="form-label small mb-0">Duration <span class="text-danger">*</span></label>
                                        <input type="text" name="duration" id="duration" class="form-control form-control-sm" placeholder="1.5 days" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="start_date" class="form-label small mb-0">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="start_time" class="form-label small mb-0">Start Time <span class="text-danger">*</span></label>
                                        <select name="start_time" id="start_time" class="form-select form-select-sm" required>
                                            <option value="">Select time</option>
                                            <option value="morning">Morning</option>
                                            <option value="afternoon">Afternoon</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="end_date" class="form-label small mb-0">End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="end_time" class="form-label small mb-0">End Time <span class="text-danger">*</span></label>
                                        <select name="end_time" id="end_time" class="form-select form-select-sm" required>
                                            <option value="">Select time</option>
                                            <option value="morning">Morning</option>
                                            <option value="afternoon">Afternoon</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="reason" class="form-label small mb-0">Reason <span class="text-danger">*</span></label>
                                        <textarea name="reason" id="reason" class="form-control form-control-sm" rows="2" placeholder="Brief reason" required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideModal('createRequestModal')">
                                        <i class="bi bi-x me-1"></i> Cancel
                                    </button>
                                    <button type="submit" name="status" value="planned" class="btn btn-primary btn-sm">
                                        <i class="bi bi-calendar2 me-1"></i> Plan
                                    </button>
                                    <button type="submit" name="status" value="requested" class="btn btn-success btn-sm">
                                        <i class="bi bi-check2 me-1"></i> Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Non-Working Day Modal -->
            <div class="modal" id="nonWorkingDayModal" tabindex="-1" aria-labelledby="nonWorkingDayModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white py-2 py-md-3">
                            <h5 class="modal-title fw-bold fs-6 fs-md-5" id="nonWorkingDayModalTitle">Add Non-Working Day</h5>
                            <button type="button" class="btn-close btn-close-white m-0" onclick="hideModal('nonWorkingDayModal')" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-2 p-md-3">
                            <form id="nonWorkingDayForm" action="{{ route('non-working-days.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="nwd_method" value="POST">
                                <input type="hidden" name="id" id="nwd_id">
                                <div class="row g-2 g-md-3">
                                    <div class="col-12">
                                        <label for="nwd_title" class="form-label small mb-0">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="nwd_title" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="nwd_type" class="form-label small mb-0">Type <span class="text-danger">*</span></label>
                                        <select name="type" id="nwd_type" class="form-select form-select-sm" required>
                                            <option value="">Select type</option>
                                            <option value="holiday">Holiday</option>
                                            <option value="meeting">Meeting</option>
                                            <option value="event">Event</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="training">Training</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="nwd_start_date" class="form-label small mb-0">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" id="nwd_start_date" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="nwd_start_time" class="form-label small mb-0">Start Time (optional)</label>
                                        <select name="start_time" id="nwd_start_time" class="form-select form-select-sm">
                                            <option value="">Select time</option>
                                            <option value="morning">Morning</option>
                                            <option value="afternoon">Afternoon</option>
                                            <option value="full">Full Day</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="nwd_end_date" class="form-label small mb-0">End Date (optional)</label>
                                        <input type="date" name="end_date" id="nwd_end_date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="nwd_end_time" class="form-label small mb-0">End Time (optional)</label>
                                        <select name="end_time" id="nwd_end_time" class="form-select form-select-sm">
                                            <option value="">Select time</option>
                                            <option value="morning">Morning</option>
                                            <option value="afternoon">Afternoon</option>
                                            <option value="full">Full Day</option>
                                        </select>
                                    </div>
                                    @if(auth()->user()->hasRole('Admin'))
                                    <div class="col-12">
                                        <label for="nwd_department_id" class="form-label small mb-0">Department (leave blank for global)</label>
                                        <select name="department_id" id="nwd_department_id" class="form-select form-select-sm">
                                            <option value="">Global (All Departments)</option>
                                            @foreach(\App\Models\Department::all() as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @elseif(auth()->user()->hasRole('Manager'))
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                                    @endif
                                    <div class="col-12">
                                        <label for="nwd_description" class="form-label small mb-0">Description <span class="text-danger">*</span></label>
                                        <textarea name="description" id="nwd_description" class="form-control form-control-sm" rows="2" required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideModal('nonWorkingDayModal')">
                                        <i class="bi bi-x me-1"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-save me-1"></i> Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar Resources -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<style>
    /* Base Calendar Styles */
    #calendar {
        min-height: 500px;
        background-color: #fff;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    /* Calendar Header */
    .fc-header-toolbar {
        padding: 0.5rem;
        margin-bottom: 0.25rem !important;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }

    .fc-toolbar-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Calendar Buttons */
    .fc-button {
        background-color: #fff !important;
        color: #2c3e50 !important;
        border: 1px solid #dee2e6 !important;
        font-weight: 500 !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.8rem !important;
    }

    .fc-button-active {
        background-color: #e9ecef !important;
    }

    /* Calendar Events */
    .fc-event {
        border-radius: 0.2rem !important;
        padding: 0.1rem 0.25rem !important;
        font-size: 0.7rem !important;
        border: none !important;
        margin: 0.1rem 0 !important;
    }

    .fc-event-title {
        font-weight: 500 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Status Badges */
    .badge {
        padding: 0.3em 0.6em;
        font-size: 0.75em;
        font-weight: 600;
    }

    .badge-planned { background-color: #6c757d; }
    .badge-accepted { background-color: #28a745; }
    .badge-requested { background-color: #fd7e14; }
    .badge-rejected,
    .badge-cancellation,
    .badge-canceled { background-color: #dc3545; }
    .badge-non-working { background-color: #6c757d; }

    /* Day Headers (Mon, Tue, ...) */
    .fc-col-header-cell,
    .fc-col-header-cell-cushion {
        background-color: #f8f9fa;
        font-size: 1rem;
        color: #000 !important;
        border-bottom: none;
        text-decoration: none !important;
    }


    .fc-col-header-cell .fc-scrollgrid-sync-inner {
        padding: 0.25rem;
        color: #000 !important;
    }

    /* Day Cells */
    .fc-daygrid-day {
        padding: 0.1rem !important;
    }

    /* Make date numbers black */
    .fc-daygrid-day-number {
        color: #000 !important;
        border-bottom: none;
        font-weight: 500;
        text-decoration: none !important;
    }

    /* Today Highlight */
    .fc-day-today {
        background-color: #e6f7ff !important;
    }

    /* Non-working day styles */
    .fc-non-working-day {
        background-color: rgba(220, 53, 69, 0.1) !important;
        border-left: 3px solid #dc3545 !important;
        border-right: 3px solid #dc3545 !important;
    }

    .fc-non-working-day-title {
        font-weight: bold;
        color: #dc3545;
    }

    /* Enhanced Modal Styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(2px);
    }

    .modal.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-dialog {
        max-width: 500px;
        width: 90%;
        margin: 0 auto;
        transform: translateY(-20px);
        transition: all: 0.3s ease;
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    .modal-content {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .modal-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-footer {
        padding: 0.75rem 1rem;
        border-top: 1px solid #dee2e6;
    }

    .btn-close {
        background: transparent;
        border: none;
        font-size: 1.25rem;
        opacity: 0.7;
        transition: opacity 0.2s;
        cursor: pointer;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .btn-close-white {
        filter: invert(1);
    }

    /* Form Elements */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.1rem;
    }

    /* Mobile Optimizations */
    @media (max-width: 576px) {
        #calendar {
            min-height: 350px;
        }

        .fc-header-toolbar {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem;
        }

        .modal-dialog {
            width: 95%;
        }

        .modal-body {
            padding: 0.75rem;
        }

        .form-control, .form-select {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }

    /* Tablet Optimizations */
    @media (min-width: 577px) and (max-width: 768px) {
        #calendar {
            min-height: 450px;
        }

        .fc-toolbar-title {
            font-size: 1.1rem;
        }

        .fc-button {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8rem !important;
        }

        .fc-event {
            font-size: 0.75rem !important;
        }
    }

    /* Animation for buttons */
    .btn {
        transition: all 0.2s ease;
        transform: translateY(0);
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Focus styles for better accessibility */
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Smooth scroll for modal content */
    .modal-body {
        max-height: 60vh;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
</style>


<script>
// Enhanced modal control with animations
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        document.body.style.overflow = 'hidden';
        modal.style.display = 'flex';
        
        // Trigger reflow to enable animation
        void modal.offsetWidth;
        
        modal.classList.add('show');
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        
        // Wait for animation to complete before hiding
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

function showCreateModal() {
    showModal('createRequestModal');
}

function showNonWorkingDayModal(nwd = null) {
    const form = document.getElementById('nonWorkingDayForm');
    const title = document.getElementById('nonWorkingDayModalTitle');
    if (nwd) {
        // Edit mode
        title.textContent = 'Edit Non-Working Day';
        form.action = "{{ url('non-working-days') }}/" + nwd.id;
        document.getElementById('nwd_method').value = 'PUT';
        document.getElementById('nwd_id').value = nwd.id;
        document.getElementById('nwd_title').value = nwd.title;
        document.getElementById('nwd_type').value = nwd.type || '';
        document.getElementById('nwd_start_date').value = nwd.start;
        document.getElementById('nwd_start_time').value = nwd.start_time || '';
        document.getElementById('nwd_end_date').value = nwd.end ? new Date(new Date(nwd.end).setDate(new Date(nwd.end).getDate() - 1)).toISOString().split('T')[0] : '';
        document.getElementById('nwd_end_time').value = nwd.end_time || '';
        document.getElementById('nwd_description').value = nwd.description || '';
        @if(auth()->user()->hasRole('Admin'))
            document.getElementById('nwd_department_id').value = nwd.department_id || '';
        @endif
    } else {
        // Create mode
        title.textContent = 'Add Non-Working Day';
        form.action = "{{ route('non-working-days.store') }}";
        document.getElementById('nwd_method').value = 'POST';
        document.getElementById('nwd_id').value = '';
        document.getElementById('nwd_title').value = '';
        document.getElementById('nwd_type').value = '';
        document.getElementById('nwd_start_date').value = '';
        document.getElementById('nwd_start_time').value = '';
        document.getElementById('nwd_end_date').value = '';
        document.getElementById('nwd_end_time').value = '';
        document.getElementById('nwd_description').value = '';
        @if(auth()->user()->hasRole('Admin'))
            document.getElementById('nwd_department_id').value = '';
        @endif
    }
    showModal('nonWorkingDayModal');
}

// Close modals when clicking outside or pressing Escape
document.addEventListener('DOMContentLoaded', function() {
    // Click outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            hideModal(event.target.id);
        }
    });
    
    // Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                hideModal(openModal.id);
            }
        }
    });
    
    // Initialize calendar only if element exists
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev today next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            // Leave requests
            @foreach($leaveRequests as $request)
            {
                title: "{{ optional($request->leaveType)->name ?? 'Leave' }} ({{ ucfirst($request->status) }})",
                start: "{{ $request->start_date }}",
                end: "{{ \Carbon\Carbon::parse($request->end_date)->addDay() }}",
                id: "{{ $request->id }}",
                extendedProps: {
                    type: "leave",
                    leaveType: "{{ optional($request->leaveType)->name ?? '-' }}",
                    startTime: "{{ $request->start_time }}",
                    endTime: "{{ $request->end_time }}",
                    duration: "{{ $request->duration }}",
                    reason: "{{ $request->reason ?? '-' }}",
                    status: "{{ $request->status }}",
                    requestedAt: "{{ $request->requested_at ? $request->requested_at->format('M j, Y g:i A') : '-' }}",
                    lastChangedAt: "{{ $request->last_changed_at ? $request->last_changed_at->format('M j, Y g:i A') : '-' }}"
                },
                color: @php
                    $statusColors = [
                        'Planned' => '#6c757d',
                        'Accepted' => '#28a745',
                        'Requested' => '#fd7e14',
                        'Rejected' => '#dc3545',
                        'Cancellation' => '#dc3545',
                        'Canceled' => '#dc3545',
                    ];
                    $status = ucfirst(strtolower($request->status));
                    echo "'" . ($statusColors[$status] ?? '#adb5bd') . "'";
                @endphp,
                textColor: '#fff'
            },
            @endforeach
            
            // Non-working days
            @foreach($nonWorkingDays as $nwd)
            {
                title: "{{ $nwd->title }}",
                start: "{{ $nwd->start_date }}",
                end: "{{ $nwd->end_date ? \Carbon\Carbon::parse($nwd->end_date)->addDay()->format('Y-m-d') : '' }}",
                id: "nwd_{{ $nwd->id }}",
                display: 'background',
                className: 'fc-non-working-day',
                extendedProps: {
                    type: "non_working_day",
                    description: "{{ $nwd->description ?? '' }}",
                    department: "{{ $nwd->department ? $nwd->department->name : 'Global' }}",
                    department_id: "{{ $nwd->department_id ?? '' }}",
                    createdBy: "{{ $nwd->creator->name }}",
                    typeNwd: "{{ $nwd->type }}",
                    start_time: "{{ $nwd->start_time ?? '' }}",
                    end_time: "{{ $nwd->end_time ?? '' }}"
                },
                allDay: true
            },
            @endforeach
        ],
        dateClick: function(info) {
            @can('create-request')
                // Set dates in create form
                document.getElementById('start_date').value = info.dateStr;
                document.getElementById('end_date').value = info.dateStr;
                
                // Show modal
                showModal('createRequestModal');
            @endcan
            @can('create-non-working-day')
                // Set dates in non-working day form
                document.getElementById('nwd_start_date').value = info.dateStr;
                document.getElementById('nwd_end_date').value = info.dateStr;
                document.getElementById('nwd_start_time').value = '';
                document.getElementById('nwd_end_time').value = '';
                showNonWorkingDayModal();
            @endcan
        },
        eventClick: function(info) {
            var event = info.event;
            var extendedProps = event.extendedProps;
            
            // Reset all optional fields
            document.getElementById('nwdDepartmentContainer').style.display = 'none';
            document.getElementById('nwdCreatorContainer').style.display = 'none';
            var editForm = document.getElementById('editNonWorkingDayForm');
            var deleteForm = document.getElementById('deleteNonWorkingDayForm');
            if (editForm) editForm.style.display = 'none';
            if (deleteForm) deleteForm.style.display = 'none';
            
            if (extendedProps.type === 'leave') {
                // Handle leave request click
                var status = extendedProps.status.toLowerCase();
                
                // Format dates for display
                var startDate = new Date(event.startStr);
                var endDate = event.end ? new Date(event.endStr) : new Date(event.startStr);
                endDate.setDate(endDate.getDate() - 1); // Adjust for inclusive end date
                
                // Update modal content
                document.getElementById('requestType').textContent = extendedProps.leaveType;
                document.getElementById('requestStart').textContent = startDate.toLocaleDateString() + ' (' + extendedProps.startTime + ')';
                document.getElementById('requestEnd').textContent = endDate.toLocaleDateString() + ' (' + extendedProps.endTime + ')';
                document.getElementById('requestReason').textContent = extendedProps.reason;
                
                // Status badge
                var statusElement = document.getElementById('requestStatus');
                if (statusElement) {
                    statusElement.textContent = extendedProps.status;
                    statusElement.className = 'badge rounded-pill badge-' + status;
                }
                
                // Update cancel form action if exists
                @can('cancel-request')
                    var cancelForm = document.getElementById('cancelRequestForm');
                    if (cancelForm) {
                        cancelForm.action = "{{ route('leave-requests.cancel', ':id') }}".replace(':id', event.id);
                        cancelForm.style.display = 'block';
                    }
                @endcan
            } else if (extendedProps.type === 'non_working_day') {
                // Handle non-working day click
                document.getElementById('requestType').textContent = 'Non-Working Day: ' + event.title;
                document.getElementById('requestStart').textContent = new Date(event.startStr).toLocaleDateString() + (extendedProps.start_time ? ' (' + extendedProps.start_time + ')' : '');
                document.getElementById('requestEnd').textContent = event.end ? 
                    new Date(new Date(event.endStr).setDate(new Date(event.endStr).getDate() - 1)).toLocaleDateString() + (extendedProps.end_time ? ' (' + extendedProps.end_time + ')' : '') : 
                    new Date(event.startStr).toLocaleDateString() + (extendedProps.start_time ? ' (' + extendedProps.start_time + ')' : '');
                document.getElementById('requestReason').textContent = extendedProps.description || '-';
                
                // Status badge
                var statusElement = document.getElementById('requestStatus');
                if (statusElement) {
                    statusElement.textContent = 'Non-Working';
                    statusElement.className = 'badge rounded-pill bg-secondary';
                }
                
                // Show department if exists
                if (extendedProps.department && extendedProps.department !== 'Global') {
                    document.getElementById('nwdDepartment').textContent = extendedProps.department;
                    document.getElementById('nwdDepartmentContainer').style.display = 'block';
                }
                
                // Show creator
                if (extendedProps.createdBy) {
                    document.getElementById('nwdCreator').textContent = extendedProps.createdBy;
                    document.getElementById('nwdCreatorContainer').style.display = 'block';
                }
                
                // Update edit and delete form actions if exists
                @can('edit-non-working-day')
                    if (editForm) {
                        editForm.action = "{{ url('non-working-days') }}/" + event.id.replace('nwd_', '');
                        editForm.style.display = 'block';
                    }
                @endcan
                @can('delete-non-working-day')
                    if (deleteForm) {
                        deleteForm.action = "{{ url('non-working-days') }}/" + event.id.replace('nwd_', '');
                        deleteForm.style.display = 'block';
                    }
                @endcan
                
                // Handle edit button click
                if (editForm) {
                    editForm.onsubmit = function(e) {
                        e.preventDefault();
                        showNonWorkingDayModal({
                            id: event.id.replace('nwd_', ''),
                            title: event.title,
                            type: extendedProps.typeNwd,
                            start: event.startStr,
                            start_time: extendedProps.start_time,
                            end: event.end ? new Date(new Date(event.endStr).setDate(new Date(event.endStr).getDate() - 1)).toISOString().split('T')[0] : '',
                            end_time: extendedProps.end_time,
                            description: extendedProps.description,
                            department_id: extendedProps.department_id
                        });
                    };
                }
                
                // Handle delete form submission
                if (deleteForm) {
                    deleteForm.onsubmit = function(e) {
                        if (!confirm('Delete this non-working day?')) {
                            e.preventDefault();
                            return false;
                        }
                    };
                }
                
                // Hide cancel button for non-working days
                var cancelForm = document.getElementById('cancelRequestForm');
                if (cancelForm) {
                    cancelForm.style.display = 'none';
                }
            }
            
            // Show modal
            showModal('viewRequestModal');
            
            info.jsEvent.preventDefault();
        },
        editable: false,
        selectable: true,
        selectHelper: true,
        eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
        height: 'auto',
        aspectRatio: window.innerWidth < 576 ? 1 : 1.8,
        nowIndicator: true,
        dayMaxEvents: window.innerWidth < 576 ? 1 : 2,
        windowResize: function(view) {
            calendar.updateSize();
            calendar.setOption('dayMaxEvents', window.innerWidth < 576 ? 1 : 2);
            calendar.setOption('aspectRatio', window.innerWidth < 576 ? 1 : 1.8);
        }
    });

    calendar.render();
    
    // Auto-set end date when start date changes in leave request form
    var startDateField = document.getElementById('start_date');
    var endDateField = document.getElementById('end_date');
    if (startDateField && endDateField) {
        startDateField.addEventListener('change', function() {
            if (!endDateField.value || new Date(endDateField.value) < new Date(this.value)) {
                endDateField.value = this.value;
            }
        });
    }
    
    // Auto-set end date when start date changes in non-working day form
    var nwdStartDateField = document.getElementById('nwd_start_date');
    var nwdEndDateField = document.getElementById('nwd_end_date');
    if (nwdStartDateField && nwdEndDateField) {
        nwdStartDateField.addEventListener('change', function() {
            if (!nwdEndDateField.value || new Date(nwdEndDateField.value) < new Date(this.value)) {
                nwdEndDateField.value = this.value;
            }
        });
    }
});
</script>
@endsection
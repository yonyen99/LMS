@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Delegations</h5>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createDelegationModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Delegation
                </button>
            </div>
        </div>
        <div class="card-body">
            {{-- Dashboard summary card: Total Delegations --}}
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-start border-primary border-3 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-person-lines-fill text-primary fs-1"></i>
                            <div>
                                <h6 class="text-muted mb-1">Total Delegations</h6>
                                <h4 class="mb-0">{{ $delegations->total() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success message --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="60px">#</th>
                            <th scope="col">Delegator</th>
                            <th scope="col">Delegatee</th>
                            <th scope="col">Delegation Type</th>
                            <th scope="col" class="d-none d-md-table-cell">Start Date</th>
                            <th scope="col" class="d-none d-md-table-cell">End Date</th>
                            <th scope="col">Status</th>
                            <th scope="col" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($delegations as $delegation)
                        <tr>
                            <th scope="row">{{ ($delegations->currentPage() - 1) * $delegations->perPage() + $loop->iteration }}</th>
                            <td>{{ $delegation->delegator->name ?? 'N/A' }}</td>
                            <td>{{ $delegation->delegatee->name ?? 'N/A' }}</td>
                            <td>{{ $delegation->delegation_type }}</td>
                            <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($delegation->start_date)->format('Y-m-d') }}</td>
                            <td class="d-none d-md-table-cell">{{ \Carbon\Carbon::parse($delegation->end_date)->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $delegation->status_color }}">
                                    {{ ucfirst($delegation->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            id="actionsDropdown{{ $delegation->id }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu bg-white dropdown-menu-end shadow-sm" aria-labelledby="actionsDropdown{{ $delegation->id }}">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#showDelegationModal{{ $delegation->id }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editDelegationModal{{ $delegation->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteDelegationModal{{ $delegation->id }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- Show Delegation Modal --}}
                        <div class="modal fade" id="showDelegationModal{{ $delegation->id }}" tabindex="-1" aria-labelledby="showDelegationModalLabel{{ $delegation->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="showDelegationModalLabel{{ $delegation->id }}">Delegation Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Delegator:</label>
                                            <p>{{ $delegation->delegator->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Delegatee:</label>
                                            <p>{{ $delegation->delegatee->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Type:</label>
                                            <p>{{ $delegation->delegation_type }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Start Date:</label>
                                            <p>{{ \Carbon\Carbon::parse($delegation->start_date)->format('Y-m-d') }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">End Date:</label>
                                            <p>{{ \Carbon\Carbon::parse($delegation->end_date)->format('Y-m-d') }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status:</label>
                                            <p>
                                                <span class="badge bg-{{ $delegation->status_color }}">
                                                    {{ ucfirst($delegation->status) }}
                                                </span>
                                            </p>
                                        </div>
                                        @if($delegation->created_at || $delegation->updated_at)
                                        <div class="text-muted small">
                                            @if($delegation->created_at)
                                                <div>Created: {{ $delegation->created_at->format('M d, Y h:i A') }}</div>
                                            @endif
                                            @if($delegation->updated_at)
                                                <div>Last Updated: {{ $delegation->updated_at->format('M d, Y h:i A') }}</div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Delegation Modal --}}
                        <div class="modal fade" id="editDelegationModal{{ $delegation->id }}" tabindex="-1" aria-labelledby="editDelegationModalLabel{{ $delegation->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title" id="editDelegationModalLabel{{ $delegation->id }}">Edit Delegation</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('delegations.update', $delegation->id) }}" method="POST" class="needs-validation" novalidate>
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <label for="delegator_id{{ $delegation->id }}" class="form-label fw-bold">Delegator <span class="text-danger">*</span></label>
                                                <select name="delegator_id" id="delegator_id{{ $delegation->id }}" class="form-select @error('delegator_id') is-invalid @enderror" required>
                                                    <option value="">-- Select Delegator --</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('delegator_id', $delegation->delegator_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('delegator_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="delegatee_id{{ $delegation->id }}" class="form-label fw-bold">Delegatee <span class="text-danger">*</span></label>
                                                <select name="delegatee_id" id="delegatee_id{{ $delegation->id }}" class="form-select @error('delegatee_id') is-invalid @enderror" required>
                                                    <option value="">-- Select Delegatee --</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('delegatee_id', $delegation->delegatee_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('delegatee_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="delegation_type{{ $delegation->id }}" class="form-label fw-bold">Delegation Type <span class="text-danger">*</span></label>
                                                <input type="text" name="delegation_type" id="delegation_type{{ $delegation->id }}" class="form-control @error('delegation_type') is-invalid @enderror" value="{{ old('delegation_type', $delegation->delegation_type) }}" required>
                                                @error('delegation_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="start_date{{ $delegation->id }}" class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                                                <div class="position-relative" onclick="document.getElementById('start_date{{ $delegation->id }}').showPicker()">
                                                    <input 
                                                        type="date" 
                                                        name="start_date" 
                                                        id="start_date{{ $delegation->id }}" 
                                                        class="form-control pe-7 @error('start_date') is-invalid @enderror" 
                                                        value="{{ old('start_date', $delegation->start_date) }}" 
                                                        style="cursor: pointer;"
                                                        required
                                                    >
                                                    @error('start_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="end_date{{ $delegation->id }}" class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                                                <div class="position-relative" onclick="document.getElementById('end_date{{ $delegation->id }}').showPicker()">
                                                    <input 
                                                        type="date" 
                                                        name="end_date" 
                                                        id="end_date{{ $delegation->id }}" 
                                                        class="form-control pe-7 @error('end_date') is-invalid @enderror" 
                                                        value="{{ old('end_date', $delegation->end_date) }}" 
                                                        style="cursor: pointer;"
                                                        required
                                                    >
                                                    @error('end_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-info text-white">Update Delegation</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Confirmation Modal --}}
                        <div class="modal fade" id="deleteDelegationModal{{ $delegation->id }}" tabindex="-1" aria-labelledby="deleteDelegationModalLabel{{ $delegation->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteDelegationModalLabel{{ $delegation->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this delegation for <strong>{{ $delegation->delegator->name ?? 'N/A' }}</strong>? This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form id="delete-form-{{ $delegation->id }}" action="{{ route('delegations.destroy', $delegation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-person-x fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No delegations found</h5>
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createDelegationModal">
                                        <i class="bi bi-plus-circle me-1"></i> Create First Delegation
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($delegations->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $delegations->firstItem() }} to {{ $delegations->lastItem() }} of {{ $delegations->total() }} entries
                </div>
                <div>
                    {{ $delegations->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Create Delegation Modal --}}
    <div class="modal fade" id="createDelegationModal" tabindex="-1" aria-labelledby="createDelegationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createDelegationModalLabel">Add New Delegation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('delegations.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="delegator_id" class="form-label fw-bold">Delegator <span class="text-danger">*</span></label>
                            <select name="delegator_id" id="delegator_id" class="form-select @error('delegator_id') is-invalid @enderror" required>
                                <option value="">-- Select Delegator --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('delegator_id', Auth::id()) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('delegator_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="delegatee_id" class="form-label fw-bold">Delegatee <span class="text-danger">*</span></label>
                            <select name="delegatee_id" id="delegatee_id" class="form-select @error('delegatee_id') is-invalid @enderror" required>
                                <option value="">-- Select Delegatee --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('delegatee_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('delegatee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="delegation_type" class="form-label fw-bold">Delegation Type <span class="text-danger">*</span></label>
                            <input type="text" name="delegation_type" id="delegation_type" class="form-control @error('delegation_type') is-invalid @enderror" value="{{ old('delegation_type') }}" placeholder="e.g. Leave Approval" required>
                            @error('delegation_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <div class="position-relative" onclick="document.getElementById('start_date').showPicker()">
                                <input 
                                    type="date" 
                                    name="start_date" 
                                    id="start_date" 
                                    class="form-control pe-7 @error('start_date') is-invalid @enderror" 
                                    value="{{ old('start_date') }}" 
                                    style="cursor: pointer;"
                                    required
                                >
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <div class="position-relative" onclick="document.getElementById('end_date').showPicker()">
                                <input 
                                    type="date" 
                                    name="end_date" 
                                    id="end_date" 
                                    class="form-control pe-7 @error('end_date') is-invalid @enderror" 
                                    value="{{ old('end_date') }}" 
                                    style="cursor: pointer;"
                                    required
                                >
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Delegation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .dropdown-toggle::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 10rem;
        border-radius: 0.25rem;
    }
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .modal-content {
        border-radius: 0.5rem;
    }
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    @media screen and (max-width: 767px) {
        .d-none.d-md-table-cell {
            display: none !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Client-side validation
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Add loading state for delete forms
        const deleteForms = document.querySelectorAll('form[id^="delete-form-"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                if (button) {
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...';
                    button.disabled = true;
                }
            });
        });
    });
</script>
@endsection
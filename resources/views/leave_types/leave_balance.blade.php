@extends('layouts.app')

@section('title', 'Leave Balances')

@section('styles')
    <style>
        .transition {
            transition: background-color 0.2s ease-in-out;
        }
        .transition:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('content')
    <div class="container px-4 py-5 w-100 shadow-sm p-4 rounded bg-white">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <h1 class="h3 fw-bold mb-3 mb-md-0">Leave Balances</h1>
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 w-40 w-md-auto">
                <!-- Search -->
                <form method="GET" class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search..."
                           value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit" title="Search">Search</button>
                </form>
            </div>
        </div>

        <!-- Leave Balances Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="60px">#</th>
                        <th scope="col">User</th>
                        <th scope="col">Leave Type</th>
                        <th scope="col">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaveBalances as $balance)
                        <tr class="transition">
                            <th scope="row">{{ ($leaveBalances->currentPage() - 1) * $leaveBalances->perPage() + $loop->iteration }}</th>
                            <td>{{ $balance->user->name }}</td>
                            <td>{{ $balance->leaveType->name }}</td>
                            <td>{{ $balance->balance }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-clock fs-1 text-muted mb-2"></i>
                                    <h5 class="text-muted">No leave balances found</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            {{ $leaveBalances->links() }}
        </div>
    </div>
@endsection
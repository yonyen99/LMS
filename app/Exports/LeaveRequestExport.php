<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class LeaveRequestExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = LeaveRequest::query()->with(['leaveType', 'user']);

        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            // Admin: no restrictions
        } elseif ($user->hasRole('Manager')) {
            // Manager: only their department, excluding Admins/Managers
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id)
                    ->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Admin', 'Manager']));
            });
        } else {
            // Employee: only own requests
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($this->request->filled('statuses')) {
            $query->whereIn('status', $this->request->input('statuses', []));
        }

        if ($this->request->filled('type')) {
            $query->whereHas('leaveType', function ($q) {
                $q->where('name', $this->request->input('type'));
            });
        }

        if ($this->request->filled('status_request')) {
            $query->where('status', $this->request->input('status_request'));
        }

        if ($this->request->filled('search')) {
            $search = $this->request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('leaveType', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sortOrder = $this->request->input('sort_order', 'new');
        $query->orderBy('id', $sortOrder === 'new' ? 'desc' : 'asc');

        return $query->get();
    }


    public function headings(): array
    {
        return [
            'No.',
            'Employee',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Reason',
            'Duration',
            'Type',
            'Status',
            'Requested At',
            'Last Changed At',
        ];
    }

    public function map($leaveRequest): array
    {
        $this->rowNumber++;

        $displayStatus = ucfirst(strtolower($leaveRequest->status));
        return [
            $this->rowNumber,
            optional($leaveRequest->user)->name ?? '-',
            optional($leaveRequest->start_date)->format('d/m/Y') ?? '-',
            ucfirst($leaveRequest->start_time),
            optional($leaveRequest->end_date)->format('d/m/Y') ?? '-',
            ucfirst($leaveRequest->end_time),
            $leaveRequest->reason ?? '-',
            number_format($leaveRequest->duration, 2),
            optional($leaveRequest->leaveType)->name ?? '-',
            $displayStatus,
            optional($leaveRequest->requested_at)->format('d/m/Y') ?? '-',
            optional($leaveRequest->last_changed_at)->format('d/m/Y') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFE0E0E0']]],
        ];
    }
}

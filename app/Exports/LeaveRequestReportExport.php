<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class LeaveRequestReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function query()
    {
        $query = LeaveRequest::with(['user', 'leaveType', 'user.department', 'approver']);
        
        // Apply filters
        if ($this->request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $this->request->start_date);
        }
        
        if ($this->request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $this->request->end_date);
        }
        
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        
        if ($this->request->filled('leave_type_id')) {
            $query->where('leave_type_id', $this->request->leave_type_id);
        }
        
        if ($this->request->filled('department_id')) {
            $query->whereHas('user', function($q) {
                $q->where('department_id', $this->request->department_id);
            });
        }
        
        if ($this->request->filled('user_id')) {
            $query->where('user_id', $this->request->user_id);
        }
        
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('leaveType', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user.department', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sort options
        $sortBy = $this->request->get('sort_by', 'start_date');
        $sortOrder = $this->request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        return $query;
    }
    
    public function headings(): array
    {
        return [
            'Employee Name',
            'Department',
            'Leave Type',
            'Start Date',
            'End Date',
            'Duration (Days)',
            'Status',
            'Reason',
            'Requested At',
            'Approved By',
            'Last Changed At'
        ];
    }
    
    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->user->name,
            $leaveRequest->user->department->name ?? 'N/A',
            $leaveRequest->leaveType->name,
            $leaveRequest->start_date->format('M d, Y') . ' (' . ucfirst($leaveRequest->start_time) . ')',
            $leaveRequest->end_date->format('M d, Y') . ' (' . ucfirst($leaveRequest->end_time) . ')',
            $leaveRequest->duration,
            $leaveRequest->status,
            $leaveRequest->reason,
            $leaveRequest->requested_at->format('M d, Y H:i'),
            $leaveRequest->approver->name ?? 'N/A',
            $leaveRequest->last_changed_at->format('M d, Y H:i'),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    public function title(): string
    {
        return 'Leave Requests Report';
    }
}
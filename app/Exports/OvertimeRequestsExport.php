<?php

namespace App\Exports;

use App\Models\OvertimeRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class OvertimeRequestsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $overtimes;

    public function __construct($overtimes)
    {
        $this->overtimes = $overtimes;
    }

    public function collection()
    {
        return $this->overtimes;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Department',
            'Request Date',
            'Start Time',
            'End Time',
            'Status',
        ];
    }

    public function map($overtime): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $overtime->user->name,
            $overtime->department->name,
            $overtime->overtime_date,
            $overtime->start_time,
            $overtime->end_time,
            ucfirst($overtime->status),
        ];
    }

    public function title(): string
    {
        return 'Overtime Requests';
    }
}
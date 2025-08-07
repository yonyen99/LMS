<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeaveBalanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $user;
    protected $summaries;

    public function __construct($user, $summaries)
    {
        $this->user = $user;
        $this->summaries = $summaries;
    }

    public function title(): string
    {
        return 'Leave Balance';
    }

    public function collection()
    {
        return collect($this->summaries);
    }

    public function headings(): array
    {
        return [
            ['Leave Balance Report'],
            ['Generated on: ' . now()->format('Y-m-d H:i:s')],
            ['Employee: ' . $this->user->name],
            ['Department: ' . ($this->user->department->name ?? 'N/A')],
            [],
            ['Leave Type', 'Entitled (days)', 'Used (days)', 'Pending (days)', 'Available (days)']
        ];
    }

    public function map($summary): array
    {
        return [
            $summary['leaveType']->name,
            $summary['entitled'],
            $summary['taken'],
            $summary['requested'] + $summary['planned'],
            $summary['available_actual']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['italic' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            6 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DDDDDD']]
            ],
            'A' => ['width' => 30],
            'B' => ['width' => 15],
            'C' => ['width' => 15],
            'D' => ['width' => 15],
            'E' => ['width' => 15]
        ];
    }
}
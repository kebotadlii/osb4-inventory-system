<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExpenseTemplateExport implements WithHeadings, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'Tanggal (dd/mm/yyyy)',
            'Nama Item',
            'No Invoice',
            'Provider',
            'Jumlah',
            'Nominal',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 30,
            'C' => 20,
            'D' => 25,
            'E' => 12,
            'F' => 18,
        ];
    }
}

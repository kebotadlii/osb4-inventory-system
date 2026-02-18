<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class TemplateExcelExport implements FromArray, WithHeadings, WithColumnWidths
{
    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'Item',
            'Qty',
            'Tanggal (dd/mm/yyyy)',
            'Keterangan',
        ];
    }

    /**
     * Isi kosong (template)
     */
    public function array(): array
    {
        return [];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40, // Item
            'B' => 10, // Qty
            'C' => 20, // Tanggal
            'D' => 45, // Keterangan
        ];
    }
}

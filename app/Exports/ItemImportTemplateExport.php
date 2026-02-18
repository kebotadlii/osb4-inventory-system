<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemImportTemplateExport implements FromArray, WithHeadings
{
    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'Nama Item',
            'Jumlah',
            'Harga',
            'No PO',
            'Tanggal (DD/MM/YYYY)',
            'Keterangan',
        ];
    }

    /**
     * Data kosong (template)
     */
    public function array(): array
    {
        return [];
    }
}

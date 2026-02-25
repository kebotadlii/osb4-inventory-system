<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemImportTemplateExport implements FromArray, WithHeadings
{
    /**
     * Header Excel (WAJIB SESUAI ItemsImport)
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
     * Data kosong (template tanpa contoh)
     */
    public function array(): array
    {
        return [];
    }
}
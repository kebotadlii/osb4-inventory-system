<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HistoryInExport implements FromCollection, WithHeadings
{
    protected Collection $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        $rows = collect();
        $no = 1;

        foreach ($this->transactions as $trx) {
            $rows->push([
                $no++,
                $trx->item->name ?? '-',
                $trx->item->category->name ?? '-',
                $trx->tanggal->format('d-m-Y'),
                $trx->quantity,
                $trx->price,
                $trx->quantity * $trx->price,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Item',
            'Kategori',
            'Tanggal',
            'Qty Masuk',
            'Harga',
            'Total',
        ];
    }
}

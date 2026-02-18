<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsExport implements FromCollection, WithHeadings
{
    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function collection()
    {
        return Item::with('transactions')
            ->where('category_id', $this->categoryId)
            ->get()
            ->map(function ($item) {

                // =========================
                // HITUNG STOK (IN - OUT)
                // =========================
                $stock = $item->transactions->sum(function ($t) {
                    return $t->type === 'in'
                        ? $t->quantity
                        : -$t->quantity;
                });

                // =========================
                // NO PO (KHUSUS PEMBELIAN)
                // =========================
                $noPo = $item->transactions
                    ->where('type', 'in')
                    ->pluck('no_po')
                    ->filter(function ($value) {
                        return !empty(trim($value));
                    })
                    ->first() ?? '-';

                // =========================
                // HARGA PEMBELIAN (PERTAMA)
                // =========================
                $price = $item->transactions
                    ->where('type', 'in')
                    ->pluck('price')
                    ->filter()
                    ->first() ?? 0;

                return [
                    'Nama Item'        => $item->name,
                    'Stok'             => $stock,
                    'Harga'            => $price,
                    'No PO'            => $noPo,
                    'Total Nilai Stok' => $price * $stock,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Item',
            'Stok',
            'Harga',
            'No PO',
            'Total Nilai Stok',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithStyles, WithCustomStartCell
{
    protected ?int $categoryId;
    protected int $year;

    public function __construct($categoryId = null, $year = null)
    {
        $this->categoryId = $categoryId;
        $this->year = $year ?? now()->year;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $rows = collect();

        $grandStock = 0;
        $grandValue = 0;

        $items = Item::query()
            ->with('category')

            // STOK MASUK TAHUN TERPILIH
            ->withSum(['transactions as stok_in_year' => function ($q) {
                $q->where('type', 'in')
                    ->whereYear('tanggal', $this->year);
            }], 'quantity')

            // STOK KELUAR TAHUN TERPILIH
            ->withSum(['transactions as stok_out_year' => function ($q) {
                $q->where('type', 'out')
                    ->whereYear('tanggal', $this->year);
            }], 'quantity')

            // TOTAL STOK MASUK
            ->withSum(['transactions as stok_in_all' => function ($q) {
                $q->where('type', 'in');
            }], 'quantity')

            // TOTAL STOK KELUAR
            ->withSum(['transactions as stok_out_all' => function ($q) {
                $q->where('type', 'out');
            }], 'quantity')

            // TRANSAKSI TERAKHIR
            ->with(['transactions' => function ($q) {
                $q->where('type', 'in')
                    ->latest('tanggal')
                    ->limit(1);
            }])

            ->when($this->categoryId, function ($q) {
                $q->where('category_id', $this->categoryId);
            })

            ->orderBy('name')
            ->get();

        // =========================
        // FILTER KATEGORI
        // =========================
        if ($this->categoryId) {

            foreach ($items as $item) {

                $currentStock = max(
                    0,
                    ($item->stok_in_all ?? 0) - ($item->stok_out_all ?? 0)
                );

                $price = optional($item->transactions->first())->price ?? 0;

                $total = $currentStock * $price;

                $grandStock += $currentStock;
                $grandValue += $total;

                $rows->push([
                    $item->name,
                    $item->category->name ?? '-',
                    $item->stok_in_year ?? 0,
                    $item->stok_out_year ?? 0,
                    $currentStock,
                    $price,
                    $total,
                    optional($item->transactions->first())->keterangan ?? '',
                ]);
            }

        } else {

            // =========================
            // SEMUA KATEGORI
            // =========================
            $grouped = $items->groupBy(fn ($item) =>
                $item->category->name ?? 'Tanpa Kategori'
            );

            foreach ($grouped as $categoryName => $group) {

                $rows->push(['', $categoryName, '', '', '', '', '', '']);

                $subStock = 0;
                $subValue = 0;

                foreach ($group as $item) {

                    $currentStock = max(
                        0,
                        ($item->stok_in_all ?? 0) - ($item->stok_out_all ?? 0)
                    );

                    $price = optional($item->transactions->first())->price ?? 0;

                    $total = $currentStock * $price;

                    $subStock += $currentStock;
                    $subValue += $total;

                    $grandStock += $currentStock;
                    $grandValue += $total;

                    $rows->push([
                        $item->name,
                        '',
                        $item->stok_in_year ?? 0,
                        $item->stok_out_year ?? 0,
                        $currentStock,
                        $price,
                        $total,
                        optional($item->transactions->first())->keterangan ?? '',
                    ]);
                }

                $rows->push([
                    '',
                    'SUBTOTAL',
                    '',
                    '',
                    $subStock,
                    '',
                    $subValue,
                    ''
                ]);

                $rows->push(['', '', '', '', '', '', '', '']);
            }
        }

        $rows->push([
            '',
            'GRAND TOTAL',
            '',
            '',
            $grandStock,
            '',
            $grandValue,
            ''
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN STOK BARANG TAHUN ' . $this->year);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue(
            'A2',
            $this->categoryId
                ? 'Filter: Kategori Terpilih'
                : 'Filter: Semua Kategori'
        );

        $sheet->fromArray([[
            'Nama Barang',
            'Kategori',
            'Stok Masuk (' . $this->year . ')',
            'Stok Keluar (' . $this->year . ')',
            'Stok Saat Ini',
            'Harga Terakhir',
            'Total Nilai',
            'Keterangan',
        ]], null, 'A5');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->getStyle('A5:H5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
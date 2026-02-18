<?php

namespace App\Exports;

use App\Models\Item;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements
    FromCollection,
    WithStyles,
    WithCustomStartCell
{
    protected ?int $categoryId;
    protected int $year;

    public function __construct($categoryId = null, $year = null)
    {
        $this->categoryId = $categoryId;
        $this->year = $year ?? now()->year;
    }

    /**
     * DATA DIMULAI BARIS 6
     */
    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $rows = collect();
        $grandStock = 0;
        $grandValue = 0;

        $items = Item::with(['category', 'transactions'])
            ->when($this->categoryId, fn ($q) =>
                $q->where('category_id', $this->categoryId)
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TANPA FILTER KATEGORI → GROUP PER KATEGORI
        |--------------------------------------------------------------------------
        */
        if (!$this->categoryId) {

            $items = $items->groupBy(fn ($item) =>
                $item->category->name ?? 'Tanpa Kategori'
            );

            foreach ($items as $category => $categoryItems) {

                // Judul kategori
                $rows->push(['', $category, '', '', '', '', '', '']);

                $subStock = 0;
                $subValue = 0;

                foreach ($categoryItems as $item) {
                    [$inYear, $outYear, $currentStock, $price, $total, $ket]
                        = $this->calc($item);

                    $subStock += $currentStock;
                    $subValue += $total;

                    $rows->push([
                        $item->name,
                        '',
                        $inYear,
                        $outYear,
                        $currentStock,
                        $price,
                        $total,
                        $ket,
                    ]);
                }

                // SUBTOTAL
                $rows->push(['', 'SUBTOTAL', '', '', $subStock, '', $subValue, '']);
                $rows->push(['', '', '', '', '', '', '', '']);

                $grandStock += $subStock;
                $grandValue += $subValue;
            }
        }
        /*
        |--------------------------------------------------------------------------
        | DENGAN FILTER KATEGORI → FLAT
        |--------------------------------------------------------------------------
        */
        else {

            foreach ($items as $item) {
                [$inYear, $outYear, $currentStock, $price, $total, $ket]
                    = $this->calc($item);

                $grandStock += $currentStock;
                $grandValue += $total;

                $rows->push([
                    $item->name,
                    $item->category->name ?? '-',
                    $inYear,
                    $outYear,
                    $currentStock,
                    $price,
                    $total,
                    $ket,
                ]);
            }
        }

        // GRAND TOTAL
        $rows->push(['', 'GRAND TOTAL', '', '', $grandStock, '', $grandValue, '']);

        return $rows;
    }

    /**
     * LOGIKA HITUNG STOK (KONSISTEN DENGAN HALAMAN)
     */
    private function calc($item): array
    {
        $inAll  = $item->transactions->where('type', 'in');
        $outAll = $item->transactions->where('type', 'out');

        // Pergerakan tahun terpilih
        $inYear = $inAll->filter(fn ($t) =>
            Carbon::parse($t->tanggal)->year == $this->year
        );

        $outYear = $outAll->filter(fn ($t) =>
            Carbon::parse($t->tanggal)->year == $this->year
        );

        $stokInYear  = $inYear->sum('quantity');
        $stokOutYear = $outYear->sum('quantity');

        // Stok sekarang (SEMUA TAHUN)
        $stokInAll  = $inAll->sum('quantity');
        $stokOutAll = $outAll->sum('quantity');
        $currentStock = max(0, $stokInAll - $stokOutAll);

        $lastIn = $inAll->sortByDesc('tanggal')->first();

        $totalValue = $currentStock * ($lastIn->price ?? 0);

        return [
            $stokInYear,
            $stokOutYear,
            $currentStock,
            $lastIn->price ?? 0,
            $totalValue,
            $lastIn->keterangan ?? '',
        ];
    }

    /**
     * STYLE EXCEL
     */
    public function styles(Worksheet $sheet)
    {
        // =============================
        // JUDUL
        // =============================
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue(
            'A1',
            'LAPORAN STOK BARANG TAHUN ' . $this->year
        );

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue(
            'A2',
            $this->categoryId
                ? 'Filter: Kategori Terpilih'
                : 'Filter: Semua Kategori'
        );

        // =============================
        // HEADER TABLE
        // =============================
        $sheet->fromArray([
            [
                'Nama Barang',
                'Kategori',
                'Stok Masuk (' . $this->year . ')',
                'Stok Keluar (' . $this->year . ')',
                'Stok Saat Ini',
                'Harga Terakhir',
                'Total Nilai',
                'Keterangan',
            ]
        ], null, 'A5');

        // =============================
        // STYLE JUDUL & HEADER
        // =============================
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->getStyle('A5:H5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // =============================
        // BOLD SUBTOTAL & GRAND TOTAL
        // =============================
        $highestRow = $sheet->getHighestRow();

        for ($row = 6; $row <= $highestRow; $row++) {

            $label = trim((string) $sheet->getCell("B{$row}")->getValue());

            if (in_array($label, ['SUBTOTAL', 'GRAND TOTAL'])) {

                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                if ($label === 'GRAND TOTAL') {
                    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => ['rgb' => 'F1F3F5'],
                        ],
                    ]);
                }
            }
        }

        // =============================
        // AUTOSIZE
        // =============================
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}

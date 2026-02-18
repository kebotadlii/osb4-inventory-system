<?php

namespace App\Exports;

use App\Models\ItemTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithCustomStartCell
{
    protected Request $request;
    protected int $no = 1;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * DATA DIMULAI DARI BARIS 5
     */
    public function startCell(): string
    {
        return 'A5';
    }

    /**
     * QUERY DATA
     */
    public function collection()
    {
        $query = ItemTransaction::with(['item.category'])
            ->orderBy('tanggal', 'desc');

        $month = (int) ($this->request->month ?? now()->month);
        $year  = (int) ($this->request->year ?? now()->year);

        $query->whereMonth('tanggal', $month)
              ->whereYear('tanggal', $year);

        if ($this->request->filled('type')) {
            $query->where('type', $this->request->type);
        }

        if ($this->request->filled('category_id')) {
            $query->whereHas('item', function ($q) {
                $q->where('category_id', $this->request->category_id);
            });
        }

        return $query->get();
    }

    /**
     * HEADER TABEL
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Barang',
            'Kategori',
            'Jenis Transaksi',
            'Jumlah',
            'Keterangan / Bukti',
        ];
    }

    /**
     * ISI BARIS
     */
    public function map($trx): array
    {
        $keterangan = $trx->keterangan ?? '';

        if (!empty($trx->bukti_link)) {
            $keterangan .= ($keterangan ? ' - ' : '') . $trx->bukti_link;
        }

        return [
            $this->no++,
            optional($trx->tanggal)->format('d-m-Y'),
            $trx->item->name ?? '-',
            $trx->item->category->name ?? '-',
            $trx->type === 'in' ? 'Barang Masuk' : 'Barang Keluar',
            $trx->quantity,
            $keterangan ?: '-',
        ];
    }

    /**
     * STYLE EXCEL
     */
    public function styles(Worksheet $sheet)
    {
        $month = (int) ($this->request->month ?? now()->month);
        $year  = (int) ($this->request->year ?? now()->year);

        $monthName = Carbon::createFromDate($year, $month, 1)
            ->translatedFormat('F');

        $typeText = $this->request->type === 'in'
            ? 'Barang Masuk'
            : 'Barang Keluar';

        // JUDUL
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN HISTORY TRANSAKSI');

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue(
            'A2',
            "Periode: {$monthName} {$year} | Jenis: {$typeText}"
        );

        // STYLE JUDUL
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'alignment' => ['horizontal' => 'center'],
        ]);

        // HEADER TABLE
        $sheet->getStyle('A5:G5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // AUTOSIZE
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}

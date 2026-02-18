<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithColumnFormatting,
    WithCustomStartCell
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithColumnFormatting,
    WithCustomStartCell
{
    protected Request $request;
    protected int $no = 1;
    protected float $total = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Mulai tabel dari baris 4
     */
    public function startCell(): string
    {
        return 'A4';
    }

    /**
     * DATA EXPORT (IKUT FILTER)
     */
    public function collection()
    {
        $query = Expense::with('category');

        // FILTER TAHUN
        if ($this->request->year) {
            $query->whereYear('expense_date', $this->request->year);
        }

        // FILTER BULAN
        if ($this->request->month) {
            $query->whereMonth('expense_date', $this->request->month);
        }

        // 🔥 FIX FILTER KATEGORI (TERIMA 2 NAMA PARAMETER)
        $categoryId =
            $this->request->expense_category_id
            ?? $this->request->category_id;

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        return $query
            ->orderBy('expense_date', 'asc')
            ->get();
    }

    /**
     * HEADER TABEL
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kategori',
            'Nama Item',
            'No Invoice',
            'Provider',
            'Qty',
            'Jumlah (Rp)',
        ];
    }

    /**
     * ISI BARIS
     */
    public function map($expense): array
    {
        $this->total += $expense->amount;

        return [
            $this->no++,
            $expense->expense_date?->format('d-m-Y') ?? '-',
            $expense->category->name ?? '-',
            $expense->item_name,
            $expense->invoice_number ?? '-',
            $expense->provider ?? '-',
            $expense->quantity ?? 1,
            $expense->amount,
        ];
    }

    /**
     * STYLE + JUDUL + TOTAL
     */
    public function styles(Worksheet $sheet)
    {
        // JUDUL
        $sheet->setCellValue('A1', 'LAPORAN PENGELUARAN OPERASIONAL');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // SUB JUDUL FILTER
        $filter = [];

        if ($this->request->month) {
            $filter[] = 'Bulan: ' .
                Carbon::create()->month($this->request->month)
                    ->translatedFormat('F');
        }

        if ($this->request->year) {
            $filter[] = 'Tahun: ' . $this->request->year;
        }

        if (
            $this->request->expense_category_id
            || $this->request->category_id
        ) {
            $filter[] = 'Kategori Terfilter';
        }

        $sheet->setCellValue(
            'A2',
            $filter ? implode(' | ', $filter) : 'Semua Data'
        );
        $sheet->mergeCells('A2:H2');

        // HEADER
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);

        // TOTAL
        $lastRow = $sheet->getHighestRow() + 1;
        $sheet->mergeCells("A{$lastRow}:G{$lastRow}");
        $sheet->setCellValue("A{$lastRow}", 'TOTAL');
        $sheet->setCellValue("H{$lastRow}", $this->total);
        $sheet->getStyle("A{$lastRow}:H{$lastRow}")
            ->getFont()->setBold(true);

        return [];
    }

    /**
     * FORMAT KOLOM
     */
    public function columnFormats(): array
    {
        return [
            'H' => '"Rp"#,##0',
        ];
    }
}

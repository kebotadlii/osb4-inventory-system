<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Excel
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Maatwebsite\Excel\Facades\Excel;

// Imports
use App\Imports\ItemOutImport;

// Exports
use App\Exports\ItemsExport;
use App\Exports\TemplateExcelExport;
use App\Exports\StockReportExport;
use App\Exports\HistoryExport;
use App\Exports\ExpenseTemplateExport;

class ExcelController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | GENERIC TEMPLATE EXPORT
    |----------------------------------------------------------------------
    */
    public static function exportTemplate(array $headers, string $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $filename);
    }

    /*
    |----------------------------------------------------------------------
    | TEMPLATE BARANG KELUAR
    |----------------------------------------------------------------------
    */
    public function downloadItemOutTemplate()
    {
        $headers = [
            'Tanggal (DD/MM/YYYY)',
            'Nama Barang',
            'Qty Keluar',
            'Keterangan',
        ];

        $filename = 'template_import_barang_keluar.xlsx';

        return self::exportTemplate($headers, $filename);
    }

    /*
    |----------------------------------------------------------------------
    | IMPORT ITEMS (BARANG MASUK)
    |----------------------------------------------------------------------
    */
    public function importItemsForm($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('items.import', compact('category'));
    }

    public function importItemsProcess(Request $request, $categoryId)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $category = Category::findOrFail($categoryId);
        $sheet = IOFactory::load($request->file('file')->getRealPath())
            ->getActiveSheet();

        DB::beginTransaction();

        try {
            for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {

                $name   = trim((string) $sheet->getCell("A$row")->getValue());
                $qty    = (int) $sheet->getCell("B$row")->getValue();
                $price  = (int) preg_replace('/[^0-9]/', '', (string) $sheet->getCell("C$row")->getValue());
                $noPo   = trim((string) $sheet->getCell("D$row")->getValue());
                $tglRaw = $sheet->getCell("E$row")->getValue();
                $ket    = trim((string) $sheet->getCell("F$row")->getValue()) ?: 'Import Excel';

                if ($name === '' || $qty <= 0) continue;

                $tanggal = $this->parseTanggal($tglRaw);

                $item = Item::firstOrCreate(
                    ['name' => $name, 'category_id' => $category->id],
                    ['price' => $price, 'stock' => 0]
                );

                if ($price > 0) {
                    $item->update(['price' => $price]);
                }

                ItemTransaction::create([
                    'item_id'    => $item->id,
                    'type'       => ItemTransaction::TYPE_IN,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'total'      => $qty * $price,
                    'no_po'      => $noPo,
                    'tanggal'    => $tanggal,
                    'keterangan' => $ket,
                ]);

                $item->increment('stock', $qty);
            }

            DB::commit();
            return back()->with('success', 'Import barang masuk berhasil');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |----------------------------------------------------------------------
    | IMPORT BARANG KELUAR
    |----------------------------------------------------------------------
    */
    public function importItemOut(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new ItemOutImport();
        Excel::import($import, $request->file('file'));

        return back()
            ->with('success', 'Import barang keluar selesai')
            ->with('failed_import', $import->getFailedRows());
    }

    /*
    |----------------------------------------------------------------------
    | IMPORT EXPENSES (DENGAN NO INVOICE)
    |----------------------------------------------------------------------
    */
    public function importExpensesByCategoryProcess(Request $request, $categoryId)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $category = ExpenseCategory::findOrFail($categoryId);
        $sheet = IOFactory::load($request->file('file')->getRealPath())
            ->getActiveSheet();

        DB::beginTransaction();

        try {
            for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {

                $tglRaw    = $sheet->getCell("A$row")->getValue();
                $itemName  = trim((string) $sheet->getCell("B$row")->getValue());
                $invoice   = trim((string) $sheet->getCell("C$row")->getValue());
                $provider  = trim((string) $sheet->getCell("D$row")->getValue());
                $qtyRaw    = $sheet->getCell("E$row")->getValue();
                $amountRaw = $sheet->getCell("F$row")->getValue();

                if ($itemName === '') continue;

                $amount = (int) preg_replace('/[^0-9]/', '', (string) $amountRaw);
                if ($amount <= 0) continue;

                Expense::create([
                    'expense_category_id' => $category->id,
                    'item_name'           => $itemName,
                    'invoice_number'      => $invoice ?: null,
                    'provider'            => $provider ?: null,
                    'quantity'            => is_numeric($qtyRaw) ? (int) $qtyRaw : 1,
                    'amount'              => $amount,
                    'expense_date'        => $this->parseTanggal($tglRaw),
                ]);
            }

            DB::commit();
            return redirect()
                ->route('expense.categories.index')
                ->with('success', 'Import kategori "' . $category->name . '" berhasil');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |----------------------------------------------------------------------
    | HELPER PARSE TANGGAL (FIX TAHUN LONCAT)
    |----------------------------------------------------------------------
    */
    private function parseTanggal($value)
    {
        if (empty($value)) {
            return Carbon::now('Asia/Jakarta')->format('Y-m-d');
        }

        // Jika numeric (tanggal asli Excel)
        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            )
            ->setTimezone('Asia/Jakarta')
            ->format('Y-m-d');
        }

        // Jika string
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value), 'Asia/Jakarta')
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                // lanjut format berikutnya
            }
        }

        return Carbon::now('Asia/Jakarta')->format('Y-m-d');
    }
}

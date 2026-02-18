<?php

namespace App\Imports;

use App\Models\Expense;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| HEADER TETAP APA ADANYA (Nama Item, Tanggal, dll)
|--------------------------------------------------------------------------
*/
HeadingRowFormatter::default('none');

class ExpensesImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            if (empty($row['Nama Item'])) {
                continue;
            }

            // =============================
            // AUTO NO INVOICE
            // =============================
            $invoiceNumber = 'EXP-' . now()->format('Ymd') . '-' . ($index + 1);

            // =============================
            // PARSE TANGGAL (FINAL & AMAN)
            // =============================
            $expenseDate = null;

            if (!empty($row['Tanggal'])) {

                $raw = $row['Tanggal'];

                // Excel serial number
                if (is_numeric($raw)) {
                    $expenseDate = Carbon::createFromTimestamp(
                        ExcelDate::excelToTimestamp($raw)
                    )->toDateString();
                }
                // String date
                else {
                    foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
                        try {
                            $expenseDate = Carbon::createFromFormat(
                                $format,
                                trim($raw)
                            )->toDateString();
                            break;
                        } catch (\Exception $e) {}
                    }
                }
            }

            // fallback kalau kosong / gagal parse
            $expenseDate ??= now()->toDateString();

            // =============================
            // SIMPAN
            // =============================
            Expense::create([
                'invoice_number' => $invoiceNumber,
                'item_name'      => trim((string) $row['Nama Item']),
                'provider'       => $row['Provider'] ?? null,
                'quantity'       => !empty($row['Jumlah']) ? (int) $row['Jumlah'] : 1,
                'expense_date'   => $expenseDate,
                'amount'         => !empty($row['Nominal'])
                    ? (int) preg_replace('/[^0-9]/', '', $row['Nominal'])
                    : 0,
            ]);
        }
    }
}

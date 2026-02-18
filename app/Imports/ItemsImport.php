<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| HEADER FORMAT → SLUG
|--------------------------------------------------------------------------
*/
HeadingRowFormatter::default('slug');

class ItemsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // =============================
            // VALIDASI WAJIB
            // =============================
            if (
                empty($row['nama-item']) ||
                empty($row['jumlah'])
            ) {
                continue;
            }

            $namaItem = trim((string) $row['nama-item']);
            $jumlah   = (int) $row['jumlah'];

            // =============================
            // PARSE TANGGAL (FINAL & AMAN)
            // =============================
            $tanggal = null;

            if (!empty($row['tanggal-dd-mm-yyyy'])) {

                $raw = $row['tanggal-dd-mm-yyyy'];

                // Excel serial number
                if (is_numeric($raw)) {
                    $tanggal = Carbon::createFromTimestamp(
                        ExcelDate::excelToTimestamp($raw)
                    )->toDateString();
                }
                // String date
                else {
                    foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
                        try {
                            $tanggal = Carbon::createFromFormat(
                                $format,
                                trim($raw)
                            )->toDateString();
                            break;
                        } catch (\Exception $e) {}
                    }
                }
            }

            // =============================
            // CARI ITEM
            // =============================
            $item = Item::where('name', $namaItem)->first();

            if ($item) {

                // ITEM SUDAH ADA → UPDATE
                $item->quantity += $jumlah;

                if (!empty($row['harga'])) {
                    $item->price = (int) $row['harga'];
                }

                if (!empty($row['no-po'])) {
                    $item->no_po = trim((string) $row['no-po']);
                }

                if ($tanggal) {
                    $item->tanggal_pembelian = $tanggal;
                }

                if (!empty($row['keterangan-bukti-link'])) {
                    $item->keterangan = $row['keterangan-bukti-link'];
                }

                $item->save();

            } else {

                // ITEM BARU
                Item::create([
                    'name'              => $namaItem,
                    'quantity'          => $jumlah,
                    'price'             => !empty($row['harga']) ? (int) $row['harga'] : null,
                    'no_po'             => $row['no-po'] ?? null,
                    'tanggal_pembelian' => $tanggal,
                    'keterangan'        => $row['keterangan-bukti-link'] ?? null,
                ]);
            }
        }
    }
}

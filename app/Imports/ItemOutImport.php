<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ItemOutImport implements ToCollection
{
    protected array $failedRows = [];

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function collection(Collection $rows)
    {
        if ($rows->count() < 2) return;

        // =============================
        // HEADER (disesuaikan template kamu)
        // =============================
        $header = array_map(
            fn ($h) => strtolower(trim((string) $h)),
            $rows->shift()->toArray()
        );

        $map = [
            'tanggal'    => $this->find($header, ['tanggal']),
            'item'       => $this->find($header, ['nama barang']),
            'quantity'   => $this->find($header, ['qty keluar', 'qty']),
            'keterangan' => $this->find($header, ['keterangan']),
        ];

        if ($map['item'] === null || $map['quantity'] === null) {
            $this->failedRows[] = [
                'row'    => '-',
                'item'   => '-',
                'qty'    => '-',
                'reason' => 'Header tidak sesuai template (Nama Barang / Qty Keluar)',
            ];
            return;
        }

        // =============================
        // DATA
        // =============================
        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2;

            try {

                // =============================
                // ITEM
                // =============================
                $itemName = trim((string) ($row[$map['item']] ?? ''));

                if ($itemName === '') {
                    throw new \Exception('Nama barang kosong');
                }

                $item = Item::whereRaw('LOWER(name) = ?', [strtolower($itemName)])
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    throw new \Exception('Item tidak ditemukan');
                }

                // =============================
                // QTY
                // =============================
                $qty = (int) ($row[$map['quantity']] ?? 0);

                if ($qty <= 0) {
                    throw new \Exception('Qty tidak valid');
                }

                if ($item->stock < $qty) {
                    throw new \Exception('Stok tidak mencukupi. Sisa stok: ' . $item->stock);
                }

                // =============================
                // TANGGAL (AMAN & FLEXIBLE)
                // =============================
                $tanggal = now()->toDateString();

                if ($map['tanggal'] !== null && !empty($row[$map['tanggal']])) {

                    $raw = $row[$map['tanggal']];

                    if (is_numeric($raw)) {
                        $tanggal = Carbon::instance(
                            ExcelDate::excelToDateTimeObject($raw)
                        )->format('Y-m-d');
                    } else {
                        foreach (['d/m/Y', 'd-m/Y', 'Y-m-d'] as $format) {
                            try {
                                $tanggal = Carbon::createFromFormat(
                                    $format,
                                    trim($raw)
                                )->format('Y-m-d');
                                break;
                            } catch (\Exception $e) {}
                        }
                    }
                }

                // =============================
                // SIMPAN
                // =============================
                DB::transaction(function () use ($item, $qty, $tanggal, $row, $map) {

                    $keterangan = 'Import Excel';

                    if ($map['keterangan'] !== null) {
                        $ket = trim((string) ($row[$map['keterangan']] ?? ''));
                        if ($ket !== '') {
                            $keterangan = $ket;
                        }
                    }

                    ItemTransaction::create([
                        'item_id'    => $item->id,
                        'type'       => ItemTransaction::TYPE_OUT,
                        'quantity'   => $qty,
                        'price'      => $item->price,
                        'total'      => $qty * $item->price,
                        'tanggal'    => $tanggal,
                        'keterangan' => $keterangan,
                    ]);

                    $item->decrement('stock', $qty);
                });

            } catch (\Exception $e) {

                $this->failedRows[] = [
                    'row'    => $rowNumber,
                    'item'   => $row[$map['item']] ?? '-',
                    'qty'    => $row[$map['quantity']] ?? '-',
                    'reason' => $e->getMessage(),
                ];
            }
        }
    }

    private function find(array $header, array $keywords): ?int
    {
        foreach ($header as $index => $name) {
            foreach ($keywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    return $index;
                }
            }
        }
        return null;
    }
}

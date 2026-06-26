<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ItemInImport implements ToCollection
{
    protected array $failedRows = [];

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }

    public function collection(Collection $rows)
    {
        if ($rows->count() < 2) return;

        $header = array_map(
            fn ($h) => strtolower(trim((string) $h)),
            $rows->shift()->toArray()
        );

        $map = [
            'item'       => $this->find($header, ['nama item']),
            'quantity'   => $this->find($header, ['jumlah']),
            'price'      => $this->find($header, ['harga']),
            'no_po'      => $this->find($header, ['no po']),
            'tanggal'    => $this->find($header, ['tanggal']),
            'keterangan' => $this->find($header, ['keterangan']),
        ];

        if ($map['item'] === null || $map['quantity'] === null) {

            $this->failedRows[] = [
                'row'    => '-',
                'item'   => '-',
                'qty'    => '-',
                'reason' => 'Header tidak sesuai template',
            ];

            return;
        }

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2;

            try {

                $itemName = trim((string) ($row[$map['item']] ?? ''));

                if ($itemName === '') {
                    throw new \Exception('Nama item kosong');
                }

                $item = Item::whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($itemName)]
                )->lockForUpdate()->first();

                if (!$item) {
                    throw new \Exception('Item tidak ditemukan');
                }

                $qty = (int) ($row[$map['quantity']] ?? 0);

                if ($qty <= 0) {
                    throw new \Exception('Jumlah tidak valid');
                }

                $price = 0;

                if ($map['price'] !== null) {
                    $price = (int) preg_replace(
                        '/[^0-9]/',
                        '',
                        (string) ($row[$map['price']] ?? 0)
                    );
                }

                $noPo = null;

                if ($map['no_po'] !== null) {
                    $noPo = trim(
                        (string) ($row[$map['no_po']] ?? '')
                    );
                }

                $tanggal = now()->toDateString();

                if ($map['tanggal'] !== null && !empty($row[$map['tanggal']])) {

                    $raw = $row[$map['tanggal']];

                    if (is_numeric($raw)) {

                        $tanggal = Carbon::instance(
                            ExcelDate::excelToDateTimeObject($raw)
                        )->format('Y-m-d');

                    } else {

                        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {

                            try {

                                $tanggal = Carbon::createFromFormat(
                                    $format,
                                    trim($raw)
                                )->format('Y-m-d');

                                break;

                            } catch (\Exception $e) {
                            }
                        }
                    }
                }

                $keterangan = 'Import Excel';

                if ($map['keterangan'] !== null) {

                    $ket = trim(
                        (string) ($row[$map['keterangan']] ?? '')
                    );

                    if ($ket !== '') {
                        $keterangan = $ket;
                    }
                }

                DB::transaction(function () use (
                    $item,
                    $qty,
                    $price,
                    $noPo,
                    $tanggal,
                    $keterangan
                ) {

                    ItemTransaction::create([
                        'item_id'    => $item->id,
                        'user_id'    => auth()->id(),
                        'type'       => ItemTransaction::TYPE_IN,
                        'quantity'   => $qty,
                        'price'      => $price,
                        'total'      => $qty * $price,
                        'no_po'      => $noPo,
                        'tanggal'    => $tanggal,
                        'keterangan' => $keterangan,
                    ]);

                    $item->increment('stock', $qty);

                    if ($price > 0) {
                        $item->update([
                            'price' => $price
                        ]);
                    }
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
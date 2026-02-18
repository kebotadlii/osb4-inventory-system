<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class DynamicSheetImport implements ToCollection, WithTitle
{
    protected $sheetName;

    public function __construct($sheetName = null)
    {
        $this->sheetName = $sheetName;
    }

    public function title(): string
    {
        return $this->sheetName ?: 'Unknown';
    }

    public function collection(Collection $rows)
    {
        if ($rows->count() < 2) return;

        // Buat kategori berdasarkan nama sheet
        $category = Category::firstOrCreate([
            'name' => $this->sheetName
        ]);

        // ambil header
        $header = array_map('strtolower', $rows->shift()->toArray());

        // mapping otomatis
        $map = [
            'name'     => $this->find($header, ['nama','name','barang','item']),
            'quantity' => $this->find($header, ['qty','jumlah','stok','stock','quantity']),
            'price'    => $this->find($header, ['harga','price','nilai']),
        ];

        foreach ($rows as $row) {
            Item::create([
                'category_id' => $category->id,
                'item_name'   => $row[$map['name']] ?? '-',
                'quantity'    => $row[$map['quantity']] ?? 0,
                'price'       => $row[$map['price']] ?? 0,
            ]);
        }
    }

    private function find($header, $keywords)
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

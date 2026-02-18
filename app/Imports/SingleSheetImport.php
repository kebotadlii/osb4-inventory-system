<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SingleSheetImport implements ToModel, WithHeadingRow
{
    protected $sheetName;

    public function __construct($sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function model(array $row)
    {
        // Buat kategori berdasarkan nama sheet
        $category = Category::firstOrCreate([
            'name' => $this->sheetName
        ]);

        return new Item([
            'category_id' => $category->id,
            'item_name'   => $row['nama'] ?? '-',    // ambil kolom "nama"
            'quantity'    => $row['stok'] ?? 0,      // ambil kolom "stok"
            'price'       => $row['harga'] ?? 0      // ambil kolom "harga" atau isi 0 kalau tidak ada
        ]);
    }
}

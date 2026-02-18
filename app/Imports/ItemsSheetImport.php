<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ItemsSheetImport implements ToModel, WithHeadingRow, WithTitle, WithChunkReading
{
    public function model(array $row)
    {
        if (!isset($row['name']) || !isset($row['stock'])) {
            return null;
        }

        $category = Category::firstOrCreate([
            'name' => $this->title()
        ]);

        return new Item([
            'category_id' => $category->id,
            'name' => $row['name'],
            'stock' => $row['stock'],
        ]);
    }

    public function chunkSize(): int
    {
        return 500;  // PROSES 500 ROW PER SEKALI
    }
}

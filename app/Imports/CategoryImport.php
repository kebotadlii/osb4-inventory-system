<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CategoryImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            '*' => function ($sheet) {
                Category::create([
                    'name' => $sheet->getTitle()
                ]);
            }
        ];
    }
}

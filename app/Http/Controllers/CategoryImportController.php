<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoryImport;

class CategoryImportController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new CategoryImport, $request->file('file'));

        return back()->with('success', 'Import berhasil! Semua nama sheet sudah disimpan.');
    }
}

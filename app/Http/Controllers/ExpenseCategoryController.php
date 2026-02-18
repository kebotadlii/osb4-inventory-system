<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\ExcelController;

class ExpenseCategoryController extends Controller
{
    /**
     * LIST KATEGORI BIAYA
     */
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expense_categories.index', compact('categories'));
    }

    /**
     * FORM TAMBAH KATEGORI
     */
    public function create()
    {
        return view('expense_categories.create');
    }

    /**
     * SIMPAN KATEGORI
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('expense.categories.index')
            ->with('success', 'Kategori biaya berhasil ditambahkan');
    }

    /**
     * FORM IMPORT
     */
    public function showImportForm($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        return view('expense_categories.import', compact('category'));
    }

    /**
     * DOWNLOAD TEMPLATE EXCEL (SUDAH ADA NO INVOICE)
     */
    public function downloadTemplate($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        // ⚠️ HARUS SAMA URUTANNYA DENGAN IMPORT
        $headers = [
            'Tanggal (dd/mm/yyyy)',
            'Nama Item',
            'No Invoice',
            'Penyedia',
            'Qty',
            'Nominal',
        ];

        $filename = 'template_import_biaya_' . str()->slug($category->name) . '.xlsx';

        return ExcelController::exportTemplate($headers, $filename);
    }

    /**
     * PROSES IMPORT
     */
    public function import(Request $request, $id)
    {
        return app(ExcelController::class)
            ->importExpensesByCategoryProcess($request, $id);
    }

    /**
     * HAPUS KATEGORI
     */
    public function destroy($id)
    {
        ExpenseCategory::findOrFail($id)->delete();

        return redirect()
            ->route('expense.categories.index')
            ->with('success', 'Kategori biaya berhasil dihapus');
    }
}

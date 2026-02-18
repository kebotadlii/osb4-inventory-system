<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * LIST DATA PENGELUARAN
     */
    public function index(Request $request)
    {
        // QUERY UTAMA
        $query = Expense::with('category');

        // FILTER TAHUN
        if ($request->filled('year')) {
            $query->whereYear('expense_date', $request->year);
        }

        // FILTER BULAN
        if ($request->filled('month')) {
            $query->whereMonth('expense_date', $request->month);
        }

        // FILTER KATEGORI
        if ($request->filled('expense_category_id')) {
            $query->where(
                'expense_category_id',
                (int) $request->expense_category_id
            );
        }

        // TOTAL IKUT FILTER
        $totalExpense = (clone $query)->sum('amount');

        // DATA LIST + PAGINATION
        $expenses = $query
            ->orderBy('expense_date', 'desc')
            ->paginate(50)
            ->withQueryString();

        // DATA SUPPORT VIEW
        $categories = ExpenseCategory::orderBy('name')->get();

        $years = Expense::selectRaw('YEAR(expense_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('expenses.index', compact(
            'expenses',
            'categories',
            'totalExpense',
            'years'
        ));
    }

    /**
     * SIMPAN
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name'           => 'required|string|max:255',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'invoice_number'      => 'nullable|string|max:255',
            'provider'            => 'nullable|string|max:255',
            'quantity'            => 'nullable|integer|min:1',
            'expense_date'        => 'required|date',
            'amount'              => 'required|numeric|min:0',
        ]);

        Expense::create($request->only([
            'item_name',
            'expense_category_id',
            'invoice_number',
            'provider',
            'quantity',
            'expense_date',
            'amount',
        ]));

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    /**
     * FORM EDIT
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'item_name'           => 'required|string|max:255',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'invoice_number'      => 'nullable|string|max:255',
            'provider'            => 'nullable|string|max:255',
            'quantity'            => 'nullable|integer|min:1',
            'expense_date'        => 'required|date',
            'amount'              => 'required|numeric|min:0',
        ]);

        $expense->update($request->only([
            'item_name',
            'expense_category_id',
            'invoice_number',
            'provider',
            'quantity',
            'expense_date',
            'amount',
        ]));

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui');
    }

    /**
     * HAPUS
     */
    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }
}

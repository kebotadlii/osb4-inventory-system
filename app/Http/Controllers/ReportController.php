<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Item,
    Category,
    Expense,
    ExpenseCategory
};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockReportExport;
use App\Exports\ExpensesExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    /*
    |------------------------------------------------------------------
    | LAPORAN STOK & RINGKASAN DANA
    |------------------------------------------------------------------
    */
    public function stock(Request $request)
    {
        $year = $request->get('year', now()->year);

        $categoryId = $request->filled('category_id')
            ? (int) $request->category_id
            : null;

        $itemsQuery = Item::with(['category', 'transactions'])
            ->when($categoryId, fn ($q) =>
                $q->where('category_id', $categoryId)
            );

        $items = (clone $itemsQuery)
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        $totalStockQty   = 0;
        $totalStockValue = 0;

        (clone $itemsQuery)->get()->each(function ($item) use (
            &$totalStockQty,
            &$totalStockValue
        ) {
            $in  = $item->transactions->where('type', 'in');
            $out = $item->transactions->where('type', 'out');

            $stock = $in->sum('quantity') - $out->sum('quantity');

            $lastIn = $in->sortByDesc('tanggal')->first();
            $price  = $lastIn->price ?? 0;

            $totalStockQty   += $stock;
            $totalStockValue += $stock * $price;
        });

        $totalInValue = 0;

        (clone $itemsQuery)->get()->each(function ($item) use (&$totalInValue, $year) {
            $in = $item->transactions->where('type', 'in');

            if ($year !== 'all') {
                $in = $in->filter(fn ($t) =>
                    Carbon::parse($t->tanggal)->year == $year
                );
            }

            $totalInValue += $in->sum(fn ($t) => $t->quantity * $t->price);
        });

        $totalExpense = Expense::when($year !== 'all', fn ($q) =>
                $q->whereYear('expense_date', $year)
            )
            ->when($categoryId, fn ($q) =>
                $q->where('expense_category_id', $categoryId)
            )
            ->sum('amount');

        $totalDana = $totalInValue + $totalExpense;

        $items->getCollection()->transform(function ($item) use ($year) {

            $transactions = $item->transactions;

            $inYear = $transactions
                ->where('type', 'in')
                ->when($year !== 'all', fn ($q) =>
                    $q->filter(fn ($t) =>
                        Carbon::parse($t->tanggal)->year == $year
                    )
                )
                ->sum('quantity');

            $outYear = $transactions
                ->where('type', 'out')
                ->when($year !== 'all', fn ($q) =>
                    $q->filter(fn ($t) =>
                        Carbon::parse($t->tanggal)->year == $year
                    )
                )
                ->sum('quantity');

            $inAll  = $transactions->where('type', 'in');
            $outAll = $transactions->where('type', 'out');

            $stock = $inAll->sum('quantity') - $outAll->sum('quantity');

            $lastIn = $inAll->sortByDesc('tanggal')->first();
            $price  = $lastIn->price ?? 0;

            return (object) [
                'id'        => $item->id,
                'name'      => $item->name,
                'category'  => $item->category->name ?? '-',
                'stock_in'  => $inYear,
                'stock_out' => $outYear,
                'stock'     => $stock,
                'price'     => $price,
                'total'     => $stock * $price,
            ];
        });

        $categories = Category::orderBy('name')->get();

        return view('reports.stock', compact(
            'items',
            'categories',
            'year',
            'totalStockQty',
            'totalStockValue',
            'totalInValue',
            'totalExpense',
            'totalDana'
        ));
    }

    /*
    |------------------------------------------------------------------
    | LAPORAN PENGELUARAN
    |------------------------------------------------------------------
    */
    public function expenses(Request $request)
    {
        // ⬅️ BIAR MASUK LANGSUNG ADA DATA
        $year = $request->filled('year')
            ? $request->year
            : now()->year;

        $month      = $request->month;
        $categoryId = $request->category_id; // ✅ FINAL

        $query = Expense::with('category')
            ->whereYear('expense_date', $year);

        if ($month) {
            $query->whereMonth('expense_date', $month);
        }

        if ($categoryId) {
            $query->where('expense_category_id', (int) $categoryId);
        }

        $expenses = $query
            ->orderByDesc('expense_date')
            ->paginate(30)
            ->withQueryString();

        $totalExpense = (clone $query)->sum('amount');
        $categories   = ExpenseCategory::orderBy('name')->get();

        return view('reports.expenses', compact(
            'expenses',
            'categories',
            'totalExpense',
            'year',
            'month',
            'categoryId'
        ));
    }

    /*
    |------------------------------------------------------------------
    | EXPORT LAPORAN PENGELUARAN
    |------------------------------------------------------------------
    */
    public function exportExpenses(Request $request)
    {
        $year  = $request->year;
        $month = $request->month;

        if ($year && $month) {
            $filename = "laporan_pengeluaran_{$year}_{$month}.xlsx";
        } elseif ($year) {
            $filename = "laporan_pengeluaran_{$year}.xlsx";
        } else {
            $filename = "laporan_pengeluaran_semua.xlsx";
        }

        return Excel::download(
            new ExpensesExport($request),
            $filename
        );
    }

    /*
    |------------------------------------------------------------------
    | EXPORT LAPORAN STOK
    |------------------------------------------------------------------
    */
    public function exportStock(Request $request)
    {
        $categoryId = $request->filled('category_id')
            ? (int) $request->category_id
            : null;

        return Excel::download(
            new StockReportExport($categoryId),
            'laporan_stok_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}

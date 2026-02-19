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
    |--------------------------------------------------------------------------
    | LAPORAN STOK
    |--------------------------------------------------------------------------
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
            'totalInValue'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | LAPORAN PENGELUARAN
    |--------------------------------------------------------------------------
    */
    public function expenses(Request $request)
    {
        $year = $request->filled('year')
            ? $request->year
            : now()->year;

        $month      = $request->month;
        $categoryId = $request->category_id;

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
            ->paginate(50)
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

    public function exportExpenses(Request $request)
    {
        return Excel::download(
            new ExpensesExport($request),
            'laporan_pengeluaran.xlsx'
        );
    }

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

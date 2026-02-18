<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;
use App\Models\Category;

class TransactionController extends Controller
{
    /**
     * Ambil bulan & tahun (default: sekarang)
     */
    private function getMonthYear(Request $request)
    {
        return [
            'month' => (int) ($request->month ?? now()->month),
            'year'  => (int) ($request->year  ?? now()->year),
        ];
    }

    // =========================
    // HISTORY PEMBELIAN (IN)
    // =========================
    public function pembelian(Request $request)
    {
        ['month' => $month, 'year' => $year] = $this->getMonthYear($request);

        $query = ItemTransaction::with('item.category')
            ->where('type', 'in')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year);

        // FILTER KATEGORI
        if ($request->filled('category_id')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $transactions = $query
            ->orderBy('tanggal', 'desc')
            ->paginate(25)
            ->withQueryString(); // ⬅️ biar filter tetap saat pindah page

        $categories = Category::orderBy('name')->get();

        return view('history.in', compact(
            'transactions',
            'month',
            'year',
            'categories'
        ));
    }

    // =========================
    // HISTORY PEMAKAIAN (OUT)
    // =========================
    public function pemakaian(Request $request)
    {
        ['month' => $month, 'year' => $year] = $this->getMonthYear($request);

        $query = ItemTransaction::with('item.category')
            ->where('type', 'out')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year);

        // FILTER KATEGORI
        if ($request->filled('category_id')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $transactions = $query
            ->orderBy('tanggal', 'desc')
            ->paginate(25)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('history.out', compact(
            'transactions',
            'month',
            'year',
            'categories'
        ));
    }
}

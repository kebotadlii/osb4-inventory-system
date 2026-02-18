<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |==================================================
        | MASTER DATA
        |==================================================
        */
        $totalCategories = Category::count();
        $totalItems      = Item::count();

        /*
        |==================================================
        | TOTAL STOK (DARI items.stock)
        |==================================================
        */
        $totalStock = Item::sum('stock');

        /*
        |==================================================
        | STOK HABIS & KRITIS
        |==================================================
        */
        $stockHabis = Item::where('stock', '<=', 0)->count();

        $stockKritis = Item::where('stock', '>', 0)
            ->where('stock', '<', 10)
            ->count();

        /*
        |==================================================
        | TOTAL TRANSAKSI MASUK & KELUAR
        |==================================================
        */
        $totalIn = ItemTransaction::where('type', 'in')
            ->sum('quantity');

        $totalOut = ItemTransaction::where('type', 'out')
            ->sum('quantity');

        /*
        |==================================================
        | TRANSAKSI TERAKHIR (IN + OUT)
        |==================================================
        */
        $latestTransactions = ItemTransaction::with(['item', 'item.category'])
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get();

        /*
        |==================================================
        | RETURN VIEW
        |==================================================
        */
        return view('dashboard', compact(
            'totalCategories',
            'totalItems',
            'totalStock',
            'stockHabis',
            'stockKritis',
            'totalIn',
            'totalOut',
            'latestTransactions'
        ));
    }
}

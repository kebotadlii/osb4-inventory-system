<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;
use App\Models\Category;
use App\Exports\HistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    /**
     * ===============================
     * HALAMAN HISTORY
     * ===============================
     */
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        $transactions = $query
            ->paginate(50)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('history.index', compact('transactions', 'categories'));
    }

    /**
     * ===============================
     * EXPORT XLS (IKUT FILTER)
     * ===============================
     */
    public function export(Request $request)
    {
        // 🔒 WAJIB PILIH JENIS TRANSAKSI
        if (!$request->filled('type')) {
            return redirect()
                ->back()
                ->with('error', 'Pilih jenis transaksi (Masuk / Keluar) sebelum export.');
        }

        $typeLabel = $request->type === 'in' ? 'masuk' : 'keluar';

        $filename = 'history-transaksi-' . $typeLabel . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        /**
         * 🔥 PENTING: BERSIHKAN OUTPUT BUFFER
         * (INI YANG MENCEGAH FILE CORRUPT)
         */
        if (ob_get_length()) {
            ob_end_clean();
        }
        ob_start();

        return Excel::download(
            new HistoryExport($request),
            $filename
        );
    }

    /**
     * ===============================
     * QUERY UTAMA (SATU SUMBER)
     * ===============================
     */
    private function baseQuery(Request $request)
    {
        $query = ItemTransaction::with(['item.category'])
            ->orderBy('tanggal', 'desc');

        /**
         * FILTER BULAN & TAHUN
         * DEFAULT: BULAN & TAHUN SEKARANG
         */
        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year ?? now()->year);

        $query->whereMonth('tanggal', $month)
              ->whereYear('tanggal', $year);

        /**
         * FILTER JENIS TRANSAKSI
         */
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        /**
         * FILTER KATEGORI
         */
        if ($request->filled('category_id')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        return $query;
    }
}

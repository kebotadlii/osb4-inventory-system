<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ItemTransactionController extends Controller
{
    // =========================
    // TRANSAKSI MASUK
    // =========================
    public function createIn()
    {
        $items = Item::orderBy('name')->get();

        $transactions = ItemTransaction::with('item')
            ->where('type', ItemTransaction::TYPE_IN)
            ->orderBy('tanggal', 'desc')
            ->paginate(100)
            ->withQueryString();

        return view('transactions.in', compact('items', 'transactions'));
    }

    public function storeIn(Request $request)
    {
        $request->validate([
            'no_po'    => 'required|string|max:50',
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'price'    => 'required|integer|min:0',
            'tanggal'  => 'required',
        ]);

        $tanggal = $this->parseTanggal($request->tanggal);

        DB::transaction(function () use ($request, $tanggal) {

            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            ItemTransaction::create([
                'item_id'    => $item->id,
                'type'       => ItemTransaction::TYPE_IN,
                'no_po'      => $request->no_po,
                'quantity'   => $request->quantity,
                'price'      => $request->price,
                'total'      => $request->quantity * $request->price,
                'tanggal'    => $tanggal,
                'keterangan' => $request->keterangan ?? 'Barang masuk',
            ]);

            $item->increment('stock', $request->quantity);

            if ($request->price > 0) {
                $item->update(['price' => $request->price]);
            }
        });

        return redirect()
            ->route('transactions.in.form')
            ->with('success', 'Barang masuk berhasil dicatat');
    }

    // =========================
    // TRANSAKSI KELUAR
    // =========================
    public function createOut()
    {
        $items = Item::orderBy('name')->get();

        $transactions = ItemTransaction::with('item')
            ->where('type', ItemTransaction::TYPE_OUT)
            ->orderBy('tanggal', 'desc')
            ->paginate(100)
            ->withQueryString();

        return view('transactions.out', compact('items', 'transactions'));
    }

    public function storeOut(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'tanggal'  => 'required',
        ]);

        $tanggal = $this->parseTanggal($request->tanggal);

        DB::transaction(function () use ($request, $tanggal) {

            $item = Item::lockForUpdate()->findOrFail($request->item_id);

            if ($request->quantity > $item->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Sisa stok: ' . $item->stock,
                ]);
            }

            ItemTransaction::create([
                'item_id'    => $item->id,
                'type'       => ItemTransaction::TYPE_OUT,
                'quantity'   => $request->quantity,
                'price'      => $item->price,
                'total'      => $request->quantity * $item->price,
                'tanggal'    => $tanggal,
                'keterangan' => $request->keterangan ?? 'Barang keluar',
            ]);

            $item->decrement('stock', $request->quantity);
        });

        return redirect()
            ->route('transactions.out.form')
            ->with('success', 'Barang keluar berhasil dicatat');
    }

    // =========================
    // PARSING TANGGAL FLEXIBLE (MANUAL + EXCEL)
    // =========================
    private function parseTanggal($value)
    {
        if (!$value) {
            return null;
        }

        // Jika numeric (dari Excel)
        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            )->format('Y-m-d');
        }

        $value = trim($value);

        // Coba beberapa format umum
        $formats = [
            'Y-m-d',           // input type="date"
            'd/m/Y',
            'd-m-Y',
            'd/m/Y H:i:s',
            'Y-m-d H:i:s',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                // lanjut coba format lain
            }
        }

        // fallback terakhir (auto detect)
        return Carbon::parse($value)->format('Y-m-d');
    }
}

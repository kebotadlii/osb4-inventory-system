<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemTransaction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

// TAMBAHAN
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemImportTemplateExport;

class ItemController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ITEMS PER KATEGORI
    |--------------------------------------------------------------------------
    */
    public function byCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $items = Item::with('transactions')
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->paginate(50);

        return view('items.index', compact('category', 'items'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM IMPORT ITEM
    |--------------------------------------------------------------------------
    */
    public function importForm($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('items.import', compact('category'));
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES IMPORT ITEM
    |--------------------------------------------------------------------------
    */
    public function import(Request $request, $categoryId)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $category = Category::findOrFail($categoryId);

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        // hapus header
        unset($rows[0]);

        $totalImport = 0;

        DB::transaction(function () use ($rows, $category, &$totalImport) {

            foreach ($rows as $row) {

                /*
                 | FORMAT EXCEL:
                 | 0 = Nama Item
                 | 1 = Jumlah
                 | 2 = Harga
                 | 3 = No PO
                 | 4 = Tanggal
                 | 5 = Keterangan
                 */

                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }

                $item = Item::where('category_id', $category->id)
                    ->where('name', trim($row[0]))
                    ->first();

                if ($item) {
                    $item->increment('stock', (int) $row[1]);
                } else {
                    $item = Item::create([
                        'category_id' => $category->id,
                        'name'        => trim($row[0]),
                        'stock'       => (int) $row[1],
                        'price'       => (int) ($row[2] ?? 0),
                    ]);
                }

                // parsing tanggal aman
                $tanggal = null;

                if (!empty($row[4])) {
                    try {
                        if (is_numeric($row[4])) {
                            $tanggal = Carbon::instance(
                                ExcelDate::excelToDateTimeObject($row[4])
                            );
                        } else {
                            foreach (['d/m/Y', 'Y-m-d', 'm/d/Y'] as $format) {
                                try {
                                    $tanggal = Carbon::createFromFormat($format, trim($row[4]));
                                    break;
                                } catch (\Exception $e) {}
                            }
                        }
                    } catch (\Exception $e) {
                        $tanggal = null;
                    }
                }

                ItemTransaction::create([
                    'item_id'    => $item->id,
                    'type'       => ItemTransaction::TYPE_IN,
                    'quantity'   => (int) $row[1],
                    'price'      => (int) ($row[2] ?? 0),
                    'no_po'      => $row[3] ?? null,
                    'tanggal'    => $tanggal,
                    'keterangan' => $row[5] ?? 'Import Excel',
                ]);

                $totalImport++;
            }
        });

        return redirect()
            ->route('categories.items', $categoryId)
            ->with('success', "Import berhasil: {$totalImport} data diproses.");
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD TEMPLATE (FIX – TANPA FILE PUBLIC)
    |--------------------------------------------------------------------------
    */
    public function downloadTemplate($categoryId)
    {
        Category::findOrFail($categoryId); // validasi aja

        return Excel::download(
            new ItemImportTemplateExport(),
            'template_import_items.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ITEMS GLOBAL & CRUD (TIDAK DIUBAH)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $totalItems = Item::count();
        $stokHabis  = Item::where('stock', '<=', 0)->count();
        $stokKritis = Item::whereBetween('stock', [1, 9])->count();

        $query = Item::with(['category', 'transactions']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filter === 'habis') {
            $query->where('stock', '<=', 0);
        }

        if ($request->filter === 'kritis') {
            $query->whereBetween('stock', [1, 9]);
        }

        $items = $query->orderBy('name')->paginate(25)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('items_all', compact(
            'items',
            'categories',
            'totalItems',
            'stokHabis',
            'stokKritis'
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'nullable|numeric|min:0',
        ]);

        Item::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'price'       => $request->price ?? 0,
        ]);

        return redirect()->route('items.all')
            ->with('success', 'Item berhasil ditambahkan.');
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        Item::findOrFail($id)->update($request->only('name', 'price'));
        return back()->with('success', 'Item berhasil diperbarui.');
    }

    public function deleteItem($id)
    {
        Item::findOrFail($id)->delete();
        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function history($id)
    {
        $item = Item::with('category')->findOrFail($id);

        $transactions = ItemTransaction::with('item.category')
            ->where('item_id', $id)
            ->orderBy('tanggal', 'desc')
            ->paginate(50);

        return view('items.history', compact('item', 'transactions'));
    }
}

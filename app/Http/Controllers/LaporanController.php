<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;

class LaporanController extends Controller
{
    public function index()
    {
        $data = Laporan::all();
        return view('laporan.index', compact('data'));
    }

    public function showImport()
    {
        return view('laporan.import');
    }

    public function updateField($id, Request $request)
    {
        $laporan = Laporan::findOrFail($id);

        $field = $request->field;
        $value = $request->value;

        $laporan->$field = $value;
        $laporan->save();

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = [
        'nama_barang',
        'tanggal',
        'jumlah',
        'no_po',
        'harga'
    ];
}

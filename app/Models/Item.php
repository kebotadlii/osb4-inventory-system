<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
    ];

    // Pastikan tipe data konsisten
    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
    ];

    // HANYA status, stock jangan di-append
    protected $appends = ['stock_status'];

    // =============================
    // RELATION
    // =============================
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class);
    }

    // =============================
    // HELPER STOCK (AMAN DIPAKAI)
    // =============================
    public function hasEnoughStock(int $qty): bool
    {
        return $this->stock >= $qty;
    }

    // =============================
    // STATUS STOK (DASHBOARD & TABEL)
    // =============================
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'empty';   // merah
        }

        if ($this->stock < 10) {
            return 'low';     // kuning
        }

        return 'safe';        // hijau
    }
}

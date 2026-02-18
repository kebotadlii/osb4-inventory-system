<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'invoice_number',
        'item_name',
        'expense_category_id',
        'provider',
        'quantity',
        'expense_date',
        'amount',
        'notes', // ✅ WAJIB (INI YANG HILANG)
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'integer', // lebih aman untuk rupiah
        'quantity'     => 'integer',
    ];

    /**
     * Relasi ke kategori biaya
     */
    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }
}

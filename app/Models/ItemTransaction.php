<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;

    public const TYPE_IN  = 'in';
    public const TYPE_OUT = 'out';

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'price',
        'total',
        'no_po',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'integer',
        'total'    => 'integer',
        'tanggal'  => 'datetime', // FIX & KONSISTEN
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL EVENT
    |--------------------------------------------------------------------------
    | Auto hitung total agar data konsisten
    */
    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->total =
                ($transaction->quantity ?? 0) * ($transaction->price ?? 0);
        });

        static::updating(function ($transaction) {
            $transaction->total =
                ($transaction->quantity ?? 0) * ($transaction->price ?? 0);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */
    public function isIn(): bool
    {
        return $this->type === self::TYPE_IN;
    }

    public function isOut(): bool
    {
        return $this->type === self::TYPE_OUT;
    }
}

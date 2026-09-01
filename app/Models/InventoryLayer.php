<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLayer extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'tanggal_masuk',
        'qty_masuk',
        'qty_sisa',
        'harga_per_unit',
        'referensi_type',
        'referensi_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
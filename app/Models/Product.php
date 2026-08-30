<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok_gudang',
        'stok_konsinyasi',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }
}
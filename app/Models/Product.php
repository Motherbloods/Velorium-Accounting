<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use Auditable;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok_gudang',
        'stok_konsinyasi',
        'metode_penilaian',
        'harga_rata_rata',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'nomor_transaksi',
        'customer_id',
        'tanggal',
        'tipe',
        'subtotal',
        'diskon_dagang',
        'dpp_ppn',
        'ppn',
        'total',
        'termin_diskon_persen',
        'termin_diskon_hari',
        'termin_jatuh_tempo_hari',
        'coa_pembayaran_id',
        'journal_entry_id',
        'receivable_id',
        'branch_id',
        'warehouse_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }
}
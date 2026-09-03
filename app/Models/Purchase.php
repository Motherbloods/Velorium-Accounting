<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use Auditable;

    protected $fillable = [
        'nomor_transaksi',
        'supplier_id',
        'tanggal',
        'tipe',
        'subtotal',
        'diskon_dagang',
        'dpp_ppn',
        'ppn',
        'total',
        'termin_diskon_persen',
        'termin_diskon_hari',
        'coa_pembayaran_id',
        'journal_entry_id',
        'payable_id',
        'branch_id',
        'warehouse_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }
}
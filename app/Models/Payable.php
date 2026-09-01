<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payable extends Model
{
    protected $fillable = [
        'nomor_hutang',
        'supplier_id',
        'tanggal',
        'tanggal_jatuh_tempo',
        'jenis',
        'tarif_bunga_tahunan',
        'total_hutang',
        'sisa_hutang',
        'status',
        'referensi_type',
        'referensi_id',
        'journal_entry_id',
        'branch_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PayablePayment::class);
    }

    public function klasifikasi(): string
    {
        $sisaHari = now()->startOfDay()->diffInDays($this->tanggal_jatuh_tempo->copy()->startOfDay(), false);

        return $sisaHari <= 365 ? 'jangka_pendek' : 'jangka_panjang';
    }
}
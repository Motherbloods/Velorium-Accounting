<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receivable extends Model
{
    protected $fillable = [
        'nomor_invoice',
        'customer_id',
        'tanggal',
        'tanggal_jatuh_tempo',
        'total_tagihan',
        'sisa_piutang',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    public function umurHari(): int
    {
        return max(0, now()->startOfDay()->diffInDays($this->tanggal_jatuh_tempo->startOfDay(), false) * -1);
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'lunas' && $this->tanggal_jatuh_tempo->isPast();
    }
}
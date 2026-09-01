<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivablePayment extends Model
{
    protected $fillable = [
        'receivable_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'coa_kas_bank_id',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
        ];
    }

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }

    public function coaKasBank(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_kas_bank_id');
    }
}
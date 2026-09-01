<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayablePayment extends Model
{
    protected $fillable = [
        'payable_id',
        'tanggal_bayar',
        'jumlah_pokok',
        'jumlah_bunga',
        'coa_kas_bank_id',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
        ];
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class);
    }

    public function coaKasBank(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_kas_bank_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankReconciliationItem extends Model
{
    protected $fillable = [
        'bank_reconciliation_id',
        'kategori',
        'jenis',
        'keterangan',
        'jumlah',
        'sudah_diposting',
        'cash_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'sudah_diposting' => 'boolean',
        ];
    }

    public function bankReconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class);
    }

    public function cashTransaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class);
    }
}
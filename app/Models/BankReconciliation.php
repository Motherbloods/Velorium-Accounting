<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankReconciliation extends Model
{
    protected $fillable = [
        'bank_account_id',
        'periode',
        'saldo_buku',
        'saldo_rekening_koran',
        'saldo_disesuaikan_buku',
        'saldo_disesuaikan_bank',
        'status',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'periode' => 'date',
        ];
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BankReconciliationItem::class);
    }

    public function isValid(): bool
    {
        return $this->saldo_disesuaikan_buku !== null
            && $this->saldo_disesuaikan_bank !== null
            && bccomp((string) $this->saldo_disesuaikan_buku, (string) $this->saldo_disesuaikan_bank, 2) === 0;
    }
}
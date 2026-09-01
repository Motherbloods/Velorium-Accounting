<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    protected $fillable = [
        'nomor_bukti',
        'tanggal',
        'tipe',
        'coa_kas_bank_id',
        'coa_lawan_id',
        'jumlah',
        'keterangan',
        'referensi_type',
        'referensi_id',
        'journal_entry_id',
        'branch_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function coaKasBank(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_kas_bank_id');
    }

    public function coaLawan(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_lawan_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
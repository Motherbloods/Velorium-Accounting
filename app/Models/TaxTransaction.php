<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxTransaction extends Model
{
    protected $fillable = [
        'referensi_type',
        'referensi_id',
        'tipe',
        'dpp',
        'tarif_persen',
        'jumlah_pajak',
        'periode_pajak',
        'journal_entry_id',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
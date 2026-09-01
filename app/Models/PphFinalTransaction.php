<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PphFinalTransaction extends Model
{
    protected $fillable = [
        'periode_pajak',
        'omzet_bruto',
        'tarif_persen',
        'jumlah_pajak',
        'status',
        'journal_entry_pengakuan_id',
        'journal_entry_penyetoran_id',
    ];

    public function journalEntryPengakuan(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_pengakuan_id');
    }

    public function journalEntryPenyetoran(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_penyetoran_id');
    }
}
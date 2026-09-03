<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdjustingEntry extends Model
{
    protected $fillable = [
        'tipe',
        'referensi_id',
        'periode',
        'jumlah',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'periode' => 'date',
        ];
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
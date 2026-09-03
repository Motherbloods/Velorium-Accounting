<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingPeriod extends Model
{
    protected $fillable = [
        'fiscal_period_id',
        'laba_rugi_bersih',
        'closing_journal_entry_id',
        'reversing_journal_entry_id',
        'closed_at',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function closingJournalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'closing_journal_entry_id');
    }

    public function reversingJournalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversing_journal_entry_id');
    }
}
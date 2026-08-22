<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_POSTED = 'posted';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'nomor_bukti',
        'tanggal',
        'keterangan',
        'referensi_type',
        'referensi_id',
        'fiscal_period_id',
        'created_by',
        'status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'catatan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalDebit(): string
    {
        return (string) $this->details()->sum('debit');
    }

    public function totalKredit(): string
    {
        return (string) $this->details()->sum('kredit');
    }

    public function isBalanced(): bool
    {
        return bccomp($this->totalDebit(), $this->totalKredit(), 2) === 0;
    }
}
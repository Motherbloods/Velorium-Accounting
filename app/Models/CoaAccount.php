<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CoaAccount extends Model
{
    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'level',
        'parent_id',
        'tipe_akun',
        'saldo_normal',
        'is_postable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_postable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CoaAccount::class, 'parent_id');
    }

    public function journalDetails(): HasMany
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function scopePostable(Builder $query): Builder
    {
        return $query->where('is_postable', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
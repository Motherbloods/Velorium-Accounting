<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepreciationSchedule extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'periode',
        'beban_penyusutan',
        'akumulasi_penyusutan_setelah',
        'nilai_buku_setelah',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'periode' => 'date',
        ];
    }

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
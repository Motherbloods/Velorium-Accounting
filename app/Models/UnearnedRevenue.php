<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnearnedRevenue extends Model
{
    protected $fillable = [
        'nama',
        'coa_kewajiban_id',
        'coa_pendapatan_id',
        'tanggal_terima',
        'total_diterima',
        'jumlah_bulan_cakupan',
        'sisa_belum_diakui',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terima' => 'date',
        ];
    }

    public function coaKewajiban(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_kewajiban_id');
    }

    public function coaPendapatan(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_pendapatan_id');
    }

    public function alokasiBulanan(): string
    {
        return bcdiv((string) $this->total_diterima, (string) $this->jumlah_bulan_cakupan, 2);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrepaidExpense extends Model
{
    protected $fillable = [
        'nama',
        'coa_aset_id',
        'coa_beban_id',
        'tanggal_bayar',
        'total_dibayar',
        'jumlah_bulan_cakupan',
        'sisa_belum_diakui',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
        ];
    }

    public function coaAset(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_aset_id');
    }

    public function coaBeban(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_beban_id');
    }

    public function alokasiBulanan(): string
    {
        return bcdiv((string) $this->total_dibayar, (string) $this->jumlah_bulan_cakupan, 2);
    }
}
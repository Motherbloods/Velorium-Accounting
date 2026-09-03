<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedAsset extends Model
{
    use Auditable;

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'coa_aset_id',
        'coa_akumulasi_penyusutan_id',
        'tanggal_perolehan',
        'harga_perolehan',
        'nilai_residu',
        'umur_manfaat_tahun',
        'umur_manfaat_fiskal_tahun',
        'metode_penyusutan',
        'akumulasi_penyusutan',
        'nilai_buku',
        'status',
        'tanggal_pelepasan',
        'harga_jual_pelepasan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
            'tanggal_pelepasan' => 'date',
        ];
    }

    public function coaAset(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_aset_id');
    }

    public function coaAkumulasiPenyusutan(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'coa_akumulasi_penyusutan_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DepreciationSchedule::class);
    }
}
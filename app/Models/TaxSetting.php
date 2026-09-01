<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $fillable = [
        'nama_pajak',
        'tarif_persen',
        'berlaku_sejak',
    ];

    protected function casts(): array
    {
        return [
            'berlaku_sejak' => 'date',
        ];
    }

    public static function tarifBerlaku(string $namaPajak, ?string $tanggal = null): ?self
    {
        $tanggal = $tanggal ?? now()->toDateString();

        return static::where('nama_pajak', $namaPajak)
            ->where('berlaku_sejak', '<=', $tanggal)
            ->orderByDesc('berlaku_sejak')
            ->first();
    }
}
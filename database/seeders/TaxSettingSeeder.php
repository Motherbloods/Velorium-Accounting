<?php

namespace Database\Seeders;

use App\Models\TaxSetting;
use Illuminate\Database\Seeder;

class TaxSettingSeeder extends Seeder
{
    public function run(): void
    {
        TaxSetting::create([
            'nama_pajak' => 'PPN',
            'tarif_persen' => 11,
            'berlaku_sejak' => '2022-04-01',
        ]);

        TaxSetting::create([
            'nama_pajak' => 'PPh Final UMKM',
            'tarif_persen' => 0.5,
            'berlaku_sejak' => '2018-07-01',
        ]);
    }
}
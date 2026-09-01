<?php

namespace App\Services;

class DiscountService
{
    public function hargaSetelahDiskonDagang(string $harga, string $persenDiskon): string
    {
        $faktor = bcsub('1', bcdiv($persenDiskon, '100', 6), 6);

        return bcmul($harga, $faktor, 2);
    }

    public function jumlahDiskonTunai(string $totalTagihan, string $persenDiskonTunai): string
    {
        return bcmul($totalTagihan, bcdiv($persenDiskonTunai, '100', 6), 2);
    }

    public function isDalamMasaTermin(string $tanggalTransaksi, string $tanggalBayar, ?int $terminHari): bool
    {
        if (!$terminHari) {
            return false;
        }

        $batasAkhir = date('Y-m-d', strtotime($tanggalTransaksi . " +{$terminHari} days"));

        return $tanggalBayar <= $batasAkhir;
    }
}
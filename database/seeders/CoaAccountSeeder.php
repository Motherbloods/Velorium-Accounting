<?php

namespace Database\Seeders;

use App\Models\CoaAccount;
use Illuminate\Database\Seeder;

class CoaAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['1', 'ASET', 'aset', 'debit', false],
            ['11', 'Aset Lancar', 'aset', 'debit', false],
            ['111', 'Kas', 'aset', 'debit', true],
            ['112', 'Bank', 'aset', 'debit', true],
            ['113', 'Piutang Usaha', 'aset', 'debit', true],
            ['114', 'Cadangan Kerugian Piutang', 'aset', 'kredit', true],
            ['115', 'Persediaan Barang Dagang', 'aset', 'debit', true],
            ['116', 'Persediaan Barang Konsinyasi', 'aset', 'debit', true],
            ['117', 'PPN Masukan', 'aset', 'debit', true],
            ['118', 'Beban Dibayar Dimuka', 'aset', 'debit', true],
            ['12', 'Aset Tetap', 'aset', 'debit', false],
            ['121', 'Peralatan', 'aset', 'debit', true],
            ['122', 'Akumulasi Penyusutan Peralatan', 'aset', 'kredit', true],
            ['123', 'Kendaraan', 'aset', 'debit', true],
            ['124', 'Akumulasi Penyusutan Kendaraan', 'aset', 'kredit', true],
            ['125', 'Bangunan', 'aset', 'debit', true],
            ['126', 'Akumulasi Penyusutan Bangunan', 'aset', 'kredit', true],

            ['2', 'KEWAJIBAN', 'kewajiban', 'kredit', false],
            ['21', 'Kewajiban Jangka Pendek', 'kewajiban', 'kredit', false],
            ['211', 'Hutang Usaha', 'kewajiban', 'kredit', true],
            ['212', 'Hutang Komisi Konsinyasi', 'kewajiban', 'kredit', true],
            ['213', 'Beban Yang Masih Harus Dibayar', 'kewajiban', 'kredit', true],
            ['214', 'Hutang Bank Jangka Pendek', 'kewajiban', 'kredit', true],
            ['215', 'Pendapatan Diterima Dimuka', 'kewajiban', 'kredit', true],
            ['216', 'PPN Keluaran', 'kewajiban', 'kredit', true],
            ['217', 'Hutang PPh Final', 'kewajiban', 'kredit', true],
            ['22', 'Kewajiban Jangka Panjang', 'kewajiban', 'kredit', false],
            ['221', 'Hutang Bank Jangka Panjang', 'kewajiban', 'kredit', true],

            ['3', 'EKUITAS', 'modal', 'kredit', false],
            ['31', 'Modal Pemilik', 'modal', 'kredit', true],
            ['32', 'Prive / Penarikan Modal', 'modal', 'debit', true],
            ['33', 'Laba Ditahan', 'modal', 'kredit', true],
            ['34', 'Ikhtisar Laba Rugi', 'modal', 'kredit', true],

            ['4', 'PENDAPATAN', 'pendapatan', 'kredit', false],
            ['41', 'Pendapatan Penjualan', 'pendapatan', 'kredit', true],
            ['411', 'Retur Penjualan', 'pendapatan', 'debit', true],
            ['412', 'Potongan Penjualan', 'pendapatan', 'debit', true],
            ['42', 'Pendapatan Penjualan Konsinyasi', 'pendapatan', 'kredit', true],
            ['43', 'Pendapatan Lain-lain', 'pendapatan', 'kredit', true],
            ['431', 'Laba Pelepasan Aset Tetap', 'pendapatan', 'kredit', true],

            ['5', 'BEBAN', 'beban', 'debit', false],
            ['51', 'Beban Pokok Penjualan', 'beban', 'debit', true],
            ['511', 'Potongan Pembelian', 'beban', 'kredit', true],
            ['52', 'Beban Komisi Konsinyasi', 'beban', 'debit', true],
            ['53', 'Beban Gaji', 'beban', 'debit', true],
            ['54', 'Beban Penyusutan', 'beban', 'debit', true],
            ['55', 'Beban Kerugian Piutang', 'beban', 'debit', true],
            ['56', 'Beban Bunga', 'beban', 'debit', true],
            ['57', 'Beban Operasional Lainnya', 'beban', 'debit', true],
            ['58', 'Beban Pajak Penghasilan', 'beban', 'debit', true],
            ['59', 'Rugi Pelepasan Aset Tetap', 'beban', 'debit', true],
        ];

        $idsByKode = [];

        foreach ($accounts as [$kode, $nama, $tipe, $saldoNormal, $postable]) {
            $level = strlen($kode);
            $parentKode = $level > 1 ? substr($kode, 0, $level - 1) : null;

            $account = CoaAccount::create([
                'kode_akun' => $kode,
                'nama_akun' => $nama,
                'level' => $level,
                'parent_id' => $parentKode ? ($idsByKode[$parentKode] ?? null) : null,
                'tipe_akun' => $tipe,
                'saldo_normal' => $saldoNormal,
                'is_postable' => $postable,
                'is_active' => true,
            ]);

            $idsByKode[$kode] = $account->id;
        }
    }
}
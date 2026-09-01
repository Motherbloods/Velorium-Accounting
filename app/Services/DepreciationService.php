<?php

namespace App\Services;

use App\Models\DepreciationSchedule;
use App\Models\FixedAsset;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function hitungBebanBulanan(FixedAsset $asset): string
    {
        if ($asset->metode_penyusutan === 'garis_lurus') {
            return $this->hitungGarisLurusBulanan($asset);
        }

        return $this->hitungSaldoMenurunGandaBulanan($asset);
    }

    protected function hitungGarisLurusBulanan(FixedAsset $asset): string
    {
        $dasarPenyusutan = bcsub((string) $asset->harga_perolehan, (string) $asset->nilai_residu, 2);
        $bebanTahunan = bcdiv($dasarPenyusutan, (string) $asset->umur_manfaat_tahun, 2);
        $bebanBulanan = bcdiv($bebanTahunan, '12', 2);

        return $this->batasiSampaiNilaiResidu($asset, $bebanBulanan);
    }

    protected function hitungSaldoMenurunGandaBulanan(FixedAsset $asset): string
    {
        $tarifTahunan = bcmul('2', bcdiv('1', (string) $asset->umur_manfaat_tahun, 6), 6);
        $tarifBulanan = bcdiv($tarifTahunan, '12', 6);

        $nilaiBuku = bcsub((string) $asset->harga_perolehan, (string) $asset->akumulasi_penyusutan, 2);
        $bebanBulanan = bcmul($nilaiBuku, $tarifBulanan, 2);

        return $this->batasiSampaiNilaiResidu($asset, $bebanBulanan);
    }

    protected function batasiSampaiNilaiResidu(FixedAsset $asset, string $bebanUsulan): string
    {
        $nilaiBukuSaatIni = bcsub((string) $asset->harga_perolehan, (string) $asset->akumulasi_penyusutan, 2);
        $maksimalBeban = bcsub($nilaiBukuSaatIni, (string) $asset->nilai_residu, 2);

        if (bccomp($maksimalBeban, '0', 2) <= 0) {
            return '0';
        }

        return bccomp($bebanUsulan, $maksimalBeban, 2) > 0 ? $maksimalBeban : $bebanUsulan;
    }

    public function runForAsset(FixedAsset $asset, string $periode, User $user): ?DepreciationSchedule
    {
        $periodeAwalBulan = date('Y-m-01', strtotime($periode));

        if (DepreciationSchedule::where('fixed_asset_id', $asset->id)->where('periode', $periodeAwalBulan)->exists()) {
            return null;
        }

        $bebanPenyusutan = $this->hitungBebanBulanan($asset);

        if (bccomp($bebanPenyusutan, '0', 2) <= 0) {
            return null;
        }

        return DB::transaction(function () use ($asset, $periodeAwalBulan, $bebanPenyusutan, $user) {
            $coaBeban = \App\Models\CoaAccount::where('kode_akun', '54')->firstOrFail();

            $entry = $this->journalService->create([
                'tanggal' => $periodeAwalBulan,
                'keterangan' => "Beban penyusutan {$asset->nama_aset} periode " . date('M Y', strtotime($periodeAwalBulan)),
                'referensi_type' => FixedAsset::class,
                'referensi_id' => $asset->id,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaBeban->id, 'debit' => $bebanPenyusutan, 'kredit' => 0],
                ['coa_account_id' => $asset->coa_akumulasi_penyusutan_id, 'debit' => 0, 'kredit' => $bebanPenyusutan],
            ]);

            $akumulasiBaru = bcadd((string) $asset->akumulasi_penyusutan, $bebanPenyusutan, 2);
            $nilaiBukuBaru = bcsub((string) $asset->harga_perolehan, $akumulasiBaru, 2);

            $asset->update([
                'akumulasi_penyusutan' => $akumulasiBaru,
                'nilai_buku' => $nilaiBukuBaru,
            ]);

            return DepreciationSchedule::create([
                'fixed_asset_id' => $asset->id,
                'periode' => $periodeAwalBulan,
                'beban_penyusutan' => $bebanPenyusutan,
                'akumulasi_penyusutan_setelah' => $akumulasiBaru,
                'nilai_buku_setelah' => $nilaiBukuBaru,
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function runMonthly(string $periode, User $user): array
    {
        $assets = FixedAsset::where('status', 'aktif')->get();
        $results = [];

        foreach ($assets as $asset) {
            $schedule = $this->runForAsset($asset, $periode, $user);

            if ($schedule) {
                $results[] = $schedule;
            }
        }

        return $results;
    }

    public function dispose(FixedAsset $asset, string $tanggalPelepasan, string $hargaJual, User $user): void
    {
        DB::transaction(function () use ($asset, $tanggalPelepasan, $hargaJual, $user) {
            $nilaiBuku = (string) $asset->nilai_buku;
            $selisih = bcsub($hargaJual, $nilaiBuku, 2);

            $coaKasBank = \App\Models\CoaAccount::where('kode_akun', '111')->firstOrFail();

            $lines = [
                ['coa_account_id' => $coaKasBank->id, 'debit' => $hargaJual, 'kredit' => 0],
                ['coa_account_id' => $asset->coa_akumulasi_penyusutan_id, 'debit' => $asset->akumulasi_penyusutan, 'kredit' => 0],
                ['coa_account_id' => $asset->coa_aset_id, 'debit' => 0, 'kredit' => $asset->harga_perolehan],
            ];

            if (bccomp($selisih, '0', 2) > 0) {
                $coaLaba = \App\Models\CoaAccount::where('kode_akun', '431')->firstOrFail();
                $lines[] = ['coa_account_id' => $coaLaba->id, 'debit' => 0, 'kredit' => $selisih];
            } elseif (bccomp($selisih, '0', 2) < 0) {
                $coaRugi = \App\Models\CoaAccount::where('kode_akun', '59')->firstOrFail();
                $lines[] = ['coa_account_id' => $coaRugi->id, 'debit' => bcmul($selisih, '-1', 2), 'kredit' => 0];
            }

            $this->journalService->create([
                'tanggal' => $tanggalPelepasan,
                'keterangan' => "Pelepasan aset {$asset->nama_aset}",
                'referensi_type' => FixedAsset::class,
                'referensi_id' => $asset->id,
                'created_by' => $user->id,
            ], $lines);

            $asset->update([
                'status' => 'dilepas',
                'tanggal_pelepasan' => $tanggalPelepasan,
                'harga_jual_pelepasan' => $hargaJual,
            ]);
        });
    }
}
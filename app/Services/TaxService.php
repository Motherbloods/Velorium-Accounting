<?php

namespace App\Services;

use App\Exceptions\TaxSettingNotFoundException;
use App\Models\CoaAccount;
use App\Models\PphFinalTransaction;
use App\Models\TaxSetting;
use App\Models\TaxTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaxService
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function tarifPpn(string $tanggal): TaxSetting
    {
        $setting = TaxSetting::tarifBerlaku('PPN', $tanggal);

        if (!$setting) {
            throw new TaxSettingNotFoundException('PPN');
        }

        return $setting;
    }

    public function tarifPphFinal(string $tanggal): TaxSetting
    {
        $setting = TaxSetting::tarifBerlaku('PPh Final UMKM', $tanggal);

        if (!$setting) {
            throw new TaxSettingNotFoundException('PPh Final UMKM');
        }

        return $setting;
    }

    public function hitungPpn(string $dpp, string $tanggal): array
    {
        $setting = $this->tarifPpn($tanggal);
        $tarif = bcdiv((string) $setting->tarif_persen, '100', 6);
        $jumlahPajak = bcmul($dpp, $tarif, 2);

        return [
            'tarif_persen' => $setting->tarif_persen,
            'jumlah_pajak' => $jumlahPajak,
        ];
    }

    public function recordPpnKeluaran(string $referensiType, int $referensiId, string $dpp, string $tarifPersen, string $jumlahPajak, string $periodePajak, ?int $journalEntryId = null): TaxTransaction
    {
        return TaxTransaction::create([
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'tipe' => 'ppn_keluaran',
            'dpp' => $dpp,
            'tarif_persen' => $tarifPersen,
            'jumlah_pajak' => $jumlahPajak,
            'periode_pajak' => $periodePajak,
            'journal_entry_id' => $journalEntryId,
        ]);
    }

    public function recordPpnMasukan(string $referensiType, int $referensiId, string $dpp, string $tarifPersen, string $jumlahPajak, string $periodePajak, ?int $journalEntryId = null): TaxTransaction
    {
        return TaxTransaction::create([
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'tipe' => 'ppn_masukan',
            'dpp' => $dpp,
            'tarif_persen' => $tarifPersen,
            'jumlah_pajak' => $jumlahPajak,
            'periode_pajak' => $periodePajak,
            'journal_entry_id' => $journalEntryId,
        ]);
    }

    public function setorPpn(string $periodePajak, User $user): void
    {
        $totalKeluaran = (string) TaxTransaction::where('tipe', 'ppn_keluaran')
            ->where('periode_pajak', $periodePajak)
            ->sum('jumlah_pajak');

        $totalMasukan = (string) TaxTransaction::where('tipe', 'ppn_masukan')
            ->where('periode_pajak', $periodePajak)
            ->sum('jumlah_pajak');

        $selisih = bcsub($totalKeluaran, $totalMasukan, 2);

        $coaPpnKeluaran = CoaAccount::where('kode_akun', '216')->firstOrFail();
        $coaPpnMasukan = CoaAccount::where('kode_akun', '117')->firstOrFail();

        $lines = [
            ['coa_account_id' => $coaPpnKeluaran->id, 'debit' => $totalKeluaran, 'kredit' => 0],
        ];

        if (bccomp($selisih, '0', 2) > 0) {
            $coaKasBank = CoaAccount::where('kode_akun', '111')->firstOrFail();
            $lines[] = ['coa_account_id' => $coaPpnMasukan->id, 'debit' => 0, 'kredit' => $totalMasukan];
            $lines[] = ['coa_account_id' => $coaKasBank->id, 'debit' => 0, 'kredit' => $selisih];
        } else {
            $lines[] = ['coa_account_id' => $coaPpnMasukan->id, 'debit' => 0, 'kredit' => $totalMasukan];
        }

        $this->journalService->create([
            'tanggal' => now()->toDateString(),
            'keterangan' => "Penyetoran/pelaporan PPN periode {$periodePajak}",
            'created_by' => $user->id,
        ], $lines);
    }

    public function omzetBrutoBulanan(string $periodePajak): string
    {
        $tanggalMulai = "{$periodePajak}-01";
        $tanggalSelesai = date('Y-m-t', strtotime($tanggalMulai));

        return (string) DB::table('sales')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->sum('dpp_ppn');
    }

    public function recognizePphFinal(string $periodePajak, User $user): PphFinalTransaction
    {
        if (PphFinalTransaction::where('periode_pajak', $periodePajak)->exists()) {
            abort(422, 'PPh Final untuk periode ini sudah diakui.');
        }

        $omzet = $this->omzetBrutoBulanan($periodePajak);
        $setting = $this->tarifPphFinal("{$periodePajak}-01");
        $tarif = bcdiv((string) $setting->tarif_persen, '100', 6);
        $jumlahPajak = bcmul($omzet, $tarif, 2);

        return DB::transaction(function () use ($periodePajak, $omzet, $setting, $jumlahPajak, $user) {
            $coaBeban = CoaAccount::where('kode_akun', '58')->firstOrFail();
            $coaHutang = CoaAccount::where('kode_akun', '217')->firstOrFail();

            $entry = $this->journalService->create([
                'tanggal' => now()->toDateString(),
                'keterangan' => "Pengakuan PPh Final periode {$periodePajak}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaBeban->id, 'debit' => $jumlahPajak, 'kredit' => 0],
                ['coa_account_id' => $coaHutang->id, 'debit' => 0, 'kredit' => $jumlahPajak],
            ]);

            return PphFinalTransaction::create([
                'periode_pajak' => $periodePajak,
                'omzet_bruto' => $omzet,
                'tarif_persen' => $setting->tarif_persen,
                'jumlah_pajak' => $jumlahPajak,
                'status' => 'diakui',
                'journal_entry_pengakuan_id' => $entry->id,
            ]);
        });
    }

    public function setorPphFinal(PphFinalTransaction $pphFinal, string $coaKasBankId, User $user): void
    {
        if ($pphFinal->status !== 'diakui') {
            abort(422, 'PPh Final ini sudah disetor.');
        }

        DB::transaction(function () use ($pphFinal, $coaKasBankId, $user) {
            $coaHutang = CoaAccount::where('kode_akun', '217')->firstOrFail();

            $entry = $this->journalService->create([
                'tanggal' => now()->toDateString(),
                'keterangan' => "Penyetoran PPh Final periode {$pphFinal->periode_pajak}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaHutang->id, 'debit' => $pphFinal->jumlah_pajak, 'kredit' => 0],
                ['coa_account_id' => $coaKasBankId, 'debit' => 0, 'kredit' => $pphFinal->jumlah_pajak],
            ]);

            $pphFinal->update([
                'status' => 'disetor',
                'journal_entry_penyetoran_id' => $entry->id,
            ]);
        });
    }
}
<?php

namespace App\Services;

use App\Models\AdjustingEntry;
use App\Models\CoaAccount;
use App\Models\PrepaidExpense;
use App\Models\UnearnedRevenue;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdjustingEntryService
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function createPrepaidExpense(array $data, User $user): PrepaidExpense
    {
        return DB::transaction(function () use ($data, $user) {
            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal_bayar'],
                'keterangan' => "Pembayaran dimuka: {$data['nama']}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $data['coa_aset_id'], 'debit' => $data['total_dibayar'], 'kredit' => 0],
                ['coa_account_id' => $data['coa_kas_bank_id'], 'debit' => 0, 'kredit' => $data['total_dibayar']],
            ]);

            return PrepaidExpense::create([
                'nama' => $data['nama'],
                'coa_aset_id' => $data['coa_aset_id'],
                'coa_beban_id' => $data['coa_beban_id'],
                'tanggal_bayar' => $data['tanggal_bayar'],
                'total_dibayar' => $data['total_dibayar'],
                'jumlah_bulan_cakupan' => $data['jumlah_bulan_cakupan'],
                'sisa_belum_diakui' => $data['total_dibayar'],
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function createUnearnedRevenue(array $data, User $user): UnearnedRevenue
    {
        return DB::transaction(function () use ($data, $user) {
            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal_terima'],
                'keterangan' => "Penerimaan dimuka: {$data['nama']}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $data['coa_kas_bank_id'], 'debit' => $data['total_diterima'], 'kredit' => 0],
                ['coa_account_id' => $data['coa_kewajiban_id'], 'debit' => 0, 'kredit' => $data['total_diterima']],
            ]);

            return UnearnedRevenue::create([
                'nama' => $data['nama'],
                'coa_kewajiban_id' => $data['coa_kewajiban_id'],
                'coa_pendapatan_id' => $data['coa_pendapatan_id'],
                'tanggal_terima' => $data['tanggal_terima'],
                'total_diterima' => $data['total_diterima'],
                'jumlah_bulan_cakupan' => $data['jumlah_bulan_cakupan'],
                'sisa_belum_diakui' => $data['total_diterima'],
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    protected function alokasiTerbatas(string $alokasiUsulan, string $sisaBelumDiakui): string
    {
        return bccomp($alokasiUsulan, $sisaBelumDiakui, 2) > 0 ? $sisaBelumDiakui : $alokasiUsulan;
    }

    public function runPrepaidExpense(PrepaidExpense $prepaid, string $periode, User $user): ?AdjustingEntry
    {
        $periodeAwalBulan = date('Y-m-01', strtotime($periode));

        if (AdjustingEntry::where('tipe', 'prepaid_expense')->where('referensi_id', $prepaid->id)->where('periode', $periodeAwalBulan)->exists()) {
            return null;
        }

        if (bccomp((string) $prepaid->sisa_belum_diakui, '0', 2) <= 0) {
            return null;
        }

        $alokasi = $this->alokasiTerbatas($prepaid->alokasiBulanan(), (string) $prepaid->sisa_belum_diakui);

        return DB::transaction(function () use ($prepaid, $periodeAwalBulan, $alokasi, $user) {
            $entry = $this->journalService->create([
                'tanggal' => $periodeAwalBulan,
                'keterangan' => "Penyesuaian biaya dibayar dimuka: {$prepaid->nama}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $prepaid->coa_beban_id, 'debit' => $alokasi, 'kredit' => 0],
                ['coa_account_id' => $prepaid->coa_aset_id, 'debit' => 0, 'kredit' => $alokasi],
            ]);

            $prepaid->update([
                'sisa_belum_diakui' => bcsub((string) $prepaid->sisa_belum_diakui, $alokasi, 2),
            ]);

            return AdjustingEntry::create([
                'tipe' => 'prepaid_expense',
                'referensi_id' => $prepaid->id,
                'periode' => $periodeAwalBulan,
                'jumlah' => $alokasi,
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function runUnearnedRevenue(UnearnedRevenue $unearned, string $periode, User $user): ?AdjustingEntry
    {
        $periodeAwalBulan = date('Y-m-01', strtotime($periode));

        if (AdjustingEntry::where('tipe', 'unearned_revenue')->where('referensi_id', $unearned->id)->where('periode', $periodeAwalBulan)->exists()) {
            return null;
        }

        if (bccomp((string) $unearned->sisa_belum_diakui, '0', 2) <= 0) {
            return null;
        }

        $alokasi = $this->alokasiTerbatas($unearned->alokasiBulanan(), (string) $unearned->sisa_belum_diakui);

        return DB::transaction(function () use ($unearned, $periodeAwalBulan, $alokasi, $user) {
            $entry = $this->journalService->create([
                'tanggal' => $periodeAwalBulan,
                'keterangan' => "Penyesuaian pendapatan diterima dimuka: {$unearned->nama}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $unearned->coa_kewajiban_id, 'debit' => $alokasi, 'kredit' => 0],
                ['coa_account_id' => $unearned->coa_pendapatan_id, 'debit' => 0, 'kredit' => $alokasi],
            ]);

            $unearned->update([
                'sisa_belum_diakui' => bcsub((string) $unearned->sisa_belum_diakui, $alokasi, 2),
            ]);

            return AdjustingEntry::create([
                'tipe' => 'unearned_revenue',
                'referensi_id' => $unearned->id,
                'periode' => $periodeAwalBulan,
                'jumlah' => $alokasi,
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function recordAccruedExpense(array $data, User $user): AdjustingEntry
    {
        return DB::transaction(function () use ($data, $user) {
            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'],
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $data['coa_beban_id'], 'debit' => $data['jumlah'], 'kredit' => 0],
                ['coa_account_id' => $data['coa_kewajiban_id'], 'debit' => 0, 'kredit' => $data['jumlah']],
            ]);

            return AdjustingEntry::create([
                'tipe' => 'accrued_expense',
                'referensi_id' => null,
                'periode' => date('Y-m-01', strtotime($data['tanggal'])),
                'jumlah' => $data['jumlah'],
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function recordAccruedRevenue(array $data, User $user): AdjustingEntry
    {
        return DB::transaction(function () use ($data, $user) {
            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'],
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $data['coa_piutang_id'], 'debit' => $data['jumlah'], 'kredit' => 0],
                ['coa_account_id' => $data['coa_pendapatan_id'], 'debit' => 0, 'kredit' => $data['jumlah']],
            ]);

            return AdjustingEntry::create([
                'tipe' => 'accrued_revenue',
                'referensi_id' => null,
                'periode' => date('Y-m-01', strtotime($data['tanggal'])),
                'jumlah' => $data['jumlah'],
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function runMonthly(string $periode, User $user): array
    {
        $results = [];

        foreach (PrepaidExpense::where('sisa_belum_diakui', '>', 0)->get() as $prepaid) {
            $adjustingEntry = $this->runPrepaidExpense($prepaid, $periode, $user);

            if ($adjustingEntry) {
                $results[] = $adjustingEntry;
            }
        }

        foreach (UnearnedRevenue::where('sisa_belum_diakui', '>', 0)->get() as $unearned) {
            $adjustingEntry = $this->runUnearnedRevenue($unearned, $periode, $user);

            if ($adjustingEntry) {
                $results[] = $adjustingEntry;
            }
        }

        return $results;
    }
}
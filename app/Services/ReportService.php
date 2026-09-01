<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\FiscalPeriod;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;

class ReportService
{
    public function openingBalance(CoaAccount $account, string $sebelumTanggal): string
    {
        $debit = (string) JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->where('journal_entries.tanggal', '<', $sebelumTanggal)
            ->sum('journal_details.debit');

        $kredit = (string) JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->where('journal_entries.tanggal', '<', $sebelumTanggal)
            ->sum('journal_details.kredit');

        return $account->saldo_normal === 'debit'
            ? bcsub($debit, $kredit, 2)
            : bcsub($kredit, $debit, 2);
    }

    public function generalLedger(CoaAccount $account, FiscalPeriod $fiscalPeriod): array
    {
        $saldoAwal = $this->openingBalance($account, $fiscalPeriod->tanggal_mulai->toDateString());

        $rows = JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->whereBetween('journal_entries.tanggal', [$fiscalPeriod->tanggal_mulai, $fiscalPeriod->tanggal_selesai])
            ->orderBy('journal_entries.tanggal')
            ->orderBy('journal_entries.id')
            ->select(
                'journal_entries.id as journal_entry_id',
                'journal_entries.nomor_bukti',
                'journal_entries.tanggal',
                'journal_entries.keterangan as keterangan_jurnal',
                'journal_details.debit',
                'journal_details.kredit',
                'journal_details.keterangan as keterangan_baris'
            )
            ->get();

        $saldoBerjalan = $saldoAwal;
        $mutasi = $rows->map(function ($row) use (&$saldoBerjalan, $account) {
            if ($account->saldo_normal === 'debit') {
                $saldoBerjalan = bcadd($saldoBerjalan, bcsub((string) $row->debit, (string) $row->kredit, 2), 2);
            } else {
                $saldoBerjalan = bcadd($saldoBerjalan, bcsub((string) $row->kredit, (string) $row->debit, 2), 2);
            }

            return [
                'journal_entry_id' => $row->journal_entry_id,
                'nomor_bukti' => $row->nomor_bukti,
                'tanggal' => $row->tanggal,
                'keterangan' => $row->keterangan_baris ?: $row->keterangan_jurnal,
                'debit' => $row->debit,
                'kredit' => $row->kredit,
                'saldo_berjalan' => $saldoBerjalan,
            ];
        });

        return [
            'account' => $account,
            'saldo_awal' => $saldoAwal,
            'mutasi' => $mutasi,
            'saldo_akhir' => $saldoBerjalan,
        ];
    }

    public function trialBalance(FiscalPeriod $fiscalPeriod): Collection
    {
        $accounts = CoaAccount::postable()->active()->orderBy('kode_akun')->get();

        return $accounts->map(function (CoaAccount $account) use ($fiscalPeriod) {
            $totalDebit = (string) JournalDetail::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                ->where('journal_details.coa_account_id', $account->id)
                ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
                ->whereBetween('journal_entries.tanggal', [$fiscalPeriod->tanggal_mulai, $fiscalPeriod->tanggal_selesai])
                ->sum('journal_details.debit');

            $totalKredit = (string) JournalDetail::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
                ->where('journal_details.coa_account_id', $account->id)
                ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
                ->whereBetween('journal_entries.tanggal', [$fiscalPeriod->tanggal_mulai, $fiscalPeriod->tanggal_selesai])
                ->sum('journal_details.kredit');

            $saldoAwal = $this->openingBalance($account, $fiscalPeriod->tanggal_mulai->toDateString());

            $saldoAkhir = $account->saldo_normal === 'debit'
                ? bcadd($saldoAwal, bcsub($totalDebit, $totalKredit, 2), 2)
                : bcadd($saldoAwal, bcsub($totalKredit, $totalDebit, 2), 2);

            return [
                'account' => $account,
                'saldo_awal' => $saldoAwal,
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit,
                'saldo_akhir' => $saldoAkhir,
            ];
        })->filter(function (array $row) {
            return bccomp($row['saldo_awal'], '0', 2) !== 0
                || bccomp($row['total_debit'], '0', 2) !== 0
                || bccomp($row['total_kredit'], '0', 2) !== 0;
        })->values();
    }

    public function trialBalanceIsBalanced(Collection $trialBalance): bool
    {
        $totalDebitAkhir = '0';
        $totalKreditAkhir = '0';

        foreach ($trialBalance as $row) {
            if (bccomp($row['saldo_akhir'], '0', 2) >= 0) {
                if ($row['account']->saldo_normal === 'debit') {
                    $totalDebitAkhir = bcadd($totalDebitAkhir, $row['saldo_akhir'], 2);
                } else {
                    $totalKreditAkhir = bcadd($totalKreditAkhir, $row['saldo_akhir'], 2);
                }
            }
        }

        return bccomp($totalDebitAkhir, $totalKreditAkhir, 2) === 0;
    }
}
<?php

namespace App\Services;

use App\Models\AdjustingEntry;
use App\Models\ClosingPeriod;
use App\Models\CoaAccount;
use App\Models\FiscalPeriod;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClosingService
{
    public function __construct(
        protected JournalService $journalService,
        protected ReportService $reportService
    ) {
    }

    public function prepareClosingEntry(FiscalPeriod $fiscalPeriod, User $user): ClosingPeriod
    {
        if (ClosingPeriod::where('fiscal_period_id', $fiscalPeriod->id)->exists()) {
            abort(422, 'Jurnal penutup untuk periode ini sudah pernah dibuat.');
        }

        return DB::transaction(function () use ($fiscalPeriod, $user) {
            $accounts = CoaAccount::postable()->active()
                ->whereIn('tipe_akun', ['pendapatan', 'beban'])
                ->get();

            $lines = [];
            $totalPendapatan = '0';
            $totalBeban = '0';

            foreach ($accounts as $account) {
                $saldo = $this->reportService->accountBalanceAsOf($account, $fiscalPeriod->tanggal_selesai->toDateString());
                $saldoAwal = $this->reportService->openingBalance($account, $fiscalPeriod->tanggal_mulai->toDateString());
                $mutasiPeriode = bcsub($saldo, $saldoAwal, 2);

                if (bccomp($mutasiPeriode, '0', 2) === 0) {
                    continue;
                }

                if ($account->tipe_akun === 'pendapatan') {
                    $lines[] = [
                        'coa_account_id' => $account->id,
                        'debit' => $account->saldo_normal === 'kredit' ? $mutasiPeriode : 0,
                        'kredit' => $account->saldo_normal === 'debit' ? $mutasiPeriode : 0,
                    ];
                    $totalPendapatan = bcadd($totalPendapatan, $mutasiPeriode, 2);
                } else {
                    $lines[] = [
                        'coa_account_id' => $account->id,
                        'debit' => $account->saldo_normal === 'kredit' ? $mutasiPeriode : 0,
                        'kredit' => $account->saldo_normal === 'debit' ? $mutasiPeriode : 0,
                    ];
                    $totalBeban = bcadd($totalBeban, $mutasiPeriode, 2);
                }
            }

            $coaIkhtisar = CoaAccount::where('kode_akun', '34')->firstOrFail();
            $labaRugiBersih = bcsub($totalPendapatan, $totalBeban, 2);

            if (bccomp($totalPendapatan, '0', 2) > 0) {
                $lines[] = ['coa_account_id' => $coaIkhtisar->id, 'debit' => 0, 'kredit' => $totalPendapatan];
            }

            if (bccomp($totalBeban, '0', 2) > 0) {
                $lines[] = ['coa_account_id' => $coaIkhtisar->id, 'debit' => $totalBeban, 'kredit' => 0];
            }

            $coaLabaDitahan = CoaAccount::where('kode_akun', '33')->firstOrFail();

            if (bccomp($labaRugiBersih, '0', 2) > 0) {
                $lines[] = ['coa_account_id' => $coaIkhtisar->id, 'debit' => $labaRugiBersih, 'kredit' => 0];
                $lines[] = ['coa_account_id' => $coaLabaDitahan->id, 'debit' => 0, 'kredit' => $labaRugiBersih];
            } elseif (bccomp($labaRugiBersih, '0', 2) < 0) {
                $rugi = bcmul($labaRugiBersih, '-1', 2);
                $lines[] = ['coa_account_id' => $coaIkhtisar->id, 'debit' => 0, 'kredit' => $rugi];
                $lines[] = ['coa_account_id' => $coaLabaDitahan->id, 'debit' => $rugi, 'kredit' => 0];
            }

            $coaPrive = CoaAccount::where('kode_akun', '32')->firstOrFail();
            $saldoPrive = $this->reportService->accountBalanceAsOf($coaPrive, $fiscalPeriod->tanggal_selesai->toDateString());

            if (bccomp($saldoPrive, '0', 2) > 0) {
                $coaModal = CoaAccount::where('kode_akun', '31')->firstOrFail();
                $lines[] = ['coa_account_id' => $coaModal->id, 'debit' => $saldoPrive, 'kredit' => 0];
                $lines[] = ['coa_account_id' => $coaPrive->id, 'debit' => 0, 'kredit' => $saldoPrive];
            }

            $entry = $this->journalService->create([
                'tanggal' => $fiscalPeriod->tanggal_selesai->toDateString(),
                'keterangan' => "Jurnal penutup periode {$fiscalPeriod->nama_periode}",
                'referensi_type' => FiscalPeriod::class,
                'referensi_id' => $fiscalPeriod->id,
                'created_by' => $user->id,
            ], $lines, 'JU');

            return ClosingPeriod::create([
                'fiscal_period_id' => $fiscalPeriod->id,
                'laba_rugi_bersih' => $labaRugiBersih,
                'closing_journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function finalizeClosing(ClosingPeriod $closingPeriod, User $user): void
    {
        $entry = $closingPeriod->closingJournalEntry;

        if (!$entry || $entry->status !== JournalEntry::STATUS_POSTED) {
            abort(422, 'Jurnal penutup harus berstatus posted sebelum periode ditutup.');
        }

        DB::transaction(function () use ($closingPeriod, $user) {
            $fiscalPeriod = $closingPeriod->fiscalPeriod;
            $fiscalPeriod->update(['status' => 'closed']);

            $closingPeriod->update([
                'closed_at' => now(),
                'closed_by' => $user->id,
            ]);

            $this->createReversingEntries($fiscalPeriod, $closingPeriod, $user);
        });
    }

    protected function createReversingEntries(FiscalPeriod $fiscalPeriod, ClosingPeriod $closingPeriod, User $user): void
    {
        $accruedEntries = AdjustingEntry::whereIn('tipe', ['accrued_expense', 'accrued_revenue'])
            ->where('periode', $fiscalPeriod->tanggal_selesai->copy()->startOfMonth()->toDateString())
            ->get();

        if ($accruedEntries->isEmpty()) {
            return;
        }

        $tanggalPembalik = $fiscalPeriod->tanggal_selesai->copy()->addDay()->toDateString();

        $lines = [];

        foreach ($accruedEntries as $accrued) {
            $originalEntry = $accrued->journalEntry;

            if (!$originalEntry) {
                continue;
            }

            foreach ($originalEntry->details as $detail) {
                $lines[] = [
                    'coa_account_id' => $detail->coa_account_id,
                    'debit' => $detail->kredit,
                    'kredit' => $detail->debit,
                ];
            }
        }

        if (empty($lines)) {
            return;
        }

        $reversingEntry = $this->journalService->create([
            'tanggal' => $tanggalPembalik,
            'keterangan' => "Jurnal pembalik awal periode setelah {$fiscalPeriod->nama_periode}",
            'referensi_type' => FiscalPeriod::class,
            'referensi_id' => $fiscalPeriod->id,
            'created_by' => $user->id,
        ], $lines, 'JU');

        $closingPeriod->update(['reversing_journal_entry_id' => $reversingEntry->id]);
    }
}
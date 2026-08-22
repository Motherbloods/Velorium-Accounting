<?php

namespace App\Services;

use App\Exceptions\FiscalPeriodClosedException;
use App\Exceptions\JournalNotBalancedException;
use App\Models\FiscalPeriod;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function __construct(
        protected DocumentNumberService $documentNumberService
    ) {
    }

    public function create(array $data, array $lines, string $prefix = 'JU'): JournalEntry
    {
        $totalDebit = '0';
        $totalKredit = '0';

        foreach ($lines as $line) {
            $totalDebit = bcadd($totalDebit, (string) ($line['debit'] ?? 0), 2);
            $totalKredit = bcadd($totalKredit, (string) ($line['kredit'] ?? 0), 2);
        }

        if (bccomp($totalDebit, $totalKredit, 2) !== 0) {
            throw new JournalNotBalancedException($totalDebit, $totalKredit);
        }

        $fiscalPeriod = FiscalPeriod::findForDate($data['tanggal']);

        if (!$fiscalPeriod || !$fiscalPeriod->isOpen()) {
            throw new FiscalPeriodClosedException();
        }

        return DB::transaction(function () use ($data, $lines, $fiscalPeriod, $prefix) {
            $entry = JournalEntry::create([
                'nomor_bukti' => $this->documentNumberService->generate($prefix, $data['tanggal']),
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'] ?? null,
                'referensi_type' => $data['referensi_type'] ?? null,
                'referensi_id' => $data['referensi_id'] ?? null,
                'fiscal_period_id' => $fiscalPeriod->id,
                'created_by' => $data['created_by'] ?? null,
                'status' => JournalEntry::STATUS_DRAFT,
            ]);

            foreach ($lines as $line) {
                $entry->details()->create([
                    'coa_account_id' => $line['coa_account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'kredit' => $line['kredit'] ?? 0,
                    'keterangan' => $line['keterangan'] ?? null,
                ]);
            }

            return $entry->fresh('details');
        });
    }

    public function submit(JournalEntry $entry, User $user): JournalEntry
    {
        if ($entry->status !== JournalEntry::STATUS_DRAFT) {
            abort(422, 'Hanya jurnal berstatus draft yang bisa diajukan.');
        }

        if (!$entry->isBalanced()) {
            throw new JournalNotBalancedException($entry->totalDebit(), $entry->totalKredit());
        }

        $entry->update([
            'status' => JournalEntry::STATUS_SUBMITTED,
            'submitted_by' => $user->id,
        ]);

        return $entry;
    }

    public function approve(JournalEntry $entry, User $user): JournalEntry
    {
        if ($entry->status !== JournalEntry::STATUS_SUBMITTED) {
            abort(422, 'Hanya jurnal berstatus submitted yang bisa disetujui.');
        }

        $entry->update([
            'status' => JournalEntry::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return $entry;
    }

    public function reject(JournalEntry $entry, User $user, string $catatan): JournalEntry
    {
        if ($entry->status !== JournalEntry::STATUS_SUBMITTED) {
            abort(422, 'Hanya jurnal berstatus submitted yang bisa ditolak.');
        }

        $entry->update([
            'status' => JournalEntry::STATUS_REJECTED,
            'approved_by' => $user->id,
            'catatan_penolakan' => $catatan,
        ]);

        return $entry;
    }

    public function backToDraft(JournalEntry $entry): JournalEntry
    {
        if ($entry->status !== JournalEntry::STATUS_REJECTED) {
            abort(422, 'Hanya jurnal berstatus rejected yang bisa dikembalikan ke draft.');
        }

        $entry->update([
            'status' => JournalEntry::STATUS_DRAFT,
            'submitted_by' => null,
            'approved_by' => null,
            'approved_at' => null,
            'catatan_penolakan' => null,
        ]);

        return $entry;
    }

    public function post(JournalEntry $entry): JournalEntry
    {
        if ($entry->status !== JournalEntry::STATUS_APPROVED) {
            abort(422, 'Hanya jurnal berstatus approved yang bisa diposting.');
        }

        if (!$entry->isBalanced()) {
            throw new JournalNotBalancedException($entry->totalDebit(), $entry->totalKredit());
        }

        $entry->update([
            'status' => JournalEntry::STATUS_POSTED,
        ]);

        return $entry;
    }
}
<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\CoaAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CashTransactionService
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function create(array $data, User $user): CashTransaction
    {
        return DB::transaction(function () use ($data, $user) {
            $coaKasBank = CoaAccount::findOrFail($data['coa_kas_bank_id']);
            $coaLawan = CoaAccount::findOrFail($data['coa_lawan_id']);
            $jumlah = (string) $data['jumlah'];
            $tipe = $data['tipe'];

            $prefix = $tipe === 'masuk' ? 'KM' : 'KK';

            $lines = $tipe === 'masuk'
                ? [
                    ['coa_account_id' => $coaKasBank->id, 'debit' => $jumlah, 'kredit' => 0],
                    ['coa_account_id' => $coaLawan->id, 'debit' => 0, 'kredit' => $jumlah],
                ]
                : [
                    ['coa_account_id' => $coaLawan->id, 'debit' => $jumlah, 'kredit' => 0],
                    ['coa_account_id' => $coaKasBank->id, 'debit' => 0, 'kredit' => $jumlah],
                ];

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'] ?? null,
                'referensi_type' => $data['referensi_type'] ?? null,
                'referensi_id' => $data['referensi_id'] ?? null,
                'created_by' => $user->id,
            ], $lines, $prefix);

            $cashTransaction = CashTransaction::create([
                'nomor_bukti' => $entry->nomor_bukti,
                'tanggal' => $data['tanggal'],
                'tipe' => $tipe,
                'coa_kas_bank_id' => $coaKasBank->id,
                'coa_lawan_id' => $coaLawan->id,
                'jumlah' => $jumlah,
                'keterangan' => $data['keterangan'] ?? null,
                'referensi_type' => $data['referensi_type'] ?? null,
                'referensi_id' => $data['referensi_id'] ?? null,
                'journal_entry_id' => $entry->id,
                'branch_id' => $data['branch_id'] ?? null,
            ]);

            $bankAccount = BankAccount::where('coa_account_id', $coaKasBank->id)->first();

            if ($bankAccount) {
                $delta = $tipe === 'masuk' ? $jumlah : bcmul($jumlah, '-1', 2);
                $bankAccount->update([
                    'saldo_berjalan' => bcadd((string) $bankAccount->saldo_berjalan, $delta, 2),
                ]);
            }

            return $cashTransaction;
        });
    }
}
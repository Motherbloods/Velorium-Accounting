<?php

namespace App\Services;

use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\CoaAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BankReconciliationService
{
    public function __construct(protected CashTransactionService $cashTransactionService)
    {
    }

    public function create(array $data, User $user): BankReconciliation
    {
        return BankReconciliation::create([
            'bank_account_id' => $data['bank_account_id'],
            'periode' => $data['periode'],
            'saldo_buku' => $data['saldo_buku'],
            'saldo_rekening_koran' => $data['saldo_rekening_koran'],
            'status' => 'draft',
            'dibuat_oleh' => $user->id,
        ]);
    }

    public function addItem(BankReconciliation $reconciliation, array $data): BankReconciliationItem
    {
        $item = BankReconciliationItem::create([
            'bank_reconciliation_id' => $reconciliation->id,
            'kategori' => $data['kategori'],
            'jenis' => $data['jenis'],
            'keterangan' => $data['keterangan'] ?? null,
            'jumlah' => $data['jumlah'],
            'sudah_diposting' => false,
        ]);

        $this->recalculate($reconciliation);

        return $item;
    }

    public function postItem(BankReconciliationItem $item, string $coaLawanId, User $user): void
    {
        if ($item->kategori !== 'sisi_buku') {
            abort(422, 'Hanya item sisi buku yang bisa diposting ke jurnal.');
        }

        if ($item->sudah_diposting) {
            abort(422, 'Item ini sudah diposting.');
        }

        $reconciliation = $item->bankReconciliation()->with('bankAccount')->first();
        $coaKasBank = $reconciliation->bankAccount->coa_account_id;

        $tipe = str_contains(strtolower($item->jenis), 'biaya') || str_contains(strtolower($item->jenis), 'admin')
            ? 'keluar'
            : 'masuk';

        DB::transaction(function () use ($item, $coaKasBank, $coaLawanId, $tipe, $reconciliation, $user) {
            $cashTransaction = $this->cashTransactionService->create([
                'tanggal' => $reconciliation->periode->toDateString(),
                'tipe' => $tipe,
                'coa_kas_bank_id' => $coaKasBank,
                'coa_lawan_id' => $coaLawanId,
                'jumlah' => $item->jumlah,
                'keterangan' => $item->keterangan ?? $item->jenis,
            ], $user);

            $item->update([
                'sudah_diposting' => true,
                'cash_transaction_id' => $cashTransaction->id,
            ]);
        });
    }

    public function recalculate(BankReconciliation $reconciliation): void
    {
        $reconciliation->refresh();

        $jasaGiro = (string) $reconciliation->items()->where('kategori', 'sisi_buku')->where('jenis', 'jasa_giro')->sum('jumlah');
        $biayaAdmin = (string) $reconciliation->items()->where('kategori', 'sisi_buku')->where('jenis', 'biaya_admin')->sum('jumlah');

        $saldoDisesuaikanBuku = bcsub(
            bcadd((string) $reconciliation->saldo_buku, $jasaGiro, 2),
            $biayaAdmin,
            2
        );

        $setoranDalamPerjalanan = (string) $reconciliation->items()->where('kategori', 'sisi_bank')->where('jenis', 'setoran_dalam_perjalanan')->sum('jumlah');
        $cekBeredar = (string) $reconciliation->items()->where('kategori', 'sisi_bank')->where('jenis', 'cek_beredar')->sum('jumlah');

        $saldoDisesuaikanBank = bcsub(
            bcadd((string) $reconciliation->saldo_rekening_koran, $setoranDalamPerjalanan, 2),
            $cekBeredar,
            2
        );

        $reconciliation->update([
            'saldo_disesuaikan_buku' => $saldoDisesuaikanBuku,
            'saldo_disesuaikan_bank' => $saldoDisesuaikanBank,
        ]);
    }

    public function complete(BankReconciliation $reconciliation): void
    {
        $this->recalculate($reconciliation);
        $reconciliation->refresh();

        if (!$reconciliation->isValid()) {
            abort(422, 'Rekonsiliasi tidak valid: saldo disesuaikan buku dan bank belum sama.');
        }

        $reconciliation->update(['status' => 'selesai']);
    }
}
<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReceivableService
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function create(array $data): Receivable
    {
        $terminHari = $data['termin_jatuh_tempo_hari'] ?? 30;

        return Receivable::create([
            'nomor_invoice' => $data['nomor_invoice'],
            'customer_id' => $data['customer_id'],
            'tanggal' => $data['tanggal'],
            'tanggal_jatuh_tempo' => date('Y-m-d', strtotime($data['tanggal'] . " +{$terminHari} days")),
            'total_tagihan' => $data['total_tagihan'],
            'sisa_piutang' => $data['total_tagihan'],
            'status' => 'belum_lunas',
            'referensi_type' => $data['referensi_type'] ?? null,
            'referensi_id' => $data['referensi_id'] ?? null,
            'journal_entry_id' => $data['journal_entry_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
        ]);
    }

    public function pay(Receivable $receivable, array $data, User $user): ReceivablePayment
    {
        if ($data['jumlah_bayar'] > $receivable->sisa_piutang) {
            abort(422, 'Jumlah bayar melebihi sisa piutang.');
        }

        return DB::transaction(function () use ($receivable, $data, $user) {
            $coaKasBank = CoaAccount::findOrFail($data['coa_kas_bank_id']);
            $coaPiutang = CoaAccount::where('kode_akun', '113')->firstOrFail();

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal_bayar'],
                'keterangan' => "Pembayaran piutang {$receivable->nomor_invoice}",
                'referensi_type' => Receivable::class,
                'referensi_id' => $receivable->id,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaKasBank->id, 'debit' => $data['jumlah_bayar'], 'kredit' => 0],
                ['coa_account_id' => $coaPiutang->id, 'debit' => 0, 'kredit' => $data['jumlah_bayar']],
            ]);

            $payment = ReceivablePayment::create([
                'receivable_id' => $receivable->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah_bayar' => $data['jumlah_bayar'],
                'coa_kas_bank_id' => $coaKasBank->id,
                'journal_entry_id' => $entry->id,
            ]);

            $sisaBaru = bcsub((string) $receivable->sisa_piutang, (string) $data['jumlah_bayar'], 2);

            $receivable->update([
                'sisa_piutang' => $sisaBaru,
                'status' => bccomp($sisaBaru, '0', 2) === 0
                    ? 'lunas'
                    : (bccomp($sisaBaru, (string) $receivable->total_tagihan, 2) === 0 ? 'belum_lunas' : 'lunas_sebagian'),
            ]);

            return $payment;
        });
    }

    public function agingBuckets(): array
    {
        $receivables = Receivable::where('status', '!=', 'lunas')->get();

        $buckets = [
            'belum_jatuh_tempo' => ['label' => 'Belum jatuh tempo', 'persen' => '1', 'total' => '0'],
            '1_30' => ['label' => '1-30 hari lewat jatuh tempo', 'persen' => '5', 'total' => '0'],
            '31_60' => ['label' => '31-60 hari', 'persen' => '10', 'total' => '0'],
            '61_90' => ['label' => '61-90 hari', 'persen' => '25', 'total' => '0'],
            'lebih_90' => ['label' => '> 90 hari', 'persen' => '50', 'total' => '0'],
        ];

        foreach ($receivables as $receivable) {
            $hariLewat = now()->startOfDay()->diffInDays($receivable->tanggal_jatuh_tempo->copy()->startOfDay(), false);

            $key = match (true) {
                $hariLewat >= 0 => 'belum_jatuh_tempo',
                $hariLewat >= -30 => '1_30',
                $hariLewat >= -60 => '31_60',
                $hariLewat >= -90 => '61_90',
                default => 'lebih_90',
            };

            $buckets[$key]['total'] = bcadd($buckets[$key]['total'], (string) $receivable->sisa_piutang, 2);
        }

        foreach ($buckets as $key => $bucket) {
            $buckets[$key]['taksiran'] = bcmul($bucket['total'], bcdiv($bucket['persen'], '100', 4), 2);
        }

        return $buckets;
    }

    public function totalTaksiranTakTertagih(): string
    {
        $buckets = $this->agingBuckets();
        $total = '0';

        foreach ($buckets as $bucket) {
            $total = bcadd($total, $bucket['taksiran'], 2);
        }

        return $total;
    }

    public function recordAllowanceAdjustment(string $tanggal, string $jumlahBaru, User $user): void
    {
        $coaBeban = CoaAccount::where('kode_akun', '55')->firstOrFail();
        $coaCadangan = CoaAccount::where('kode_akun', '114')->firstOrFail();

        if (bccomp($jumlahBaru, '0', 2) <= 0) {
            return;
        }

        $this->journalService->create([
            'tanggal' => $tanggal,
            'keterangan' => 'Estimasi cadangan kerugian piutang (metode aging)',
            'created_by' => $user->id,
        ], [
            ['coa_account_id' => $coaBeban->id, 'debit' => $jumlahBaru, 'kredit' => 0],
            ['coa_account_id' => $coaCadangan->id, 'debit' => 0, 'kredit' => $jumlahBaru],
        ]);
    }
}
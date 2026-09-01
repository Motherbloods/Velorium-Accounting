<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\Payable;
use App\Models\PayablePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayableService
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function create(array $data): Payable
    {
        $terminHari = $data['termin_jatuh_tempo_hari'] ?? 30;

        $tanggalJatuhTempo = $data['jenis'] === 'pinjaman'
            ? $data['tanggal_jatuh_tempo']
            : date('Y-m-d', strtotime($data['tanggal'] . " +{$terminHari} days"));

        return Payable::create([
            'nomor_hutang' => $data['nomor_hutang'],
            'supplier_id' => $data['supplier_id'] ?? null,
            'tanggal' => $data['tanggal'],
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'jenis' => $data['jenis'],
            'tarif_bunga_tahunan' => $data['tarif_bunga_tahunan'] ?? null,
            'total_hutang' => $data['total_hutang'],
            'sisa_hutang' => $data['total_hutang'],
            'status' => 'belum_lunas',
            'referensi_type' => $data['referensi_type'] ?? null,
            'referensi_id' => $data['referensi_id'] ?? null,
            'journal_entry_id' => $data['journal_entry_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
        ]);
    }

    public function createManualLoan(array $data, User $user): Payable
    {
        return DB::transaction(function () use ($data, $user) {
            $coaKasBank = CoaAccount::findOrFail($data['coa_kas_bank_id']);
            $coaHutangBank = CoaAccount::where('kode_akun', $data['jangka'] === 'panjang' ? '221' : '214')->firstOrFail();

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => "Penerimaan pinjaman bank {$data['nomor_hutang']}",
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaKasBank->id, 'debit' => $data['total_hutang'], 'kredit' => 0],
                ['coa_account_id' => $coaHutangBank->id, 'debit' => 0, 'kredit' => $data['total_hutang']],
            ]);

            return $this->create([
                'nomor_hutang' => $data['nomor_hutang'],
                'supplier_id' => null,
                'tanggal' => $data['tanggal'],
                'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo'],
                'jenis' => 'pinjaman',
                'tarif_bunga_tahunan' => $data['tarif_bunga_tahunan'],
                'total_hutang' => $data['total_hutang'],
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function pay(Payable $payable, array $data, User $user): PayablePayment
    {
        $jumlahPokok = (string) $data['jumlah_pokok'];
        $jumlahBunga = (string) ($data['jumlah_bunga'] ?? 0);

        if (bccomp($jumlahPokok, (string) $payable->sisa_hutang, 2) > 0) {
            abort(422, 'Jumlah pokok melebihi sisa hutang.');
        }

        return DB::transaction(function () use ($payable, $data, $jumlahPokok, $jumlahBunga, $user) {
            $coaKasBank = CoaAccount::findOrFail($data['coa_kas_bank_id']);
            $coaHutang = $payable->jenis === 'pinjaman'
                ? CoaAccount::where('kode_akun', $payable->klasifikasi() === 'jangka_panjang' ? '221' : '214')->firstOrFail()
                : CoaAccount::where('kode_akun', '211')->firstOrFail();

            $lines = [
                ['coa_account_id' => $coaHutang->id, 'debit' => $jumlahPokok, 'kredit' => 0],
            ];

            $totalBayar = $jumlahPokok;

            if (bccomp($jumlahBunga, '0', 2) > 0) {
                $coaBunga = CoaAccount::where('kode_akun', '56')->firstOrFail();
                $lines[] = ['coa_account_id' => $coaBunga->id, 'debit' => $jumlahBunga, 'kredit' => 0];
                $totalBayar = bcadd($jumlahPokok, $jumlahBunga, 2);
            }

            $lines[] = ['coa_account_id' => $coaKasBank->id, 'debit' => 0, 'kredit' => $totalBayar];

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal_bayar'],
                'keterangan' => "Pembayaran hutang {$payable->nomor_hutang}",
                'referensi_type' => Payable::class,
                'referensi_id' => $payable->id,
                'created_by' => $user->id,
            ], $lines);

            $payment = PayablePayment::create([
                'payable_id' => $payable->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah_pokok' => $jumlahPokok,
                'jumlah_bunga' => $jumlahBunga,
                'coa_kas_bank_id' => $coaKasBank->id,
                'journal_entry_id' => $entry->id,
            ]);

            $sisaBaru = bcsub((string) $payable->sisa_hutang, $jumlahPokok, 2);

            $payable->update([
                'sisa_hutang' => $sisaBaru,
                'status' => bccomp($sisaBaru, '0', 2) === 0
                    ? 'lunas'
                    : (bccomp($sisaBaru, (string) $payable->total_hutang, 2) === 0 ? 'belum_lunas' : 'lunas_sebagian'),
            ]);

            return $payment;
        });
    }

    public function hitungBebanBunga(Payable $payable, int $jumlahBulan): string
    {
        if (!$payable->tarif_bunga_tahunan) {
            return '0';
        }

        $tarif = bcdiv((string) $payable->tarif_bunga_tahunan, '100', 6);
        $porsiTahun = bcdiv((string) $jumlahBulan, '12', 6);

        return bcmul(bcmul((string) $payable->sisa_hutang, $tarif, 6), $porsiTahun, 2);
    }
}
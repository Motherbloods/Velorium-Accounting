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

    public function accountBalanceAsOf(CoaAccount $account, string $sampaiTanggal): string
    {
        $debit = (string) JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->where('journal_entries.tanggal', '<=', $sampaiTanggal)
            ->sum('journal_details.debit');

        $kredit = (string) JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->where('journal_entries.tanggal', '<=', $sampaiTanggal)
            ->sum('journal_details.kredit');

        return $account->saldo_normal === 'debit'
            ? bcsub($debit, $kredit, 2)
            : bcsub($kredit, $debit, 2);
    }

    protected function periodMovement(string $kodeAkun, FiscalPeriod $fiscalPeriod): string
    {
        $account = CoaAccount::where('kode_akun', $kodeAkun)->first();

        if (!$account) {
            return '0';
        }

        $debit = (string) JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->whereBetween('journal_entries.tanggal', [$fiscalPeriod->tanggal_mulai, $fiscalPeriod->tanggal_selesai])
            ->sum('journal_details.debit');

        $kredit = (string) JournalDetail::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_details.journal_entry_id')
            ->where('journal_details.coa_account_id', $account->id)
            ->where('journal_entries.status', JournalEntry::STATUS_POSTED)
            ->whereBetween('journal_entries.tanggal', [$fiscalPeriod->tanggal_mulai, $fiscalPeriod->tanggal_selesai])
            ->sum('journal_details.kredit');

        return $account->saldo_normal === 'debit'
            ? bcsub($debit, $kredit, 2)
            : bcsub($kredit, $debit, 2);
    }

    protected function balanceAsOfByKode(string $kodeAkun, string $sampaiTanggal): string
    {
        $account = CoaAccount::where('kode_akun', $kodeAkun)->first();

        if (!$account) {
            return '0';
        }

        return $this->accountBalanceAsOf($account, $sampaiTanggal);
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

    public function incomeStatement(FiscalPeriod $fiscalPeriod): array
    {
        $pendapatanPenjualanKotor = $this->periodMovement('41', $fiscalPeriod);
        $returPenjualan = $this->periodMovement('411', $fiscalPeriod);
        $potonganPenjualan = $this->periodMovement('412', $fiscalPeriod);
        $pendapatanPenjualanBersih = bcsub(bcsub($pendapatanPenjualanKotor, $returPenjualan, 2), $potonganPenjualan, 2);

        $pendapatanKonsinyasi = $this->periodMovement('42', $fiscalPeriod);

        $totalPendapatan = bcadd($pendapatanPenjualanBersih, $pendapatanKonsinyasi, 2);

        $hppKotor = $this->periodMovement('51', $fiscalPeriod);
        $potonganPembelian = $this->periodMovement('511', $fiscalPeriod);
        $hppBersih = bcsub($hppKotor, $potonganPembelian, 2);

        $labaKotor = bcsub($totalPendapatan, $hppBersih, 2);

        $bebanGaji = $this->periodMovement('53', $fiscalPeriod);
        $bebanPenyusutan = $this->periodMovement('54', $fiscalPeriod);
        $bebanKomisiKonsinyasi = $this->periodMovement('52', $fiscalPeriod);
        $bebanKerugianPiutang = $this->periodMovement('55', $fiscalPeriod);
        $bebanOperasionalLainnya = $this->periodMovement('57', $fiscalPeriod);

        $totalBebanOperasional = bcadd(
            bcadd(
                bcadd($bebanGaji, $bebanPenyusutan, 2),
                bcadd($bebanKomisiKonsinyasi, $bebanKerugianPiutang, 2),
                2
            ),
            $bebanOperasionalLainnya,
            2
        );

        $labaOperasional = bcsub($labaKotor, $totalBebanOperasional, 2);

        $pendapatanLainLain = bcadd($this->periodMovement('43', $fiscalPeriod), $this->periodMovement('431', $fiscalPeriod), 2);
        $bebanBunga = $this->periodMovement('56', $fiscalPeriod);

        $labaBersihSebelumPajak = bcsub(bcadd($labaOperasional, $pendapatanLainLain, 2), $bebanBunga, 2);

        $bebanPajakPenghasilan = $this->periodMovement('58', $fiscalPeriod);

        $labaBersihSetelahPajak = bcsub($labaBersihSebelumPajak, $bebanPajakPenghasilan, 2);

        return [
            'fiscal_period' => $fiscalPeriod,
            'pendapatan_penjualan_kotor' => $pendapatanPenjualanKotor,
            'retur_penjualan' => $returPenjualan,
            'potongan_penjualan' => $potonganPenjualan,
            'pendapatan_penjualan_bersih' => $pendapatanPenjualanBersih,
            'pendapatan_konsinyasi' => $pendapatanKonsinyasi,
            'total_pendapatan' => $totalPendapatan,
            'hpp_kotor' => $hppKotor,
            'potongan_pembelian' => $potonganPembelian,
            'hpp_bersih' => $hppBersih,
            'laba_kotor' => $labaKotor,
            'beban_gaji' => $bebanGaji,
            'beban_penyusutan' => $bebanPenyusutan,
            'beban_komisi_konsinyasi' => $bebanKomisiKonsinyasi,
            'beban_kerugian_piutang' => $bebanKerugianPiutang,
            'beban_operasional_lainnya' => $bebanOperasionalLainnya,
            'total_beban_operasional' => $totalBebanOperasional,
            'laba_operasional' => $labaOperasional,
            'pendapatan_lain_lain' => $pendapatanLainLain,
            'beban_bunga' => $bebanBunga,
            'laba_bersih_sebelum_pajak' => $labaBersihSebelumPajak,
            'beban_pajak_penghasilan' => $bebanPajakPenghasilan,
            'laba_bersih_setelah_pajak' => $labaBersihSetelahPajak,
        ];
    }

    public function balanceSheet(FiscalPeriod $fiscalPeriod): array
    {
        $sampaiTanggal = $fiscalPeriod->tanggal_selesai->toDateString();

        $kas = $this->balanceAsOfByKode('111', $sampaiTanggal);
        $bank = $this->balanceAsOfByKode('112', $sampaiTanggal);
        $piutangUsaha = $this->balanceAsOfByKode('113', $sampaiTanggal);
        $cadanganKerugianPiutang = $this->balanceAsOfByKode('114', $sampaiTanggal);
        $persediaanDagang = $this->balanceAsOfByKode('115', $sampaiTanggal);
        $persediaanKonsinyasi = $this->balanceAsOfByKode('116', $sampaiTanggal);
        $ppnMasukan = $this->balanceAsOfByKode('117', $sampaiTanggal);
        $bebanDibayarDimuka = $this->balanceAsOfByKode('118', $sampaiTanggal);

        $totalAsetLancar = bcadd(
            bcadd(
                bcadd(bcadd($kas, $bank, 2), bcsub($piutangUsaha, $cadanganKerugianPiutang, 2), 2),
                bcadd($persediaanDagang, $persediaanKonsinyasi, 2),
                2
            ),
            bcadd($ppnMasukan, $bebanDibayarDimuka, 2),
            2
        );

        $pasanganAsetTetap = [
            ['kode_aset' => '121', 'nama' => 'Peralatan', 'kode_akumulasi' => '122'],
            ['kode_aset' => '123', 'nama' => 'Kendaraan', 'kode_akumulasi' => '124'],
            ['kode_aset' => '125', 'nama' => 'Bangunan', 'kode_akumulasi' => '126'],
        ];

        $rincianAsetTetap = [];
        $totalAsetTetap = '0';

        foreach ($pasanganAsetTetap as $pasangan) {
            $saldoAset = $this->balanceAsOfByKode($pasangan['kode_aset'], $sampaiTanggal);
            $saldoAkumulasi = $this->balanceAsOfByKode($pasangan['kode_akumulasi'], $sampaiTanggal);
            $nilaiBuku = bcsub($saldoAset, $saldoAkumulasi, 2);

            $rincianAsetTetap[] = [
                'nama' => $pasangan['nama'],
                'harga_perolehan' => $saldoAset,
                'akumulasi_penyusutan' => $saldoAkumulasi,
                'nilai_buku' => $nilaiBuku,
            ];

            $totalAsetTetap = bcadd($totalAsetTetap, $nilaiBuku, 2);
        }

        $totalAset = bcadd($totalAsetLancar, $totalAsetTetap, 2);

        $kodeKewajibanPendek = ['211', '212', '213', '214', '215', '216', '217'];
        $rincianKewajibanPendek = [];
        $totalKewajibanPendek = '0';

        foreach ($kodeKewajibanPendek as $kode) {
            $account = CoaAccount::where('kode_akun', $kode)->first();

            if (!$account) {
                continue;
            }

            $saldo = $this->balanceAsOfByKode($kode, $sampaiTanggal);

            if (bccomp($saldo, '0', 2) !== 0) {
                $rincianKewajibanPendek[] = ['account' => $account, 'saldo' => $saldo];
            }

            $totalKewajibanPendek = bcadd($totalKewajibanPendek, $saldo, 2);
        }

        $totalKewajibanPanjang = $this->balanceAsOfByKode('221', $sampaiTanggal);

        $totalKewajiban = bcadd($totalKewajibanPendek, $totalKewajibanPanjang, 2);

        $modalPemilik = $this->balanceAsOfByKode('31', $sampaiTanggal);
        $prive = $this->balanceAsOfByKode('32', $sampaiTanggal);
        $modalPemilikBersih = bcsub($modalPemilik, $prive, 2);
        $labaDitahan = $this->balanceAsOfByKode('33', $sampaiTanggal);

        $labaTahunBerjalan = $fiscalPeriod->isOpen()
            ? $this->incomeStatement($fiscalPeriod)['laba_bersih_setelah_pajak']
            : '0';

        $totalEkuitas = bcadd(bcadd($modalPemilikBersih, $labaDitahan, 2), $labaTahunBerjalan, 2);

        $totalKewajibanEkuitas = bcadd($totalKewajiban, $totalEkuitas, 2);

        return [
            'fiscal_period' => $fiscalPeriod,
            'kas' => $kas,
            'bank' => $bank,
            'piutang_usaha' => $piutangUsaha,
            'cadangan_kerugian_piutang' => $cadanganKerugianPiutang,
            'persediaan_dagang' => $persediaanDagang,
            'persediaan_konsinyasi' => $persediaanKonsinyasi,
            'ppn_masukan' => $ppnMasukan,
            'beban_dibayar_dimuka' => $bebanDibayarDimuka,
            'total_aset_lancar' => $totalAsetLancar,
            'rincian_aset_tetap' => $rincianAsetTetap,
            'total_aset_tetap' => $totalAsetTetap,
            'total_aset' => $totalAset,
            'rincian_kewajiban_pendek' => $rincianKewajibanPendek,
            'total_kewajiban_pendek' => $totalKewajibanPendek,
            'total_kewajiban_panjang' => $totalKewajibanPanjang,
            'total_kewajiban' => $totalKewajiban,
            'modal_pemilik' => $modalPemilikBersih,
            'laba_ditahan' => $labaDitahan,
            'laba_tahun_berjalan' => $labaTahunBerjalan,
            'total_ekuitas' => $totalEkuitas,
            'total_kewajiban_ekuitas' => $totalKewajibanEkuitas,
            'is_balanced' => bccomp($totalAset, $totalKewajibanEkuitas, 2) === 0,
        ];
    }
}
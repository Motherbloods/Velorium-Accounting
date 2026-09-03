<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #1F2937;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 2px;
        }

        h2 {
            font-size: 13px;
            margin-top: 24px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        p.sub {
            color: #64748B;
            margin-top: 0;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td,
        th {
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
        }

        td.right,
        th.right {
            text-align: right;
        }

        tr.total {
            font-weight: bold;
            border-top: 1px solid #999;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <h1>Laporan Keuangan Lengkap</h1>
    <p class="sub">{{ $fiscal_period->nama_periode }} ({{ $fiscal_period->tanggal_mulai->format('d M Y') }} -
        {{ $fiscal_period->tanggal_selesai->format('d M Y') }})</p>

    <h2>1. Neraca Saldo</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Akun</th>
                <th class="right">Saldo Awal</th>
                <th class="right">Debit</th>
                <th class="right">Kredit</th>
                <th class="right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trial_balance as $row)
                <tr>
                    <td>{{ $row['account']->kode_akun }}</td>
                    <td>{{ $row['account']->nama_akun }}</td>
                    <td class="right">{{ number_format($row['saldo_awal'], 2) }}</td>
                    <td class="right">{{ number_format($row['total_debit'], 2) }}</td>
                    <td class="right">{{ number_format($row['total_kredit'], 2) }}</td>
                    <td class="right">{{ number_format($row['saldo_akhir'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>
    <h2>2. Laporan Laba Rugi</h2>
    <table>
        <tr>
            <td>Pendapatan Penjualan</td>
            <td class="right">{{ number_format($income_statement['pendapatan_penjualan_bersih'], 2) }}</td>
        </tr>
        <tr>
            <td>Pendapatan Penjualan Konsinyasi</td>
            <td class="right">{{ number_format($income_statement['pendapatan_konsinyasi'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Total Pendapatan</td>
            <td class="right">{{ number_format($income_statement['total_pendapatan'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Pokok Penjualan</td>
            <td class="right">({{ number_format($income_statement['hpp_bersih'], 2) }})</td>
        </tr>
        <tr class="total">
            <td>Laba Kotor</td>
            <td class="right">{{ number_format($income_statement['laba_kotor'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Gaji</td>
            <td class="right">{{ number_format($income_statement['beban_gaji'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Penyusutan</td>
            <td class="right">{{ number_format($income_statement['beban_penyusutan'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Komisi Konsinyasi</td>
            <td class="right">{{ number_format($income_statement['beban_komisi_konsinyasi'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Kerugian Piutang</td>
            <td class="right">{{ number_format($income_statement['beban_kerugian_piutang'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Operasional Lainnya</td>
            <td class="right">{{ number_format($income_statement['beban_operasional_lainnya'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Laba Operasional</td>
            <td class="right">{{ number_format($income_statement['laba_operasional'], 2) }}</td>
        </tr>
        <tr>
            <td>Pendapatan Lain-lain</td>
            <td class="right">{{ number_format($income_statement['pendapatan_lain_lain'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Bunga</td>
            <td class="right">({{ number_format($income_statement['beban_bunga'], 2) }})</td>
        </tr>
        <tr class="total">
            <td>Laba Bersih Sebelum Pajak</td>
            <td class="right">{{ number_format($income_statement['laba_bersih_sebelum_pajak'], 2) }}</td>
        </tr>
        <tr>
            <td>Beban Pajak Penghasilan</td>
            <td class="right">({{ number_format($income_statement['beban_pajak_penghasilan'], 2) }})</td>
        </tr>
        <tr class="total">
            <td>Laba Bersih Setelah Pajak</td>
            <td class="right">{{ number_format($income_statement['laba_bersih_setelah_pajak'], 2) }}</td>
        </tr>
    </table>

    <div class="page-break"></div>
    <h2>3. Laporan Posisi Keuangan (Neraca)</h2>
    <table>
        <tr>
            <td colspan="2"><strong>ASET LANCAR</strong></td>
        </tr>
        <tr>
            <td>Kas</td>
            <td class="right">{{ number_format($balance_sheet['kas'], 2) }}</td>
        </tr>
        <tr>
            <td>Bank</td>
            <td class="right">{{ number_format($balance_sheet['bank'], 2) }}</td>
        </tr>
        <tr>
            <td>Piutang Usaha</td>
            <td class="right">{{ number_format($balance_sheet['piutang_usaha'], 2) }}</td>
        </tr>
        <tr>
            <td>(-) Cadangan Kerugian Piutang</td>
            <td class="right">({{ number_format($balance_sheet['cadangan_kerugian_piutang'], 2) }})</td>
        </tr>
        <tr>
            <td>Persediaan Barang Dagang</td>
            <td class="right">{{ number_format($balance_sheet['persediaan_dagang'], 2) }}</td>
        </tr>
        <tr>
            <td>Persediaan Barang Konsinyasi</td>
            <td class="right">{{ number_format($balance_sheet['persediaan_konsinyasi'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Total Aset Lancar</td>
            <td class="right">{{ number_format($balance_sheet['total_aset_lancar'], 2) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>ASET TETAP</strong></td>
        </tr>
        @foreach ($balance_sheet['rincian_aset_tetap'] as $item)
            <tr>
                <td>{{ $item['nama'] }} (nilai buku bersih)</td>
                <td class="right">{{ number_format($item['nilai_buku'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>Total Aset Tetap</td>
            <td class="right">{{ number_format($balance_sheet['total_aset_tetap'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL ASET</td>
            <td class="right">{{ number_format($balance_sheet['total_aset'], 2) }}</td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="2"><strong>KEWAJIBAN</strong></td>
        </tr>
        @foreach ($balance_sheet['rincian_kewajiban_pendek'] as $item)
            <tr>
                <td>{{ $item['account']->nama_akun }}</td>
                <td class="right">{{ number_format($item['saldo'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td>Hutang Bank Jangka Panjang</td>
            <td class="right">{{ number_format($balance_sheet['total_kewajiban_panjang'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL KEWAJIBAN</td>
            <td class="right">{{ number_format($balance_sheet['total_kewajiban'], 2) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>EKUITAS</strong></td>
        </tr>
        <tr>
            <td>Modal Pemilik</td>
            <td class="right">{{ number_format($balance_sheet['modal_pemilik'], 2) }}</td>
        </tr>
        <tr>
            <td>Laba Ditahan</td>
            <td class="right">{{ number_format($balance_sheet['laba_ditahan'], 2) }}</td>
        </tr>
        <tr>
            <td>Laba (Rugi) Tahun Berjalan</td>
            <td class="right">{{ number_format($balance_sheet['laba_tahun_berjalan'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL EKUITAS</td>
            <td class="right">{{ number_format($balance_sheet['total_ekuitas'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL KEWAJIBAN + EKUITAS</td>
            <td class="right">{{ number_format($balance_sheet['total_kewajiban_ekuitas'], 2) }}</td>
        </tr>
    </table>

    <div class="page-break"></div>
    <h2>4. Laporan Perubahan Modal</h2>
    <table>
        <tr>
            <td>Modal Awal Periode</td>
            <td class="right">{{ number_format($equity_change['modal_awal_periode'], 2) }}</td>
        </tr>
        <tr>
            <td>+ Laba Bersih Periode Berjalan</td>
            <td class="right">{{ number_format($equity_change['laba_bersih_periode'], 2) }}</td>
        </tr>
        <tr>
            <td>- Prive/Penarikan Modal</td>
            <td class="right">({{ number_format($equity_change['prive_periode'], 2) }})</td>
        </tr>
        <tr class="total">
            <td>Modal Akhir Periode</td>
            <td class="right">{{ number_format($equity_change['modal_akhir_periode'], 2) }}</td>
        </tr>
    </table>

    <h2>5. Laporan Arus Kas</h2>
    <table>
        <tr>
            <td colspan="2"><strong>Arus Kas Operasi</strong></td>
        </tr>
        <tr>
            <td>Laba Bersih</td>
            <td class="right">{{ number_format($cash_flow['laba_bersih'], 2) }}</td>
        </tr>
        <tr>
            <td>+ Beban Penyusutan</td>
            <td class="right">{{ number_format($cash_flow['beban_penyusutan'], 2) }}</td>
        </tr>
        <tr>
            <td>+/- Perubahan Piutang Usaha</td>
            <td class="right">{{ number_format(bcmul($cash_flow['perubahan_piutang'], '-1', 2), 2) }}</td>
        </tr>
        <tr>
            <td>+/- Perubahan Persediaan</td>
            <td class="right">{{ number_format(bcmul($cash_flow['perubahan_persediaan'], '-1', 2), 2) }}</td>
        </tr>
        <tr>
            <td>+/- Perubahan Hutang Usaha</td>
            <td class="right">{{ number_format($cash_flow['perubahan_hutang_usaha'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Kas Bersih dari Aktivitas Operasi</td>
            <td class="right">{{ number_format($cash_flow['kas_bersih_operasi'], 2) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Arus Kas Investasi</strong></td>
        </tr>
        <tr>
            <td>- Pembelian Aset Tetap</td>
            <td class="right">({{ number_format($cash_flow['pembelian_aset_tetap'], 2) }})</td>
        </tr>
        <tr>
            <td>+ Penjualan Aset Tetap</td>
            <td class="right">{{ number_format($cash_flow['penjualan_aset_tetap'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Kas Bersih dari Aktivitas Investasi</td>
            <td class="right">{{ number_format($cash_flow['kas_bersih_investasi'], 2) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Arus Kas Pendanaan</strong></td>
        </tr>
        <tr>
            <td>+ Penerimaan Pinjaman Bank</td>
            <td class="right">{{ number_format($cash_flow['penerimaan_pinjaman'], 2) }}</td>
        </tr>
        <tr>
            <td>- Pembayaran Pokok Pinjaman</td>
            <td class="right">({{ number_format($cash_flow['pembayaran_pokok_pinjaman'], 2) }})</td>
        </tr>
        <tr>
            <td>- Prive</td>
            <td class="right">({{ number_format($cash_flow['prive'], 2) }})</td>
        </tr>
        <tr class="total">
            <td>Kas Bersih dari Aktivitas Pendanaan</td>
            <td class="right">{{ number_format($cash_flow['kas_bersih_pendanaan'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Kenaikan/Penurunan Kas Bersih</td>
            <td class="right">{{ number_format($cash_flow['kenaikan_penurunan_kas_bersih'], 2) }}</td>
        </tr>
        <tr>
            <td>Saldo Kas Awal</td>
            <td class="right">{{ number_format($cash_flow['saldo_kas_awal'], 2) }}</td>
        </tr>
        <tr class="total">
            <td>Saldo Kas Akhir</td>
            <td class="right">{{ number_format($cash_flow['saldo_kas_akhir'], 2) }}</td>
        </tr>
    </table>

    <div class="page-break"></div>
    <h2>6. Catatan atas Laporan Keuangan (CALK)</h2>
    <div style="white-space: pre-wrap; font-size: 11px;">
        {{ $financial_note->konten ?? 'Belum ada CALK untuk periode ini.' }}</div>
</body>

</html>

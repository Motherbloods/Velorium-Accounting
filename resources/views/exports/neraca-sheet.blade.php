<table>
    <tr>
        <td>Kas</td>
        <td>{{ $report['kas'] }}</td>
    </tr>
    <tr>
        <td>Bank</td>
        <td>{{ $report['bank'] }}</td>
    </tr>
    <tr>
        <td>Piutang Usaha</td>
        <td>{{ $report['piutang_usaha'] }}</td>
    </tr>
    <tr>
        <td>Cadangan Kerugian Piutang</td>
        <td>{{ $report['cadangan_kerugian_piutang'] }}</td>
    </tr>
    <tr>
        <td>Persediaan Barang Dagang</td>
        <td>{{ $report['persediaan_dagang'] }}</td>
    </tr>
    <tr>
        <td>Persediaan Barang Konsinyasi</td>
        <td>{{ $report['persediaan_konsinyasi'] }}</td>
    </tr>
    <tr>
        <td>Total Aset Lancar</td>
        <td>{{ $report['total_aset_lancar'] }}</td>
    </tr>
    @foreach ($report['rincian_aset_tetap'] as $item)
        <tr>
            <td>{{ $item['nama'] }}</td>
            <td>{{ $item['nilai_buku'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td>Total Aset Tetap</td>
        <td>{{ $report['total_aset_tetap'] }}</td>
    </tr>
    <tr>
        <td>TOTAL ASET</td>
        <td>{{ $report['total_aset'] }}</td>
    </tr>
    @foreach ($report['rincian_kewajiban_pendek'] as $item)
        <tr>
            <td>{{ $item['account']->nama_akun }}</td>
            <td>{{ $item['saldo'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td>Hutang Bank Jangka Panjang</td>
        <td>{{ $report['total_kewajiban_panjang'] }}</td>
    </tr>
    <tr>
        <td>TOTAL KEWAJIBAN</td>
        <td>{{ $report['total_kewajiban'] }}</td>
    </tr>
    <tr>
        <td>Modal Pemilik</td>
        <td>{{ $report['modal_pemilik'] }}</td>
    </tr>
    <tr>
        <td>Laba Ditahan</td>
        <td>{{ $report['laba_ditahan'] }}</td>
    </tr>
    <tr>
        <td>Laba (Rugi) Tahun Berjalan</td>
        <td>{{ $report['laba_tahun_berjalan'] }}</td>
    </tr>
    <tr>
        <td>TOTAL EKUITAS</td>
        <td>{{ $report['total_ekuitas'] }}</td>
    </tr>
    <tr>
        <td>TOTAL KEWAJIBAN + EKUITAS</td>
        <td>{{ $report['total_kewajiban_ekuitas'] }}</td>
    </tr>
</table>

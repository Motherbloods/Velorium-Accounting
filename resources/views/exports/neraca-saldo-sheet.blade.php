<table>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Akun</th>
            <th>Saldo Awal</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($trialBalance as $row)
            <tr>
                <td>{{ $row['account']->kode_akun }}</td>
                <td>{{ $row['account']->nama_akun }}</td>
                <td>{{ $row['saldo_awal'] }}</td>
                <td>{{ $row['total_debit'] }}</td>
                <td>{{ $row['total_kredit'] }}</td>
                <td>{{ $row['saldo_akhir'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

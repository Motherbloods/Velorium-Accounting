@extends('layouts.app')

@section('title', 'Buku Besar')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.general-ledger') }}"
            class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode</label>
                <select name="fiscal_period_id"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected(optional($selectedPeriod)->id === $period->id)>{{ $period->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Akun</label>
                <select name="coa_account_id"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected(optional($selectedAccount)->id === $account->id)>{{ $account->kode_akun }} —
                            {{ $account->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Tampilkan
            </button>
        </form>
    </x-card>

    @if ($ledger)
        <x-card class="shadow-md mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg font-semibold text-text">{{ $ledger['account']->kode_akun }} —
                        {{ $ledger['account']->nama_akun }}</p>
                    <p class="text-sm text-slate-500">Periode: {{ $selectedPeriod->nama_periode }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Saldo Awal</p>
                    <p class="text-lg font-semibold text-text">{{ number_format($ledger['saldo_awal'], 2) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="shadow-md p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">No. Bukti</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Debit</th>
                        <th class="px-4 py-3 text-right">Kredit</th>
                        <th class="px-4 py-3 text-right">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ledger['mutasi'] as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($row['tanggal'])->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 font-mono">
                                <a href="{{ route('journal.show', $row['journal_entry_id']) }}"
                                    class="text-primary">{{ $row['nomor_bukti'] }}</a>
                            </td>
                            <td class="px-4 py-3">{{ $row['keterangan'] }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['debit'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['kredit'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($row['saldo_berjalan'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-3 text-slate-400" colspan="6">Tidak ada mutasi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-semibold border-t border-slate-200 bg-slate-50">
                        <td class="px-4 py-3" colspan="5">Saldo Akhir</td>
                        <td class="px-4 py-3 text-right">{{ number_format($ledger['saldo_akhir'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-card>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection

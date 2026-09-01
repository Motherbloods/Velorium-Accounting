@extends('layouts.app')

@section('title', 'Neraca Saldo')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.trial-balance') }}"
            class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode</label>
                <select name="fiscal_period_id"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected(optional($selectedPeriod)->id === $period->id)>{{ $period->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Tampilkan
            </button>
        </form>
    </x-card>

    @if ($selectedPeriod)
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500">Periode: {{ $selectedPeriod->nama_periode }}</p>
            @if ($isBalanced)
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Balance</span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-error">Tidak Balance — periksa
                    jurnal</span>
            @endif
        </div>

        <x-card class="shadow-md p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Akun</th>
                        <th class="px-4 py-3 text-right">Saldo Awal</th>
                        <th class="px-4 py-3 text-right">Mutasi Debit</th>
                        <th class="px-4 py-3 text-right">Mutasi Kredit</th>
                        <th class="px-4 py-3 text-right">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($trialBalance as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono">{{ $row['account']->kode_akun }}</td>
                            <td class="px-4 py-3">{{ $row['account']->nama_akun }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['saldo_awal'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['total_debit'], 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($row['total_kredit'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($row['saldo_akhir'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-3 text-slate-400" colspan="6">Belum ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection

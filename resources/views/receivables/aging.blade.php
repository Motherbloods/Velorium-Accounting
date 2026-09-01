@extends('layouts.app')

@section('title', 'Analisis Umur Piutang')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden mb-6">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Kelompok Umur</th>
                    <th class="px-4 py-3 text-right">% Taksiran</th>
                    <th class="px-4 py-3 text-right">Saldo Piutang</th>
                    <th class="px-4 py-3 text-right">Taksiran Tak Tertagih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($buckets as $bucket)
                    <tr>
                        <td class="px-4 py-3">{{ $bucket['label'] }}</td>
                        <td class="px-4 py-3 text-right">{{ $bucket['persen'] }}%</td>
                        <td class="px-4 py-3 text-right">{{ number_format($bucket['total'], 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($bucket['taksiran'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold border-t border-slate-200 bg-slate-50">
                    <td class="px-4 py-3" colspan="3">Total Taksiran Tak Tertagih</td>
                    <td class="px-4 py-3 text-right">{{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </x-card>

    <x-card class="shadow-md bg-amber-50 border-l-4 border-l-warning max-w-md">
        <p class="text-sm font-medium text-accent mb-3">Catat Jurnal Cadangan Kerugian Piutang</p>
        <form method="POST" action="{{ route('receivables.record-allowance') }}" class="space-y-3">
            @csrf
            <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <button type="submit"
                class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Buat Jurnal (Draft)
            </button>
        </form>
    </x-card>
@endsection

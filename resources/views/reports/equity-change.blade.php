@extends('layouts.app')

@section('title', 'Laporan Perubahan Modal')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.equity-change') }}" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode</label>
                <select name="fiscal_period_id"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
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

    @if ($report)
        <x-card class="shadow-md max-w-lg">
            <p class="text-sm text-slate-500 mb-4">Periode: {{ $selectedPeriod->nama_periode }}</p>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2">Modal Awal Periode</td>
                        <td class="py-2 text-right">{{ number_format($report['modal_awal_periode'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2">+ Laba Bersih Periode Berjalan</td>
                        <td class="py-2 text-right">{{ number_format($report['laba_bersih_periode'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2">- Prive/Penarikan Modal</td>
                        <td class="py-2 text-right">({{ number_format($report['prive_periode'], 2) }})</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-emerald-100">
                        <td class="py-3 px-2">Modal Akhir Periode</td>
                        <td class="py-3 px-2 text-right">{{ number_format($report['modal_akhir_periode'], 2) }}</td>
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

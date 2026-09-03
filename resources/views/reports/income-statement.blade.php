@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.income-statement') }}" class="flex items-end gap-3">
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
        <x-card class="shadow-md max-w-2xl">
            <p class="text-sm text-slate-500 mb-4">Periode: {{ $selectedPeriod->nama_periode }}</p>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2">Pendapatan Penjualan</td>
                        <td class="py-2 text-right">{{ number_format($report['pendapatan_penjualan_bersih'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2">Pendapatan Penjualan Konsinyasi</td>
                        <td class="py-2 text-right">{{ number_format($report['pendapatan_konsinyasi'], 2) }}</td>
                    </tr>
                    <tr class="font-semibold bg-slate-50">
                        <td class="py-2 px-2">Total Pendapatan</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['total_pendapatan'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2">Beban Pokok Penjualan</td>
                        <td class="py-2 text-right">({{ number_format($report['hpp_bersih'], 2) }})</td>
                    </tr>
                    <tr class="font-semibold bg-emerald-50">
                        <td class="py-2 px-2">Laba Kotor</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['laba_kotor'], 2) }}</td>
                    </tr>

                    <tr>
                        <td class="pt-4 pb-1 font-medium text-slate-500" colspan="2">Beban Operasional</td>
                    </tr>
                    <tr>
                        <td class="py-2 pl-4">Beban Gaji</td>
                        <td class="py-2 text-right">{{ number_format($report['beban_gaji'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pl-4">Beban Penyusutan</td>
                        <td class="py-2 text-right">{{ number_format($report['beban_penyusutan'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pl-4">Beban Komisi Konsinyasi</td>
                        <td class="py-2 text-right">{{ number_format($report['beban_komisi_konsinyasi'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pl-4">Beban Kerugian Piutang</td>
                        <td class="py-2 text-right">{{ number_format($report['beban_kerugian_piutang'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 pl-4">Beban Operasional Lainnya</td>
                        <td class="py-2 text-right">{{ number_format($report['beban_operasional_lainnya'], 2) }}</td>
                    </tr>
                    <tr class="font-medium">
                        <td class="py-2 pl-4">Total Beban Operasional</td>
                        <td class="py-2 text-right">({{ number_format($report['total_beban_operasional'], 2) }})</td>
                    </tr>
                    <tr class="font-semibold bg-emerald-50">
                        <td class="py-2 px-2">Laba Operasional</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['laba_operasional'], 2) }}</td>
                    </tr>

                    <tr>
                        <td class="py-2">Pendapatan Lain-lain</td>
                        <td class="py-2 text-right">{{ number_format($report['pendapatan_lain_lain'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2">Beban Bunga</td>
                        <td class="py-2 text-right">({{ number_format($report['beban_bunga'], 2) }})</td>
                    </tr>
                    <tr class="font-semibold bg-blue-50">
                        <td class="py-2 px-2">Laba Bersih Sebelum Pajak</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['laba_bersih_sebelum_pajak'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-2">Beban Pajak Penghasilan</td>
                        <td class="py-2 text-right">({{ number_format($report['beban_pajak_penghasilan'], 2) }})</td>
                    </tr>
                    <tr class="font-bold bg-emerald-100">
                        <td class="py-3 px-2">Laba Bersih Setelah Pajak</td>
                        <td class="py-3 px-2 text-right">{{ number_format($report['laba_bersih_setelah_pajak'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </x-card>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection

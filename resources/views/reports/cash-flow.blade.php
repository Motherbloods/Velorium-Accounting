@extends('layouts.app')

@section('title', 'Laporan Arus Kas')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.cash-flow') }}" class="flex items-end gap-3">
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
            <p class="text-sm text-slate-500 mb-4">Periode: {{ $selectedPeriod->nama_periode }} (metode tidak langsung)</p>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="pt-2 pb-1 font-medium text-slate-500" colspan="2">Arus Kas Operasi</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">Laba Bersih</td>
                        <td class="py-1.5 text-right">{{ number_format($report['laba_bersih'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">+ Beban Penyusutan (non-kas)</td>
                        <td class="py-1.5 text-right">{{ number_format($report['beban_penyusutan'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">+/- Perubahan Piutang Usaha</td>
                        <td class="py-1.5 text-right">{{ number_format(bcmul($report['perubahan_piutang'], '-1', 2), 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">+/- Perubahan Persediaan</td>
                        <td class="py-1.5 text-right">
                            {{ number_format(bcmul($report['perubahan_persediaan'], '-1', 2), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">+/- Perubahan Hutang Usaha</td>
                        <td class="py-1.5 text-right">{{ number_format($report['perubahan_hutang_usaha'], 2) }}</td>
                    </tr>
                    <tr class="font-semibold bg-slate-50">
                        <td class="py-2 px-2">Kas Bersih dari Aktivitas Operasi</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['kas_bersih_operasi'], 2) }}</td>
                    </tr>

                    <tr>
                        <td class="pt-4 pb-1 font-medium text-slate-500" colspan="2">Arus Kas Investasi</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">- Pembelian Aset Tetap</td>
                        <td class="py-1.5 text-right">({{ number_format($report['pembelian_aset_tetap'], 2) }})</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">+ Penjualan Aset Tetap</td>
                        <td class="py-1.5 text-right">{{ number_format($report['penjualan_aset_tetap'], 2) }}</td>
                    </tr>
                    <tr class="font-semibold bg-slate-50">
                        <td class="py-2 px-2">Kas Bersih dari Aktivitas Investasi</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['kas_bersih_investasi'], 2) }}</td>
                    </tr>

                    <tr>
                        <td class="pt-4 pb-1 font-medium text-slate-500" colspan="2">Arus Kas Pendanaan</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">+ Penerimaan Pinjaman Bank</td>
                        <td class="py-1.5 text-right">{{ number_format($report['penerimaan_pinjaman'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">- Pembayaran Pokok Pinjaman</td>
                        <td class="py-1.5 text-right">({{ number_format($report['pembayaran_pokok_pinjaman'], 2) }})</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pl-4">- Prive</td>
                        <td class="py-1.5 text-right">({{ number_format($report['prive'], 2) }})</td>
                    </tr>
                    <tr class="font-semibold bg-slate-50">
                        <td class="py-2 px-2">Kas Bersih dari Aktivitas Pendanaan</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['kas_bersih_pendanaan'], 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-semibold bg-blue-50">
                        <td class="py-2 px-2">Kenaikan/Penurunan Kas Bersih</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['kenaikan_penurunan_kas_bersih'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-2">Saldo Kas Awal</td>
                        <td class="py-2 px-2 text-right">{{ number_format($report['saldo_kas_awal'], 2) }}</td>
                    </tr>
                    <tr class="font-bold bg-emerald-100">
                        <td class="py-3 px-2">Saldo Kas Akhir</td>
                        <td class="py-3 px-2 text-right">{{ number_format($report['saldo_kas_akhir'], 2) }}</td>
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

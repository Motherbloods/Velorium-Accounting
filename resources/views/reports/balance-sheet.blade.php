@extends('layouts.app')

@section('title', 'Laporan Posisi Keuangan (Neraca)')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.balance-sheet') }}" class="flex items-end gap-3">
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
        <div class="flex items-center justify-between mb-4 max-w-4xl">
            <p class="text-sm text-slate-500">Per {{ $selectedPeriod->tanggal_selesai->format('d M Y') }}</p>
            @if ($report['is_balanced'])
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Seimbang</span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-error">Tidak Seimbang — periksa
                    jurnal</span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
            <x-card class="shadow-md">
                <p class="text-sm font-semibold text-accent mb-3">ASET</p>

                <p class="text-xs font-medium text-slate-500 mb-1">Aset Lancar</p>
                <table class="w-full text-sm mb-3">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-1.5">Kas</td>
                            <td class="py-1.5 text-right">{{ number_format($report['kas'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Bank</td>
                            <td class="py-1.5 text-right">{{ number_format($report['bank'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Piutang Usaha</td>
                            <td class="py-1.5 text-right">{{ number_format($report['piutang_usaha'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">(-) Cadangan Kerugian Piutang</td>
                            <td class="py-1.5 text-right">({{ number_format($report['cadangan_kerugian_piutang'], 2) }})
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Persediaan Barang Dagang</td>
                            <td class="py-1.5 text-right">{{ number_format($report['persediaan_dagang'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Persediaan Barang Konsinyasi</td>
                            <td class="py-1.5 text-right">{{ number_format($report['persediaan_konsinyasi'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">PPN Masukan</td>
                            <td class="py-1.5 text-right">{{ number_format($report['ppn_masukan'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Beban Dibayar Dimuka</td>
                            <td class="py-1.5 text-right">{{ number_format($report['beban_dibayar_dimuka'], 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold bg-slate-50">
                            <td class="py-2 px-2">Total Aset Lancar</td>
                            <td class="py-2 px-2 text-right">{{ number_format($report['total_aset_lancar'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="text-xs font-medium text-slate-500 mb-1">Aset Tetap</p>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($report['rincian_aset_tetap'] as $item)
                            <tr>
                                <td class="py-1.5">{{ $item['nama'] }} (nilai buku bersih)</td>
                                <td class="py-1.5 text-right">{{ number_format($item['nilai_buku'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold bg-slate-50">
                            <td class="py-2 px-2">Total Aset Tetap</td>
                            <td class="py-2 px-2 text-right">{{ number_format($report['total_aset_tetap'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-3 font-bold bg-blue-100 rounded-lg px-3 py-2 flex justify-between">
                    <span>TOTAL ASET</span>
                    <span>{{ number_format($report['total_aset'], 2) }}</span>
                </div>
            </x-card>

            <x-card class="shadow-md">
                <p class="text-sm font-semibold text-accent mb-3">KEWAJIBAN</p>

                <p class="text-xs font-medium text-slate-500 mb-1">Kewajiban Jangka Pendek</p>
                <table class="w-full text-sm mb-3">
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($report['rincian_kewajiban_pendek'] as $item)
                            <tr>
                                <td class="py-1.5">{{ $item['account']->nama_akun }}</td>
                                <td class="py-1.5 text-right">{{ number_format($item['saldo'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold bg-slate-50">
                            <td class="py-2 px-2">Total Kewajiban Jangka Pendek</td>
                            <td class="py-2 px-2 text-right">{{ number_format($report['total_kewajiban_pendek'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="text-xs font-medium text-slate-500 mb-1">Kewajiban Jangka Panjang</p>
                <table class="w-full text-sm">
                    <tbody>
                        <tr>
                            <td class="py-1.5">Hutang Bank Jangka Panjang</td>
                            <td class="py-1.5 text-right">{{ number_format($report['total_kewajiban_panjang'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3 font-semibold bg-slate-50 rounded-lg px-3 py-2 flex justify-between">
                    <span>TOTAL KEWAJIBAN</span>
                    <span>{{ number_format($report['total_kewajiban'], 2) }}</span>
                </div>

                <p class="text-sm font-semibold text-accent mt-6 mb-3">EKUITAS</p>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-1.5">Modal Pemilik</td>
                            <td class="py-1.5 text-right">{{ number_format($report['modal_pemilik'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Laba Ditahan</td>
                            <td class="py-1.5 text-right">{{ number_format($report['laba_ditahan'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5">Laba (Rugi) Tahun Berjalan</td>
                            <td class="py-1.5 text-right">{{ number_format($report['laba_tahun_berjalan'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3 font-semibold bg-slate-50 rounded-lg px-3 py-2 flex justify-between">
                    <span>TOTAL EKUITAS</span>
                    <span>{{ number_format($report['total_ekuitas'], 2) }}</span>
                </div>

                <div class="mt-3 font-bold bg-blue-100 rounded-lg px-3 py-2 flex justify-between">
                    <span>TOTAL KEWAJIBAN + EKUITAS</span>
                    <span>{{ number_format($report['total_kewajiban_ekuitas'], 2) }}</span>
                </div>
            </x-card>
        </div>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection

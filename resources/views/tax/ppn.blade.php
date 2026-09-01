@extends('layouts.app')

@section('title', 'PPN Keluaran/Masukan')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('tax.ppn') }}" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode Pajak</label>
                <input type="month" name="periode" value="{{ $periodePajak }}"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Tampilkan
            </button>
        </form>
    </x-card>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card class="bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm text-slate-500">Total PPN Keluaran</p>
            <p class="text-lg font-semibold text-text mt-1">{{ number_format($totalKeluaran, 2) }}</p>
        </x-card>
        <x-card class="bg-amber-50 border-l-4 border-l-warning">
            <p class="text-sm text-slate-500">Total PPN Masukan</p>
            <p class="text-lg font-semibold text-text mt-1">{{ number_format($totalMasukan, 2) }}</p>
        </x-card>
        <x-card
            class="{{ bccomp($selisih, '0', 2) > 0 ? 'bg-red-50 border-l-4 border-l-error' : 'bg-emerald-50 border-l-4 border-l-success' }}">
            <p class="text-sm text-slate-500">
                {{ bccomp($selisih, '0', 2) > 0 ? 'Kurang Bayar' : 'Lebih Bayar (Dibawa ke Periode Berikutnya)' }}</p>
            <p class="text-lg font-semibold text-text mt-1">{{ number_format(abs($selisih), 2) }}</p>
        </x-card>
    </div>

    <x-card class="shadow-md p-0 overflow-hidden mb-6">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3 text-right">DPP</th>
                    <th class="px-4 py-3 text-right">Tarif</th>
                    <th class="px-4 py-3 text-right">Jumlah Pajak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($transactions as $tx)
                    <tr>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $tx->tipe === 'ppn_keluaran' ? 'bg-blue-100 text-accent' : 'bg-amber-100 text-amber-700' }}">
                                {{ $tx->tipe === 'ppn_keluaran' ? 'PPN Keluaran' : 'PPN Masukan' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format($tx->dpp, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $tx->tarif_persen }}%</td>
                        <td class="px-4 py-3 text-right">{{ number_format($tx->jumlah_pajak, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-3 text-slate-400" colspan="4">Belum ada transaksi PPN pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <form method="POST" action="{{ route('tax.ppn.setor') }}">
        @csrf
        <input type="hidden" name="periode" value="{{ $periodePajak }}">
        <button type="submit"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Buat Jurnal Penyetoran/Pelaporan (Draft)
        </button>
    </form>
@endsection

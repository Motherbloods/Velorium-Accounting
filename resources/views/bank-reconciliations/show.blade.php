@extends('layouts.app')

@section('title', 'Detail Rekonsiliasi Bank')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <x-card class="shadow-md mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-lg font-semibold text-text">{{ $reconciliation->bankAccount->nama_bank }} —
                    {{ $reconciliation->periode->format('M Y') }}</p>
            </div>
            <x-badge-status :status="$reconciliation->status === 'selesai' ? 'lunas' : 'draft'" class="text-sm px-3 py-1" />
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm font-medium text-accent mb-2">Sisi Buku</p>
                <p class="text-xs text-slate-500">Saldo Kas per Buku: {{ number_format($reconciliation->saldo_buku, 2) }}
                </p>
                <p class="text-xs text-slate-500">Saldo Kas Disesuaikan: <span
                        class="font-semibold text-text">{{ number_format($reconciliation->saldo_disesuaikan_buku ?? 0, 2) }}</span>
                </p>
            </div>
            <div class="bg-amber-50 rounded-lg p-4">
                <p class="text-sm font-medium text-accent mb-2">Sisi Bank</p>
                <p class="text-xs text-slate-500">Saldo per Rekening Koran:
                    {{ number_format($reconciliation->saldo_rekening_koran, 2) }}</p>
                <p class="text-xs text-slate-500">Saldo Kas Disesuaikan: <span
                        class="font-semibold text-text">{{ number_format($reconciliation->saldo_disesuaikan_bank ?? 0, 2) }}</span>
                </p>
            </div>
        </div>

        @if ($reconciliation->status === 'draft')
            <form method="POST" action="{{ route('bank-reconciliations.complete', $reconciliation) }}" class="mt-4">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                    Selesaikan Rekonsiliasi
                </button>
            </form>
        @endif
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card class="shadow-sm">
            <p class="text-sm font-medium text-text mb-3">Tambah Item</p>
            <form method="POST" action="{{ route('bank-reconciliations.items.store', $reconciliation) }}"
                class="space-y-3">
                @csrf
                <select name="kategori" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="sisi_buku">Sisi Buku</option>
                    <option value="sisi_bank">Sisi Bank</option>
                </select>
                <select name="jenis" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="jasa_giro">Jasa Giro (sisi buku)</option>
                    <option value="biaya_admin">Biaya Admin (sisi buku)</option>
                    <option value="setoran_dalam_perjalanan">Setoran Dalam Perjalanan (sisi bank)</option>
                    <option value="cek_beredar">Cek/Transfer Beredar (sisi bank)</option>
                </select>
                <input type="text" name="keterangan" placeholder="Keterangan"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="number" step="0.01" min="0.01" name="jumlah" required placeholder="Jumlah"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Tambah
                </button>
            </form>
        </x-card>

        <x-card class="shadow-sm p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-3 py-2">Kategori</th>
                        <th class="px-3 py-2">Jenis</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($reconciliation->items as $item)
                        <tr>
                            <td class="px-3 py-2">{{ $item->kategori === 'sisi_buku' ? 'Buku' : 'Bank' }}</td>
                            <td class="px-3 py-2">{{ str_replace('_', ' ', $item->jenis) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->jumlah, 2) }}</td>
                            <td class="px-3 py-2 text-right">
                                @if ($item->kategori === 'sisi_buku')
                                    @if ($item->sudah_diposting)
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Diposting</span>
                                    @else
                                        <form method="POST" action="{{ route('bank-reconciliations.items.post', $item) }}"
                                            class="flex items-center justify-end gap-2">
                                            @csrf
                                            <select name="coa_lawan_id" required
                                                class="px-2 py-1 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary">
                                                @foreach ($coaOptions as $coa)
                                                    <option value="{{ $coa->id }}">{{ $coa->kode_akun }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="text-primary text-xs font-medium">Posting</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    </div>
@endsection

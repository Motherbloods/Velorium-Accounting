@extends('layouts.app')

@section('title', 'Aset Tetap')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar aset tetap</p>
        <a href="{{ route('fixed-assets.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Aset
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Aset</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3 text-right">Harga Perolehan</th>
                    <th class="px-4 py-3 text-right">Akumulasi Penyusutan</th>
                    <th class="px-4 py-3 text-right">Nilai Buku</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($assets as $asset)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $asset->kode_aset }}</td>
                        <td class="px-4 py-3">{{ $asset->nama_aset }}</td>
                        <td class="px-4 py-3">
                            {{ $asset->metode_penyusutan === 'garis_lurus' ? 'Garis Lurus' : 'Saldo Menurun Ganda' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($asset->harga_perolehan, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($asset->akumulasi_penyusutan, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($asset->nilai_buku, 2) }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $asset->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('fixed-assets.show', $asset) }}"
                                class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $assets->links() }}
    </div>
@endsection

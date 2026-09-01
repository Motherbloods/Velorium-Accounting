@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Kode Produk</label>
                <input type="text" name="kode_produk" value="{{ old('kode_produk') }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Nama Produk</label>
                <input type="text" name="nama_produk" value="{{ old('nama_produk') }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Satuan</label>
                <input type="text" name="satuan" value="{{ old('satuan') }}" required placeholder="pcs, kg, box, dll"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Harga Beli</label>
                    <input type="number" step="0.01" min="0" name="harga_beli" value="{{ old('harga_beli', 0) }}"
                        required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Harga Jual</label>
                    <input type="number" step="0.01" min="0" name="harga_jual" value="{{ old('harga_jual', 0) }}"
                        required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Metode Penilaian Persediaan</label>
                <select name="metode_penilaian" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="rata_rata" @selected(old('metode_penilaian') === 'rata_rata')>Rata-rata Tertimbang</option>
                    <option value="fifo" @selected(old('metode_penilaian') === 'fifo')>FIFO</option>
                </select>
                <p class="text-xs text-slate-400 mt-1">Metode ini akan terkunci setelah produk memiliki transaksi
                    persediaan.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('products.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

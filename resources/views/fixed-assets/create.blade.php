@extends('layouts.app')

@section('title', 'Tambah Aset Tetap')

@section('content')
    <x-card class="shadow-md max-w-2xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('fixed-assets.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Kode Aset</label>
                    <input type="text" name="kode_aset" value="{{ old('kode_aset') }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Nama Aset</label>
                    <input type="text" name="nama_aset" value="{{ old('nama_aset') }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Akun Aset Tetap</label>
                    <select name="coa_aset_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($coaAsetOptions as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Akun Akumulasi Penyusutan</label>
                    <select name="coa_akumulasi_penyusutan_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($coaAkumulasiOptions as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Akun Pembayaran</label>
                <select name="coa_pembayaran_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($coaPembayaranOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tanggal Perolehan</label>
                    <input type="date" name="tanggal_perolehan"
                        value="{{ old('tanggal_perolehan', now()->toDateString()) }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Harga Perolehan</label>
                    <input type="number" step="0.01" min="0.01" name="harga_perolehan"
                        value="{{ old('harga_perolehan') }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Nilai Residu</label>
                    <input type="number" step="0.01" min="0" name="nilai_residu"
                        value="{{ old('nilai_residu', 0) }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Umur Manfaat (tahun)</label>
                    <input type="number" min="1" name="umur_manfaat_tahun" value="{{ old('umur_manfaat_tahun') }}"
                        required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Umur Manfaat Fiskal (opsional)</label>
                    <input type="number" min="1" name="umur_manfaat_fiskal_tahun"
                        value="{{ old('umur_manfaat_fiskal_tahun') }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Metode Penyusutan</label>
                <select name="metode_penyusutan" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="garis_lurus">Garis Lurus</option>
                    <option value="saldo_menurun_ganda">Saldo Menurun Ganda</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('fixed-assets.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

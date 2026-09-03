@extends('layouts.app')

@section('title', 'Tambah Biaya Dibayar Dimuka')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('adjusting-entries.prepaid.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Nama</label>
                <input type="text" name="nama" placeholder="Contoh: Sewa Gudang 1 Tahun" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Akun Aset (Dibayar Dimuka)</label>
                    <select name="coa_aset_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($coaAsetOptions as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Akun Beban Terkait</label>
                    <select name="coa_beban_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($coaBebanOptions as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Akun Kas/Bank Sumber Pembayaran</label>
                <select name="coa_kas_bank_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($coaKasBankOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" value="{{ now()->toDateString() }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Total Dibayar</label>
                    <input type="number" step="0.01" min="0.01" name="total_dibayar" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Cakupan (bulan)</label>
                    <input type="number" min="1" name="jumlah_bulan_cakupan" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('adjusting-entries.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

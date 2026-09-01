@extends('layouts.app')

@section('title', 'Catat Transaksi Kas/Bank')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('cash.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tipe</label>
                    <select name="tipe" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="masuk">Kas Masuk</option>
                        <option value="keluar">Kas Keluar</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Akun Kas/Bank</label>
                <select name="coa_kas_bank_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($kasBankAccounts as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Akun Lawan</label>
                <select name="coa_lawan_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($accounts as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Jumlah</label>
                <input type="number" step="0.01" min="0.01" name="jumlah" value="{{ old('jumlah') }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('cash.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

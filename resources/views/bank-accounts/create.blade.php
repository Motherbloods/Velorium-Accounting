@extends('layouts.app')

@section('title', 'Tambah Rekening Bank')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('bank-accounts.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Nama Bank</label>
                <input type="text" name="nama_bank" value="{{ old('nama_bank') }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">No. Rekening</label>
                <input type="text" name="no_rekening" value="{{ old('no_rekening') }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Akun COA (Bank)</label>
                <select name="coa_account_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($coaOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('bank-accounts.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

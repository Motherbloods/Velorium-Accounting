@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-text mb-1">Kode Supplier</label>
                <input type="text" value="{{ $supplier->kode_supplier }}" disabled
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $supplier->nama) }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Alamat</label>
                <textarea name="alamat"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('alamat', $supplier->alamat) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $supplier->telepon) }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $supplier->npwp) }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('suppliers.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

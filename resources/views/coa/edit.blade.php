@extends('layouts.app')

@section('title', 'Edit Akun')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('coa.update', $account) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-text mb-1">Kode Akun</label>
                <input type="text" value="{{ $account->kode_akun }}" disabled
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Nama Akun</label>
                <input type="text" name="nama_akun" value="{{ old('nama_akun', $account->nama_akun) }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Induk Akun</label>
                <select name="parent_id"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Tanpa Induk —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $account->parent_id) == $parent->id)>{{ $parent->kode_akun }} —
                            {{ $parent->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tipe Akun</label>
                    <select name="tipe_akun" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach (['aset', 'kewajiban', 'modal', 'pendapatan', 'beban'] as $tipe)
                            <option value="{{ $tipe }}" @selected(old('tipe_akun', $account->tipe_akun) === $tipe)>{{ ucfirst($tipe) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Saldo Normal</label>
                    <select name="saldo_normal" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="debit" @selected(old('saldo_normal', $account->saldo_normal) === 'debit')>Debit</option>
                        <option value="kredit" @selected(old('saldo_normal', $account->saldo_normal) === 'kredit')>Kredit</option>
                    </select>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_postable" value="1" @checked(old('is_postable', $account->is_postable))
                    class="rounded border-slate-300">
                Boleh dijadikan target posting jurnal
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active))
                    class="rounded border-slate-300">
                Aktif
            </label>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('coa.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

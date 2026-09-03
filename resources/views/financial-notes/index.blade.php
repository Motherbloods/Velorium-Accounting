@extends('layouts.app')

@section('title', 'CALK')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('financial-notes.index') }}" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode</label>
                <select name="fiscal_period_id"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected(optional($fiscalPeriod)->id === $period->id)>{{ $period->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Tampilkan
            </button>
        </form>
    </x-card>

    @if ($fiscalPeriod)
        <x-card class="shadow-md">
            <p class="text-sm text-slate-500 mb-4">
                Catatan atas Laporan Keuangan — {{ $fiscalPeriod->nama_periode }}
                @if (!$fiscalPeriod->isOpen())
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">Periode
                        ditutup — hanya bisa dilihat</span>
                @endif
            </p>

            <form method="POST" action="{{ route('financial-notes.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="fiscal_period_id" value="{{ $fiscalPeriod->id }}">
                <textarea name="konten" rows="16" @disabled(!$fiscalPeriod->isOpen())
                    placeholder="Kebijakan akuntansi yang dipakai (dasar akrual, metode penyusutan, metode penilaian persediaan), rincian akun-akun penting, dan informasi tambahan lainnya..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary font-mono">{{ old('konten', $note->konten ?? '') }}</textarea>
                @if ($fiscalPeriod->isOpen())
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                        Simpan CALK
                    </button>
                @endif
            </form>
        </x-card>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection

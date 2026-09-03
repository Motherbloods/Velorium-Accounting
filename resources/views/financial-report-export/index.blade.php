@extends('layouts.app')

@section('title', 'Ekspor Laporan Keuangan Lengkap')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('financial-report-export.index') }}" class="flex items-end gap-3">
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
                Pilih
            </button>
        </form>
    </x-card>

    @if ($fiscalPeriod)
        <x-card class="shadow-md max-w-lg">
            <p class="text-sm font-medium text-text mb-2">{{ $fiscalPeriod->nama_periode }}</p>
            <p class="text-sm text-slate-500 mb-6">
                Bundel lengkap: Neraca Saldo → Laba Rugi → Neraca → Perubahan Modal → Arus Kas → CALK
            </p>
            <div class="flex gap-3">
                <a href="{{ route('financial-report-export.pdf', $fiscalPeriod) }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-error text-white text-sm font-medium shadow-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Unduh PDF
                </a>
                <a href="{{ route('financial-report-export.excel', $fiscalPeriod) }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    Unduh Excel
                </a>
            </div>
        </x-card>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Jurnal Penyesuaian')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="flex gap-3 mb-6">
        <a href="{{ route('adjusting-entries.prepaid.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            + Biaya Dibayar Dimuka
        </a>
        <a href="{{ route('adjusting-entries.unearned.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            + Pendapatan Diterima Dimuka
        </a>
        <a href="{{ route('adjusting-entries.accrued.create') }}"
            class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
            Catat Akrual Manual
        </a>
    </div>

    <x-card class="shadow-md p-0 overflow-hidden mb-6">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
            <p class="text-sm font-medium text-text">Biaya Dibayar Dimuka</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3 text-right">Total Dibayar</th>
                    <th class="px-4 py-3 text-right">Alokasi/Bulan</th>
                    <th class="px-4 py-3 text-right">Sisa Belum Diakui</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($prepaidExpenses as $prepaid)
                    <tr>
                        <td class="px-4 py-3">{{ $prepaid->nama }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($prepaid->total_dibayar, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($prepaid->alokasiBulanan(), 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($prepaid->sisa_belum_diakui, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($prepaid->sisa_belum_diakui > 0)
                                <form method="POST" action="{{ route('adjusting-entries.prepaid.run', $prepaid) }}"
                                    class="flex items-center justify-end gap-2">
                                    @csrf
                                    <input type="month" name="periode_input" required
                                        class="px-2 py-1 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary"
                                        onchange="this.form.periode.value = this.value + '-01'">
                                    <input type="hidden" name="periode">
                                    <button type="submit" class="text-primary text-xs font-medium">Jalankan</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">Selesai diakui</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-3 text-slate-400" colspan="5">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-card class="shadow-md p-0 overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
            <p class="text-sm font-medium text-text">Pendapatan Diterima Dimuka</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3 text-right">Total Diterima</th>
                    <th class="px-4 py-3 text-right">Alokasi/Bulan</th>
                    <th class="px-4 py-3 text-right">Sisa Belum Diakui</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($unearnedRevenues as $unearned)
                    <tr>
                        <td class="px-4 py-3">{{ $unearned->nama }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($unearned->total_diterima, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($unearned->alokasiBulanan(), 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($unearned->sisa_belum_diakui, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($unearned->sisa_belum_diakui > 0)
                                <form method="POST" action="{{ route('adjusting-entries.unearned.run', $unearned) }}"
                                    class="flex items-center justify-end gap-2">
                                    @csrf
                                    <input type="month" name="periode_input" required
                                        class="px-2 py-1 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary"
                                        onchange="this.form.periode.value = this.value + '-01'">
                                    <input type="hidden" name="periode">
                                    <button type="submit" class="text-primary text-xs font-medium">Jalankan</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">Selesai diakui</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-3 text-slate-400" colspan="5">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
@endsection

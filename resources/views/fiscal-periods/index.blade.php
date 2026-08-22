@extends('layouts.app')

@section('title', 'Periode Akuntansi')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm font-medium text-accent mb-4">Tambah Periode</p>
            <form method="POST" action="{{ route('fiscal-periods.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Periode</label>
                    <input type="text" name="nama_periode" required placeholder="Contoh: Januari 2026"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required max="{{ now()->toDateString() }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
            </form>
        </x-card>

        <div class="lg:col-span-2">
            <x-card class="shadow-md p-0 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nama Periode</th>
                            <th class="px-4 py-3">Rentang Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($periods as $period)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $period->nama_periode }}</td>
                                <td class="px-4 py-3">{{ $period->tanggal_mulai->format('d M Y') }} –
                                    {{ $period->tanggal_selesai->format('d M Y') }}</td>
                                <td class="px-4 py-3"><x-badge-status :status="$period->status" /></td>
                                <td class="px-4 py-3 text-right">
                                    @if ($period->status === 'open')
                                        <form method="POST" action="{{ route('fiscal-periods.close', $period) }}"
                                            onsubmit="return confirm('Tutup periode ini?')">
                                            @csrf
                                            <button type="submit" class="text-error text-sm font-medium">Tutup</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
@endsection

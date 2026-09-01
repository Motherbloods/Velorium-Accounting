@extends('layouts.app')

@section('title', 'Piutang')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('receivables.aging') }}" class="text-sm text-primary font-medium">Lihat Analisis Umur Piutang →</a>
        <a href="{{ route('receivables.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Piutang Manual
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">No. Invoice</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Jatuh Tempo</th>
                    <th class="px-4 py-3 text-right">Total Tagihan</th>
                    <th class="px-4 py-3 text-right">Sisa Piutang</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($receivables as $receivable)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $receivable->nomor_invoice }}</td>
                        <td class="px-4 py-3">{{ $receivable->customer->nama }}</td>
                        <td class="px-4 py-3">{{ $receivable->tanggal_jatuh_tempo->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($receivable->total_tagihan, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($receivable->sisa_piutang, 2) }}</td>
                        <td class="px-4 py-3">
                            <x-badge-status :status="$receivable->isOverdue() ? 'overdue' : $receivable->status" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('receivables.show', $receivable) }}"
                                class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $receivables->links() }}
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Detail Piutang')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="shadow-md lg:col-span-2">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-lg font-semibold text-text">{{ $receivable->nomor_invoice }}</p>
                    <p class="text-sm text-slate-500">{{ $receivable->customer->nama }} — Jatuh tempo
                        {{ $receivable->tanggal_jatuh_tempo->format('d M Y') }}</p>
                </div>
                <x-badge-status :status="$receivable->isOverdue() ? 'overdue' : $receivable->status" class="text-sm px-3 py-1" />
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Total Tagihan</p>
                    <p class="text-lg font-semibold text-text">{{ number_format($receivable->total_tagihan, 2) }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Sisa Piutang</p>
                    <p class="text-lg font-semibold text-text">{{ number_format($receivable->sisa_piutang, 2) }}</p>
                </div>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-3 py-2">Tanggal Bayar</th>
                        <th class="px-3 py-2">Akun</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($receivable->payments as $payment)
                        <tr>
                            <td class="px-3 py-2">{{ $payment->tanggal_bayar->format('d M Y') }}</td>
                            <td class="px-3 py-2">{{ $payment->coaKasBank->nama_akun }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($payment->jumlah_bayar, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-2 text-slate-400" colspan="3">Belum ada pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        @if ($receivable->status !== 'lunas')
            <x-card class="shadow-md bg-emerald-50 border-l-4 border-l-success">
                <p class="text-sm font-medium text-emerald-700 mb-4">Catat Pembayaran</p>
                <form method="POST" action="{{ route('receivables.pay', $receivable) }}" class="space-y-3">
                    @csrf
                    <input type="date" name="tanggal_bayar" value="{{ now()->toDateString() }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <input type="number" step="0.01" min="0.01" max="{{ $receivable->sisa_piutang }}"
                        name="jumlah_bayar" required placeholder="Jumlah bayar"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <select name="coa_kas_bank_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($coaKasBankOptions as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="w-full py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                        Simpan Pembayaran
                    </button>
                </form>
            </x-card>
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Kas & Bank')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Riwayat transaksi kas/bank</p>
        <a href="{{ route('cash.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Catat Transaksi
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">No. Bukti</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Akun Kas/Bank</th>
                    <th class="px-4 py-3">Akun Lawan</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($transactions as $tx)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $tx->nomor_bukti }}</td>
                        <td class="px-4 py-3">{{ $tx->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $tx->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-error' }}">
                                {{ ucfirst($tx->tipe) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $tx->coaKasBank->nama_akun }}</td>
                        <td class="px-4 py-3">{{ $tx->coaLawan->nama_akun }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($tx->jumlah, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
@endsection

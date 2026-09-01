@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar transaksi penjualan</p>
        <a href="{{ route('sales.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Transaksi Baru
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">No. Transaksi</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $sale->nomor_transaksi }}</td>
                        <td class="px-4 py-3">{{ $sale->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $sale->customer->nama }}</td>
                        <td class="px-4 py-3 capitalize">{{ $sale->tipe }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($sale->total, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
@endsection

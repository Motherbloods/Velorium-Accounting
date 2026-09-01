@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar transaksi pembelian</p>
        <a href="{{ route('purchases.create') }}"
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
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($purchases as $purchase)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $purchase->nomor_transaksi }}</td>
                        <td class="px-4 py-3">{{ $purchase->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $purchase->supplier->nama }}</td>
                        <td class="px-4 py-3 capitalize">{{ $purchase->tipe }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($purchase->total, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('purchases.show', $purchase) }}"
                                class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $purchases->links() }}
    </div>
@endsection

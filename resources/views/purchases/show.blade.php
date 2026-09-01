@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-lg font-semibold text-text">{{ $purchase->nomor_transaksi }}</p>
                <p class="text-sm text-slate-500">{{ $purchase->supplier->nama }} — {{ $purchase->tanggal->format('d M Y') }}
                </p>
            </div>
            <span
                class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-accent capitalize">{{ $purchase->tipe }}</span>
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-3 py-2">Produk</th>
                    <th class="px-3 py-2 text-right">Qty</th>
                    <th class="px-3 py-2 text-right">Harga Satuan</th>
                    <th class="px-3 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($purchase->items as $item)
                    <tr>
                        <td class="px-3 py-2">{{ $item->product->nama_produk }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->qty }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->harga_satuan, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="space-y-1">
                <div class="flex justify-between"><span
                        class="text-slate-500">Subtotal</span><span>{{ number_format($purchase->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Diskon
                        Dagang</span><span>{{ number_format($purchase->diskon_dagang, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">DPP
                        PPN</span><span>{{ number_format($purchase->dpp_ppn, 2) }}</span></div>
                <div class="flex justify-between"><span
                        class="text-slate-500">PPN</span><span>{{ number_format($purchase->ppn, 2) }}</span></div>
                <div class="flex justify-between font-semibold border-t border-slate-200 pt-1">
                    <span>Total</span><span>{{ number_format($purchase->total, 2) }}</span></div>
            </div>
            @if ($purchase->payable)
                <div class="bg-amber-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Hutang Terkait</p>
                    <a href="{{ route('payables.show', $purchase->payable) }}"
                        class="text-primary font-medium">{{ $purchase->payable->nomor_hutang }} — Sisa
                        {{ number_format($purchase->payable->sisa_hutang, 2) }}</a>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('purchases.return.create', $purchase) }}"
                class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                Buat Retur
            </a>
        </div>
    </x-card>

    @if ($purchase->returns->isNotEmpty())
        <x-card class="shadow-sm p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal Retur</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($purchase->returns as $return)
                        <tr>
                            <td class="px-4 py-3">{{ $return->tanggal->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $return->keterangan }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($return->jumlah, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    @endif
@endsection

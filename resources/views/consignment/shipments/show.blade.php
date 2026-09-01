@extends('layouts.app')

@section('title', 'Detail Pengiriman Konsinyasi')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-lg font-semibold text-text">{{ $shipment->nomor_konsinyasi }}</p>
                <p class="text-sm text-slate-500">{{ $shipment->consignee->nama }} — dikirim
                    {{ $shipment->tanggal_kirim->format('d M Y') }}</p>
            </div>
            <span
                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $shipment->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                {{ ucfirst($shipment->status) }}
            </span>
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-3 py-2">Produk</th>
                    <th class="px-3 py-2 text-right">Qty Kirim</th>
                    <th class="px-3 py-2 text-right">Terjual</th>
                    <th class="px-3 py-2 text-right">Retur</th>
                    <th class="px-3 py-2 text-right">Sisa</th>
                    <th class="px-3 py-2 text-right">Harga Titip</th>
                    <th class="px-3 py-2 text-right">HPP Satuan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($shipment->items as $item)
                    <tr>
                        <td class="px-3 py-2">{{ $item->product->nama_produk }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->qty_kirim }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->qty_terjual }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->qty_retur }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->sisaBelumTerjual() }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->harga_titip, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->hpp_satuan, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($shipment->status === 'berjalan')
            <div class="flex gap-3">
                <a href="{{ route('consignment.sales-reports.create', $shipment) }}"
                    class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                    Input Laporan Penjualan
                </a>
                <a href="{{ route('consignment.returns.create', $shipment) }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Catat Retur
                </a>
            </div>
        @endif
    </x-card>

    @if ($shipment->salesReports->isNotEmpty())
        <x-card class="shadow-sm p-0 overflow-hidden mb-6">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal Lapor</th>
                        <th class="px-4 py-3 text-right">Qty Terjual</th>
                        <th class="px-4 py-3 text-right">Total Penjualan</th>
                        <th class="px-4 py-3 text-right">Total HPP</th>
                        <th class="px-4 py-3 text-right">Komisi</th>
                        <th class="px-4 py-3 text-right">Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($shipment->salesReports as $report)
                        <tr>
                            <td class="px-4 py-3">{{ $report->tanggal_lapor->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">{{ $report->total_qty_terjual }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($report->total_penjualan, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($report->total_hpp, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($report->total_komisi, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($report->total_diterima, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    @endif

    @if ($shipment->returns->isNotEmpty())
        <x-card class="shadow-sm p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal Retur</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3 text-right">Qty Retur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($shipment->returns as $return)
                        <tr>
                            <td class="px-4 py-3">{{ $return->tanggal_retur->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $return->product->nama_produk }}</td>
                            <td class="px-4 py-3 text-right">{{ $return->qty_retur }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    @endif
@endsection

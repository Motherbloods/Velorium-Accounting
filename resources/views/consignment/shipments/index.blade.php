@extends('layouts.app')

@section('title', 'Pengiriman Konsinyasi')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar pengiriman konsinyasi</p>
        <a href="{{ route('consignment.shipments.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Kirim Barang Konsinyasi
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">No. Konsinyasi</th>
                    <th class="px-4 py-3">Tanggal Kirim</th>
                    <th class="px-4 py-3">Consignee</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($shipments as $shipment)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $shipment->nomor_konsinyasi }}</td>
                        <td class="px-4 py-3">{{ $shipment->tanggal_kirim->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $shipment->consignee->nama }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $shipment->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($shipment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('consignment.shipments.show', $shipment) }}"
                                class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $shipments->links() }}
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Catat Retur Konsinyasi')

@section('content')
    <x-card class="shadow-md">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <p class="text-sm text-slate-500 mb-4">Retur untuk {{ $shipment->nomor_konsinyasi }} —
            {{ $shipment->consignee->nama }}</p>

        <form method="POST" action="{{ route('consignment.returns.store', $shipment) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Tanggal Retur</label>
                <input type="date" name="tanggal_retur" value="{{ now()->toDateString() }}" required
                    class="w-full max-w-xs px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-3 py-2">Produk</th>
                        <th class="px-3 py-2 text-right">Sisa Belum Terjual</th>
                        <th class="px-3 py-2 w-32">Qty Retur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($shipment->items as $item)
                        <tr>
                            <td class="px-3 py-2">
                                {{ $item->product->nama_produk }}
                                <input type="hidden" name="items[{{ $loop->index }}][shipment_item_id]"
                                    value="{{ $item->id }}">
                            </td>
                            <td class="px-3 py-2 text-right">{{ $item->sisaBelumTerjual() }}</td>
                            <td class="px-3 py-2">
                                <input type="number" name="items[{{ $loop->index }}][qty_retur]" min="0"
                                    max="{{ $item->sisaBelumTerjual() }}" value="0"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan Retur
                </button>
                <a href="{{ route('consignment.shipments.show', $shipment) }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>
@endsection

@extends('layouts.app')

@section('title', 'Kirim Barang Konsinyasi')

@section('content')
    <x-card class="shadow-md" x-data="shipmentForm()">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('consignment.shipments.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Consignee</label>
                    <select name="consignee_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($consignees as $consignee)
                            <option value="{{ $consignee->id }}">{{ $consignee->nama }}
                                ({{ $consignee->persentase_komisi }}% komisi)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tanggal Kirim</label>
                    <input type="date" name="tanggal_kirim" value="{{ now()->toDateString() }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Gudang Asal</label>
                <select name="warehouse_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected($warehouse->is_default)>{{ $warehouse->nama_gudang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-text">Item Produk</label>
                    <button type="button" @click="addItem()" class="text-primary text-sm font-medium">+ Tambah
                        Item</button>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-3 py-2">Produk</th>
                            <th class="px-3 py-2 w-28">Qty Kirim</th>
                            <th class="px-3 py-2 w-40">Harga Titip (Jual)</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-2">
                                    <select :name="'items[' + index + '][product_id]'" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">— Pilih Produk —</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->kode_produk }} —
                                                {{ $product->nama_produk }} (stok: {{ $product->stok_gudang }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="1" :name="'items[' + index + '][qty_kirim]'"
                                        x-model="item.qty_kirim" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0"
                                        :name="'items[' + index + '][harga_titip]'" x-model="item.harga_titip" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-error">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <p class="text-xs text-slate-400 mt-2">HPP per unit akan diambil otomatis dari sistem persediaan (bukan
                    input manual), sesuai metode penilaian produk masing-masing.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Kirim Barang
                </button>
                <a href="{{ route('consignment.shipments.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>

    <script>
        function shipmentForm() {
            return {
                items: [{
                    qty_kirim: 1,
                    harga_titip: 0
                }],
                addItem() {
                    this.items.push({
                        qty_kirim: 1,
                        harga_titip: 0
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }
        }
    </script>
@endsection

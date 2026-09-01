@extends('layouts.app')

@section('title', 'Transaksi Pembelian Baru')

@section('content')
    <x-card class="shadow-md" x-data="purchaseForm()">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('purchases.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Supplier</label>
                    <select name="supplier_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Gudang</label>
                    <select name="warehouse_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected($warehouse->is_default)>{{ $warehouse->nama_gudang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tipe</label>
                    <select name="tipe" x-model="tipe" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="tunai">Tunai</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>
            </div>

            <div x-show="tipe === 'tunai'">
                <label class="block text-sm font-medium text-text mb-1">Akun Kas/Bank</label>
                <select name="coa_kas_bank_id"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($kasBankAccounts as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="tipe === 'kredit'">
                <label class="block text-sm font-medium text-text mb-1">Termin Jatuh Tempo (hari)</label>
                <input type="number" name="termin_jatuh_tempo_hari" value="30" min="1"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
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
                            <th class="px-3 py-2 w-28">Qty</th>
                            <th class="px-3 py-2 w-40">Harga Satuan</th>
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
                                                {{ $product->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="1" :name="'items[' + index + '][qty]'"
                                        x-model="item.qty" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0"
                                        :name="'items[' + index + '][harga_satuan]'" x-model="item.harga_satuan" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-error">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Diskon Dagang</label>
                    <input type="number" step="0.01" min="0" name="diskon_dagang" value="0"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="kena_ppn" value="1" class="rounded border-slate-300">
                        Kena PPN
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan Transaksi
                </button>
                <a href="{{ route('purchases.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>

    <script>
        function purchaseForm() {
            return {
                tipe: 'tunai',
                items: [{
                    qty: 1,
                    harga_satuan: 0
                }],
                addItem() {
                    this.items.push({
                        qty: 1,
                        harga_satuan: 0
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

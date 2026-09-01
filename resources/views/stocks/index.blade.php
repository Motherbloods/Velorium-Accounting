@extends('layouts.app')

@section('title', 'Persediaan')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-card class="shadow-md bg-emerald-50 border-l-4 border-l-success">
            <p class="text-sm font-medium text-emerald-700 mb-4">Stok Masuk (Penyesuaian Manual)</p>
            <form method="POST" action="{{ route('stock.in') }}" class="space-y-3">
                @csrf
                <select name="product_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Pilih Produk —</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->kode_produk }} — {{ $product->nama_produk }}
                        </option>
                    @endforeach
                </select>
                <select name="warehouse_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Pilih Gudang —</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected($warehouse->is_default)>{{ $warehouse->nama_gudang }}
                        </option>
                    @endforeach
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="qty" min="1" required placeholder="Qty"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <input type="number" step="0.01" min="0" name="harga_per_unit" required
                        placeholder="Harga per unit"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit" class="w-full py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                    Catat Stok Masuk
                </button>
            </form>
        </x-card>

        <x-card class="shadow-md bg-red-50 border-l-4 border-l-error">
            <p class="text-sm font-medium text-error mb-4">Stok Keluar (Penyesuaian Manual)</p>
            <form method="POST" action="{{ route('stock.out') }}" class="space-y-3">
                @csrf
                <select name="product_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Pilih Produk —</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->kode_produk }} — {{ $product->nama_produk }}
                        </option>
                    @endforeach
                </select>
                <select name="warehouse_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Pilih Gudang —</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected($warehouse->is_default)>{{ $warehouse->nama_gudang }}
                        </option>
                    @endforeach
                </select>
                <input type="number" name="qty" min="1" required placeholder="Qty"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit" class="w-full py-2 rounded-lg bg-error text-white text-sm font-medium shadow-sm">
                    Catat Stok Keluar
                </button>
            </form>
        </x-card>
    </div>

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3 text-right">Harga Rata-rata</th>
                    <th class="px-4 py-3">Rincian Stok per Gudang</th>
                    <th class="px-4 py-3 text-right">Total Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $product->kode_produk }} — {{ $product->nama_produk }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-accent">
                                {{ $product->metode_penilaian === 'fifo' ? 'FIFO' : 'Rata-rata' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ $product->metode_penilaian === 'rata_rata' ? number_format($product->harga_rata_rata, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @foreach ($product->stocks as $stock)
                                <span
                                    class="inline-block mr-2 mb-1 px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">
                                    {{ $stock->warehouse->nama_gudang }}: {{ $stock->qty }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-right font-medium">{{ $product->stok_gudang }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
@endsection

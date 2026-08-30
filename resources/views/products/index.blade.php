@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar produk</p>
        <a href="{{ route('products.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Produk
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Produk</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3 text-right">Harga Beli</th>
                    <th class="px-4 py-3 text-right">Harga Jual</th>
                    <th class="px-4 py-3 text-right">Stok Gudang</th>
                    <th class="px-4 py-3 text-right">Stok Konsinyasi</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $product->kode_produk }}</td>
                        <td class="px-4 py-3">{{ $product->nama_produk }}</td>
                        <td class="px-4 py-3">{{ $product->satuan }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->harga_beli, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->harga_jual, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->stok_gudang }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->stok_konsinyasi }}</td>
                        <td class="px-4 py-3 text-right flex items-center justify-end gap-3">
                            <a href="{{ route('products.edit', $product) }}"
                                class="text-primary text-sm font-medium">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error text-sm font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection

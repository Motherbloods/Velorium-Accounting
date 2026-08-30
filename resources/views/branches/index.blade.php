@extends('layouts.app')

@section('title', 'Cabang / Gudang')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm font-medium text-accent mb-4">Tambah Cabang</p>
            <form method="POST" action="{{ route('branches.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Cabang</label>
                    <input type="text" name="nama_cabang" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Alamat</label>
                    <textarea name="alamat"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
            </form>
        </x-card>

        <x-card class="shadow-md bg-amber-50 border-l-4 border-l-warning lg:col-span-2">
            <p class="text-sm font-medium text-accent mb-4">Tambah Gudang</p>
            <form method="POST" action="{{ route('warehouses.store') }}"
                class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Cabang</label>
                    <select name="branch_id" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Gudang</label>
                    <input type="text" name="nama_gudang" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_default" value="1" class="rounded border-slate-300">
                        Gudang Default
                    </label>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                        Simpan
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    @foreach ($branches as $branch)
        <x-card class="shadow-sm mb-4">
            <div class="flex items-center justify-between mb-3">
                <p class="font-semibold text-text">{{ $branch->nama_cabang }}</p>
                <span class="text-xs text-slate-500">{{ $branch->alamat }}</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-3 py-2">Nama Gudang</th>
                        <th class="px-3 py-2">Default</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($branch->warehouses as $warehouse)
                        <tr>
                            <td class="px-3 py-2">{{ $warehouse->nama_gudang }}</td>
                            <td class="px-3 py-2">
                                @if ($warehouse->is_default)
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Default</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">
                                <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}"
                                    onsubmit="return confirm('Hapus gudang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-error text-sm font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-2 text-slate-400" colspan="3">Belum ada gudang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    @endforeach
@endsection

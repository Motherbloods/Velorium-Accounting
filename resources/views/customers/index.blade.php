@extends('layouts.app')

@section('title', 'Customer')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar customer</p>
        <a href="{{ route('customers.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Customer
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
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Telepon</th>
                    <th class="px-4 py-3">NPWP</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($customers as $customer)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $customer->kode_customer }}</td>
                        <td class="px-4 py-3">{{ $customer->nama }}</td>
                        <td class="px-4 py-3">{{ $customer->telepon }}</td>
                        <td class="px-4 py-3">{{ $customer->npwp }}</td>
                        <td class="px-4 py-3 text-right flex items-center justify-end gap-3">
                            <a href="{{ route('customers.edit', $customer) }}"
                                class="text-primary text-sm font-medium">Edit</a>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                onsubmit="return confirm('Hapus customer ini?')">
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
        {{ $customers->links() }}
    </div>
@endsection

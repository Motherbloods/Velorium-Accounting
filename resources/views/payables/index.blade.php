@extends('layouts.app')

@section('title', 'Hutang')

@section('content')
    <div class="flex items-center justify-end mb-4">
        <a href="{{ route('payables.create-loan') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Pinjaman Bank
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">No. Hutang</th>
                    <th class="px-4 py-3">Supplier</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Klasifikasi</th>
                    <th class="px-4 py-3 text-right">Sisa Hutang</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($payables as $payable)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $payable->nomor_hutang }}</td>
                        <td class="px-4 py-3">{{ $payable->supplier->nama ?? '—' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $payable->jenis }}</td>
                        <td class="px-4 py-3">
                            {{ $payable->klasifikasi() === 'jangka_pendek' ? 'Jangka Pendek' : 'Jangka Panjang' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($payable->sisa_hutang, 2) }}</td>
                        <td class="px-4 py-3"><x-badge-status :status="$payable->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('payables.show', $payable) }}"
                                class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $payables->links() }}
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Rekonsiliasi Bank')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar rekonsiliasi bank</p>
        <a href="{{ route('bank-reconciliations.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Buat Rekonsiliasi
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Rekening</th>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3 text-right">Saldo Buku</th>
                    <th class="px-4 py-3 text-right">Saldo Rek. Koran</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($reconciliations as $recon)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $recon->bankAccount->nama_bank }}</td>
                        <td class="px-4 py-3">{{ $recon->periode->format('M Y') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($recon->saldo_buku, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($recon->saldo_rekening_koran, 2) }}</td>
                        <td class="px-4 py-3"><x-badge-status :status="$recon->status === 'selesai' ? 'lunas' : 'draft'" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bank-reconciliations.show', $recon) }}"
                                class="text-primary text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
@endsection

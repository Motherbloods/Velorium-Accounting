@extends('layouts.app')

@section('title', 'Rekening Bank')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar rekening bank</p>
        <a href="{{ route('bank-accounts.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Rekening
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama Bank</th>
                    <th class="px-4 py-3">No. Rekening</th>
                    <th class="px-4 py-3">Akun COA</th>
                    <th class="px-4 py-3 text-right">Saldo Berjalan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($bankAccounts as $bankAccount)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $bankAccount->nama_bank }}</td>
                        <td class="px-4 py-3">{{ $bankAccount->no_rekening }}</td>
                        <td class="px-4 py-3">{{ $bankAccount->coaAccount->nama_akun }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($bankAccount->saldo_berjalan, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
@endsection

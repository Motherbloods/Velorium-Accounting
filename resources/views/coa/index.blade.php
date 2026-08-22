@extends('layouts.app')

@section('title', 'Chart of Account')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-slate-500">Daftar akun berjenjang</p>
        <a href="{{ route('coa.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Tambah Akun
        </a>
    </div>

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Akun</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Saldo Normal</th>
                    <th class="px-4 py-3">Postable</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($accounts as $account)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ str_repeat('　', $account->level - 1) }}{{ $account->kode_akun }}
                        </td>
                        <td class="px-4 py-3">{{ $account->nama_akun }}</td>
                        <td class="px-4 py-3 capitalize">{{ $account->tipe_akun }}</td>
                        <td class="px-4 py-3 capitalize">{{ $account->saldo_normal }}</td>
                        <td class="px-4 py-3">
                            @if ($account->is_postable)
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Ya</span>
                            @else
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">Tidak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($account->is_active)
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-error">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('coa.edit', $account) }}" class="text-primary text-sm font-medium">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
@endsection

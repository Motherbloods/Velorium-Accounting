@extends('layouts.app')

@section('title', 'Consignee')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm font-medium text-accent mb-4">Tambah Consignee</p>
            <form method="POST" action="{{ route('consignees.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="nama" placeholder="Nama" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <textarea name="alamat" placeholder="Alamat"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                <input type="text" name="telepon" placeholder="Telepon"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="number" step="0.01" min="0" max="100" name="persentase_komisi"
                    placeholder="Persentase Komisi (%)" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
            </form>
        </x-card>

        <div class="lg:col-span-2">
            <x-card class="shadow-md p-0 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Telepon</th>
                            <th class="px-4 py-3 text-right">Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($consignees as $consignee)
                            <tr>
                                <td class="px-4 py-3">{{ $consignee->nama }}</td>
                                <td class="px-4 py-3">{{ $consignee->telepon }}</td>
                                <td class="px-4 py-3 text-right">{{ $consignee->persentase_komisi }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
@endsection

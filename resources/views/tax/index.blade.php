@extends('layouts.app')

@section('title', 'Pengaturan Pajak')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm font-medium text-accent mb-4">Tambah Tarif Pajak</p>
            <form method="POST" action="{{ route('tax.settings.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Pajak</label>
                    <input type="text" name="nama_pajak" required placeholder="PPN / PPh Final UMKM"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tarif (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="tarif_persen" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Berlaku Sejak</label>
                    <input type="date" name="berlaku_sejak" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
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
                            <th class="px-4 py-3">Nama Pajak</th>
                            <th class="px-4 py-3 text-right">Tarif</th>
                            <th class="px-4 py-3">Berlaku Sejak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($settings as $setting)
                            <tr>
                                <td class="px-4 py-3">{{ $setting->nama_pajak }}</td>
                                <td class="px-4 py-3 text-right">{{ $setting->tarif_persen }}%</td>
                                <td class="px-4 py-3">{{ $setting->berlaku_sejak->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
@endsection

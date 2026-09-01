@extends('layouts.app')

@section('title', 'PPh Final')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary max-w-md">
        <p class="text-sm font-medium text-accent mb-3">Akui PPh Final Bulanan</p>
        <form method="POST" action="{{ route('tax.pph.recognize') }}" class="flex items-end gap-3">
            @csrf
            <input type="month" name="periode" required value="{{ now()->format('Y-m') }}"
                class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Akui (Draft)
            </button>
        </form>
    </x-card>

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3 text-right">Omzet Bruto</th>
                    <th class="px-4 py-3 text-right">Tarif</th>
                    <th class="px-4 py-3 text-right">Jumlah Pajak</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pphList as $pph)
                    <tr>
                        <td class="px-4 py-3">{{ $pph->periode_pajak }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($pph->omzet_bruto, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $pph->tarif_persen }}%</td>
                        <td class="px-4 py-3 text-right">{{ number_format($pph->jumlah_pajak, 2) }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $pph->status === 'disetor' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($pph->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($pph->status === 'diakui')
                                <form method="POST" action="{{ route('tax.pph.setor', $pph) }}"
                                    class="flex items-center justify-end gap-2">
                                    @csrf
                                    <select name="coa_kas_bank_id" required
                                        class="px-2 py-1 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary">
                                        @foreach ($coaKasBankOptions as $coa)
                                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="text-primary text-sm font-medium">Setor</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-3 text-slate-400" colspan="6">Belum ada PPh Final yang diakui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
@endsection

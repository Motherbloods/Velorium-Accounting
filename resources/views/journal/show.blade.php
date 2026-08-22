@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')
    <x-card class="shadow-md">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-lg font-semibold text-text">{{ $entry->nomor_bukti }}</p>
                <p class="text-sm text-slate-500">{{ $entry->tanggal->format('d M Y') }} — {{ $entry->keterangan }}</p>
            </div>
            <x-badge-status :status="$entry->status" class="text-sm px-3 py-1" />
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-3 py-2">Akun</th>
                    <th class="px-3 py-2">Keterangan</th>
                    <th class="px-3 py-2 text-right">Debit</th>
                    <th class="px-3 py-2 text-right">Kredit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($entry->details as $detail)
                    <tr>
                        <td class="px-3 py-2">{{ $detail->coaAccount->kode_akun }} — {{ $detail->coaAccount->nama_akun }}
                        </td>
                        <td class="px-3 py-2 text-slate-500">{{ $detail->keterangan }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($detail->debit, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($detail->kredit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold border-t border-slate-200">
                    <td class="px-3 py-2" colspan="2">Total</td>
                    <td class="px-3 py-2 text-right">{{ number_format($entry->details->sum('debit'), 2) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($entry->details->sum('kredit'), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="flex gap-3">
            @if ($entry->status === 'draft')
                <form method="POST" action="{{ route('journal.submit', $entry) }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                        Ajukan Persetujuan
                    </button>
                </form>
            @endif

            @if ($entry->status === 'submitted')
                <form method="POST" action="{{ route('journal.approve', $entry) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                        Setujui
                    </button>
                </form>
                <form method="POST" action="{{ route('journal.reject', $entry) }}" x-data="{ open: false }">
                    @csrf
                    <div x-show="!open">
                        <button type="button" @click="open = true"
                            class="px-4 py-2 rounded-lg bg-white border border-error text-error text-sm font-medium">
                            Tolak
                        </button>
                    </div>
                    <div x-show="open" class="flex gap-2">
                        <input type="text" name="catatan_penolakan" required placeholder="Alasan penolakan"
                            class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-error">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-error text-white text-sm font-medium">
                            Kirim
                        </button>
                    </div>
                </form>
            @endif

            @if ($entry->status === 'rejected')
                <div class="text-sm text-error">Catatan penolakan: {{ $entry->catatan_penolakan }}</div>
                <form method="POST" action="{{ route('journal.back-to-draft', $entry) }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-sm font-medium text-slate-600">
                        Kembalikan ke Draft
                    </button>
                </form>
            @endif

            @if ($entry->status === 'approved')
                <form method="POST" action="{{ route('journal.post', $entry) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm">
                        Posting Jurnal
                    </button>
                </form>
            @endif

            <a href="{{ route('journal.index') }}"
                class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                Kembali
            </a>
        </div>
    </x-card>
@endsection

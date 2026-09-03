@extends('layouts.app')

@section('title', 'Tutup Buku')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <x-card class="shadow-md mb-6 bg-red-50 border-l-4 border-l-error max-w-lg">
        <p class="text-sm font-medium text-error mb-2">Buat Jurnal Penutup</p>
        <p class="text-xs text-slate-500 mb-4">
            Tindakan ini akan membuat jurnal penutup berstatus <strong>draft</strong> untuk periode yang dipilih.
            Jurnal ini tetap harus melalui alur persetujuan (submit → approve → posting) sebelum periode benar-benar bisa
            ditutup —
            tidak langsung menutup periode saat tombol ini ditekan.
        </p>
        <form method="POST" action="{{ route('closing-periods.store') }}" class="flex gap-3">
            @csrf
            <select name="fiscal_period_id" required
                class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-error">
                <option value="">— Pilih Periode —</option>
                @foreach ($eligiblePeriods as $period)
                    <option value="{{ $period->id }}">{{ $period->nama_periode }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-error text-white text-sm font-medium shadow-sm">
                Buat Jurnal Penutup
            </button>
        </form>
    </x-card>

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3 text-right">Laba/Rugi Bersih</th>
                    <th class="px-4 py-3">Jurnal Penutup</th>
                    <th class="px-4 py-3">Jurnal Pembalik</th>
                    <th class="px-4 py-3">Status Periode</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($closingPeriods as $closing)
                    <tr>
                        <td class="px-4 py-3">{{ $closing->fiscalPeriod->nama_periode }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($closing->laba_rugi_bersih, 2) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('journal.show', $closing->closingJournalEntry) }}" class="text-primary">
                                {{ $closing->closingJournalEntry->nomor_bukti }}
                            </a>
                            <x-badge-status :status="$closing->closingJournalEntry->status" class="ml-2" />
                        </td>
                        <td class="px-4 py-3">
                            @if ($closing->reversingJournalEntry)
                                <a href="{{ route('journal.show', $closing->reversingJournalEntry) }}" class="text-primary">
                                    {{ $closing->reversingJournalEntry->nomor_bukti }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $closing->fiscalPeriod->status === 'closed' ? 'bg-slate-100 text-slate-500' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($closing->fiscalPeriod->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($closing->fiscalPeriod->status === 'open' && $closing->closingJournalEntry->status === 'posted')
                                <form method="POST" action="{{ route('closing-periods.finalize', $closing) }}"
                                    onsubmit="return confirm('Tutup periode ini secara permanen? Tidak ada transaksi baru yang bisa masuk setelahnya.')">
                                    @csrf
                                    <button type="submit" class="text-error text-sm font-medium">Finalisasi Tutup
                                        Buku</button>
                                </form>
                            @elseif ($closing->fiscalPeriod->status === 'open')
                                <span class="text-xs text-slate-400">Menunggu jurnal diposting</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-3 text-slate-400" colspan="6">Belum ada proses tutup buku.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
@endsection

@extends('layouts.app')

@section('title', 'Jurnal Umum')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex gap-2">
            @foreach (['' => 'Semua', 'draft' => 'Draft', 'submitted' => 'Submitted', 'approved' => 'Approved', 'posted' => 'Posted', 'rejected' => 'Rejected'] as $value => $label)
                <a href="{{ route('journal.index', ['status' => $value]) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium {{ request('status', '') === $value ? 'bg-primary text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <a href="{{ route('journal.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
            Buat Jurnal
        </a>
    </div>

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nomor Bukti</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($entries as $entry)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono">{{ $entry->nomor_bukti }}</td>
                        <td class="px-4 py-3">{{ $entry->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $entry->keterangan }}</td>
                        <td class="px-4 py-3"><x-badge-status :status="$entry->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('journal.show', $entry) }}" class="text-primary text-sm font-medium">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $entries->links() }}
    </div>
@endsection

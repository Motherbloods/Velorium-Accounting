@extends('layouts.app')

@section('title', 'Detail Aset Tetap')

@section('content')
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="shadow-md lg:col-span-2">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-lg font-semibold text-text">{{ $asset->kode_aset }} — {{ $asset->nama_aset }}</p>
                    <p class="text-sm text-slate-500">Perolehan {{ $asset->tanggal_perolehan->format('d M Y') }} —
                        {{ $asset->metode_penyusutan === 'garis_lurus' ? 'Garis Lurus' : 'Saldo Menurun Ganda' }}</p>
                </div>
                <span
                    class="px-2 py-0.5 rounded-full text-xs font-medium {{ $asset->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ ucfirst($asset->status) }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Harga Perolehan</p>
                    <p class="text-lg font-semibold text-text">{{ number_format($asset->harga_perolehan, 2) }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Akumulasi Penyusutan</p>
                    <p class="text-lg font-semibold text-text">{{ number_format($asset->akumulasi_penyusutan, 2) }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Nilai Buku</p>
                    <p class="text-lg font-semibold text-text">{{ number_format($asset->nilai_buku, 2) }}</p>
                </div>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-3 py-2">Periode</th>
                        <th class="px-3 py-2 text-right">Beban Penyusutan</th>
                        <th class="px-3 py-2 text-right">Akumulasi Setelah</th>
                        <th class="px-3 py-2 text-right">Nilai Buku Setelah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($asset->schedules as $schedule)
                        <tr>
                            <td class="px-3 py-2">{{ $schedule->periode->format('M Y') }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($schedule->beban_penyusutan, 2) }}</td>
                            <td class="px-3 py-2 text-right">
                                {{ number_format($schedule->akumulasi_penyusutan_setelah, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($schedule->nilai_buku_setelah, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-2 text-slate-400" colspan="4">Belum ada penyusutan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        @if ($asset->status === 'aktif')
            <div class="space-y-6">
                <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
                    <p class="text-sm font-medium text-accent mb-3">Jalankan Penyusutan Manual</p>
                    <form method="POST" action="{{ route('fixed-assets.run-depreciation', $asset) }}" class="space-y-3">
                        @csrf
                        <input type="month" name="periode_input" id="periode_input" required
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <input type="hidden" name="periode" id="periode">
                        <button type="submit"
                            class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                            Jalankan
                        </button>
                    </form>
                </x-card>

                <x-card class="shadow-md bg-red-50 border-l-4 border-l-error">
                    <p class="text-sm font-medium text-error mb-3">Lepas Aset</p>
                    <form method="POST" action="{{ route('fixed-assets.dispose', $asset) }}" class="space-y-3"
                        onsubmit="return confirm('Lepas aset ini secara permanen?')">
                        @csrf
                        <input type="date" name="tanggal_pelepasan" value="{{ now()->toDateString() }}" required
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <input type="number" step="0.01" min="0" name="harga_jual_pelepasan" required
                            placeholder="Harga jual"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <button type="submit"
                            class="w-full py-2 rounded-lg bg-error text-white text-sm font-medium shadow-sm">
                            Lepas Aset
                        </button>
                    </form>
                </x-card>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('periode_input')?.addEventListener('change', function() {
            document.getElementById('periode').value = this.value + '-01';
        });
    </script>
@endsection

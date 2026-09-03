@extends('layouts.app')

@section('title', 'Catat Akrual Manual')

@section('content')
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card class="shadow-md bg-amber-50 border-l-4 border-l-warning">
            <p class="text-sm font-medium text-accent mb-4">Beban Masih Harus Dibayar</p>
            <form method="POST" action="{{ route('adjusting-entries.accrued.expense.store') }}" class="space-y-3">
                @csrf
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="text" name="keterangan" placeholder="Keterangan, misal: Gaji karyawan bulan berjalan"
                    required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <select name="coa_beban_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Akun Beban —</option>
                    @foreach ($coaBebanOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
                <select name="coa_kewajiban_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Akun Kewajiban —</option>
                    @foreach ($coaKewajibanOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" min="0.01" name="jumlah" placeholder="Jumlah" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
            </form>
        </x-card>

        <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm font-medium text-accent mb-4">Pendapatan Masih Harus Diterima</p>
            <form method="POST" action="{{ route('adjusting-entries.accrued.revenue.store') }}" class="space-y-3">
                @csrf
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="text" name="keterangan" placeholder="Keterangan" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <select name="coa_piutang_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Akun Piutang —</option>
                    @foreach ($coaPiutangOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
                <select name="coa_pendapatan_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Akun Pendapatan —</option>
                    @foreach ($coaPendapatanOptions as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" min="0.01" name="jumlah" placeholder="Jumlah" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
            </form>
        </x-card>
    </div>
@endsection

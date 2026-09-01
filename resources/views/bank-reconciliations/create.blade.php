@extends('layouts.app')

@section('title', 'Buat Rekonsiliasi Bank')

@section('content')
    <x-card class="shadow-md max-w-xl">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('bank-reconciliations.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Rekening Bank</label>
                <select name="bank_account_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->nama_bank }} —
                            {{ $bankAccount->no_rekening }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Periode</label>
                <input type="month" name="periode_input" id="periode_input" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                <input type="hidden" name="periode" id="periode">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Saldo per Buku</label>
                    <input type="number" step="0.01" name="saldo_buku" value="{{ old('saldo_buku') }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Saldo per Rekening Koran</label>
                    <input type="number" step="0.01" name="saldo_rekening_koran"
                        value="{{ old('saldo_rekening_koran') }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan
                </button>
                <a href="{{ route('bank-reconciliations.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>

    <script>
        document.getElementById('periode_input').addEventListener('change', function() {
            document.getElementById('periode').value = this.value + '-01';
        });
    </script>
@endsection

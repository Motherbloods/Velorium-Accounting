@extends('layouts.app')

@section('title', 'Buat Jurnal')

@section('content')
    <x-card class="shadow-md" x-data="journalForm()">
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('journal.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-text">Baris Jurnal</label>
                    <button type="button" @click="addLine()" class="text-primary text-sm font-medium">+ Tambah
                        Baris</button>
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-3 py-2">Akun</th>
                            <th class="px-3 py-2 w-40">Debit</th>
                            <th class="px-3 py-2 w-40">Kredit</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, index) in lines" :key="index">
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-2">
                                    <select :name="'lines[' + index + '][coa_account_id]'" required
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">— Pilih Akun —</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->kode_akun }} —
                                                {{ $account->nama_akun }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0"
                                        :name="'lines[' + index + '][debit]'" x-model="line.debit"
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0"
                                        :name="'lines[' + index + '][kredit]'" x-model="line.kredit"
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" @click="removeLine(index)" class="text-error">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-3 flex justify-end gap-6 text-sm">
                    <span>Total Debit: <span class="font-semibold"
                            x-text="totalDebit().toLocaleString('id-ID')"></span></span>
                    <span>Total Kredit: <span class="font-semibold"
                            x-text="totalKredit().toLocaleString('id-ID')"></span></span>
                    <span
                        :class="totalDebit() === totalKredit() ? 'text-success font-semibold' : 'text-error font-semibold'"
                        x-text="totalDebit() === totalKredit() ? 'Balance' : 'Tidak Balance'"></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                    Simpan sebagai Draft
                </button>
                <a href="{{ route('journal.index') }}"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </x-card>

    <script>
        function journalForm() {
            return {
                lines: [{
                    debit: 0,
                    kredit: 0
                }, {
                    debit: 0,
                    kredit: 0
                }],
                addLine() {
                    this.lines.push({
                        debit: 0,
                        kredit: 0
                    });
                },
                removeLine(index) {
                    if (this.lines.length > 2) {
                        this.lines.splice(index, 1);
                    }
                },
                totalDebit() {
                    return this.lines.reduce((sum, l) => sum + (parseFloat(l.debit) || 0), 0);
                },
                totalKredit() {
                    return this.lines.reduce((sum, l) => sum + (parseFloat(l.kredit) || 0), 0);
                }
            }
        }
    </script>
@endsection

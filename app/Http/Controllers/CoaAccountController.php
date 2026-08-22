<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoaAccountController extends Controller
{
    public function index(): View
    {
        $accounts = CoaAccount::orderBy('kode_akun')->get();

        return view('coa.index', compact('accounts'));
    }

    public function create(): View
    {
        $parents = CoaAccount::orderBy('kode_akun')->get();

        return view('coa.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_akun' => ['required', 'string', 'max:20', 'unique:coa_accounts,kode_akun'],
            'nama_akun' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:coa_accounts,id'],
            'tipe_akun' => ['required', 'in:aset,kewajiban,modal,pendapatan,beban'],
            'saldo_normal' => ['required', 'in:debit,kredit'],
            'is_postable' => ['nullable', 'boolean'],
        ]);

        $data['level'] = strlen($data['kode_akun']);
        $data['is_postable'] = $request->boolean('is_postable', true);
        $data['is_active'] = true;

        CoaAccount::create($data);

        return redirect()->route('coa.index')->with('status', 'Akun berhasil ditambahkan.');
    }

    public function edit(CoaAccount $coa): View
    {
        $parents = CoaAccount::where('id', '!=', $coa->id)->orderBy('kode_akun')->get();

        return view('coa.edit', ['account' => $coa, 'parents' => $parents]);
    }

    public function update(Request $request, CoaAccount $coa): RedirectResponse
    {
        $data = $request->validate([
            'nama_akun' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:coa_accounts,id'],
            'tipe_akun' => ['required', 'in:aset,kewajiban,modal,pendapatan,beban'],
            'saldo_normal' => ['required', 'in:debit,kredit'],
            'is_postable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_postable'] = $request->boolean('is_postable');
        $data['is_active'] = $request->boolean('is_active');

        $coa->update($data);

        return redirect()->route('coa.index')->with('status', 'Akun berhasil diperbarui.');
    }
}
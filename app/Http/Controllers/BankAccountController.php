<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CoaAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(): View
    {
        $bankAccounts = BankAccount::with('coaAccount')->orderBy('nama_bank')->get();

        return view('bank-accounts.index', compact('bankAccounts'));
    }

    public function create(): View
    {
        $coaOptions = CoaAccount::postable()->active()->where('kode_akun', '112')->get();

        return view('bank-accounts.create', compact('coaOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_bank' => ['required', 'string', 'max:100'],
            'no_rekening' => ['required', 'string', 'max:50'],
            'coa_account_id' => ['required', 'exists:coa_accounts,id'],
        ]);

        BankAccount::create($data);

        return redirect()->route('bank-accounts.index')->with('status', 'Rekening bank berhasil ditambahkan.');
    }
}
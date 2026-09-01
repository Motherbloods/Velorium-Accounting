<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\CoaAccount;
use App\Services\CashTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    public function __construct(protected CashTransactionService $cashTransactionService)
    {
    }

    public function index(): View
    {
        $transactions = CashTransaction::with('coaKasBank', 'coaLawan')->orderByDesc('tanggal')->paginate(20);

        return view('cash.index', compact('transactions'));
    }

    public function create(): View
    {
        $accounts = CoaAccount::postable()->active()->orderBy('kode_akun')->get();
        $kasBankAccounts = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('cash.create', compact('accounts', 'kasBankAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'tipe' => ['required', 'in:masuk,keluar'],
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
            'coa_lawan_id' => ['required', 'exists:coa_accounts,id', 'different:coa_kas_bank_id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $this->cashTransactionService->create($data, $request->user());

        return redirect()->route('cash.index')->with('status', 'Transaksi kas/bank berhasil dicatat.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use App\Models\Payable;
use App\Models\Supplier;
use App\Services\PayableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayableController extends Controller
{
    public function __construct(protected PayableService $payableService)
    {
    }

    public function index(): View
    {
        $payables = Payable::with('supplier')->orderByDesc('tanggal')->paginate(20);

        return view('payables.index', compact('payables'));
    }

    public function createLoan(): View
    {
        $coaKasBankOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('payables.create-loan', compact('coaKasBankOptions'));
    }

    public function storeLoan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nomor_hutang' => ['required', 'string', 'max:50', 'unique:payables,nomor_hutang'],
            'tanggal' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after:tanggal'],
            'tarif_bunga_tahunan' => ['required', 'numeric', 'min:0'],
            'total_hutang' => ['required', 'numeric', 'min:0.01'],
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
            'jangka' => ['required', 'in:pendek,panjang'],
        ]);

        $this->payableService->createManualLoan($data, $request->user());

        return redirect()->route('payables.index')->with('status', 'Pinjaman bank berhasil dicatat.');
    }

    public function show(Payable $payable): View
    {
        $payable->load('payments.coaKasBank', 'supplier');
        $coaKasBankOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('payables.show', compact('payable', 'coaKasBankOptions'));
    }

    public function pay(Request $request, Payable $payable): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_bayar' => ['required', 'date'],
            'jumlah_pokok' => ['required', 'numeric', 'min:0.01'],
            'jumlah_bunga' => ['nullable', 'numeric', 'min:0'],
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
            'terapkan_diskon_tunai' => ['nullable', 'boolean'],
        ]);

        $this->payableService->pay($payable, $data, $request->user());

        return redirect()->route('payables.show', $payable)->with('status', 'Pembayaran hutang berhasil dicatat.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\CoaAccount;
use App\Services\BankReconciliationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankReconciliationController extends Controller
{
    public function __construct(protected BankReconciliationService $bankReconciliationService)
    {
    }

    public function index(): View
    {
        $reconciliations = BankReconciliation::with('bankAccount')->orderByDesc('periode')->get();

        return view('bank-reconciliations.index', compact('reconciliations'));
    }

    public function create(): View
    {
        $bankAccounts = BankAccount::orderBy('nama_bank')->get();

        return view('bank-reconciliations.create', compact('bankAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'periode' => ['required', 'date'],
            'saldo_buku' => ['required', 'numeric'],
            'saldo_rekening_koran' => ['required', 'numeric'],
        ]);

        $reconciliation = $this->bankReconciliationService->create($data, $request->user());

        return redirect()->route('bank-reconciliations.show', $reconciliation)->with('status', 'Rekonsiliasi berhasil dibuat.');
    }

    public function show(BankReconciliation $bankReconciliation): View
    {
        $bankReconciliation->load('items', 'bankAccount');
        $coaOptions = CoaAccount::postable()->active()->orderBy('kode_akun')->get();

        return view('bank-reconciliations.show', ['reconciliation' => $bankReconciliation, 'coaOptions' => $coaOptions]);
    }

    public function addItem(Request $request, BankReconciliation $bankReconciliation): RedirectResponse
    {
        $data = $request->validate([
            'kategori' => ['required', 'in:sisi_buku,sisi_bank'],
            'jenis' => ['required', 'in:jasa_giro,biaya_admin,setoran_dalam_perjalanan,cek_beredar'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
        ]);

        $this->bankReconciliationService->addItem($bankReconciliation, $data);

        return back()->with('status', 'Item rekonsiliasi berhasil ditambahkan.');
    }

    public function postItem(Request $request, BankReconciliationItem $item): RedirectResponse
    {
        $data = $request->validate([
            'coa_lawan_id' => ['required', 'exists:coa_accounts,id'],
        ]);

        $this->bankReconciliationService->postItem($item, $data['coa_lawan_id'], $request->user());

        return back()->with('status', 'Item berhasil diposting sebagai transaksi kas/bank.');
    }

    public function complete(BankReconciliation $bankReconciliation): RedirectResponse
    {
        $this->bankReconciliationService->complete($bankReconciliation);

        return back()->with('status', 'Rekonsiliasi selesai dan valid.');
    }
}
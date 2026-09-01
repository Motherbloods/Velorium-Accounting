<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use App\Models\Customer;
use App\Models\Receivable;
use App\Services\ReceivableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function __construct(protected ReceivableService $receivableService)
    {
    }

    public function index(): View
    {
        $receivables = Receivable::with('customer')->orderByDesc('tanggal')->paginate(20);

        return view('receivables.index', compact('receivables'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('nama')->get();

        return view('receivables.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nomor_invoice' => ['required', 'string', 'max:50', 'unique:receivables,nomor_invoice'],
            'customer_id' => ['required', 'exists:customers,id'],
            'tanggal' => ['required', 'date'],
            'total_tagihan' => ['required', 'numeric', 'min:0.01'],
            'termin_jatuh_tempo_hari' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->receivableService->create($data);

        return redirect()->route('receivables.index')->with('status', 'Piutang manual berhasil ditambahkan.');
    }

    public function show(Receivable $receivable): View
    {
        $receivable->load('payments.coaKasBank', 'customer');
        $coaKasBankOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('receivables.show', compact('receivable', 'coaKasBankOptions'));
    }

    public function pay(Request $request, Receivable $receivable): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_bayar' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0.01'],
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
            'terapkan_diskon_tunai' => ['nullable', 'boolean'],
        ]);

        $this->receivableService->pay($receivable, $data, $request->user());

        return redirect()->route('receivables.show', $receivable)->with('status', 'Pembayaran piutang berhasil dicatat.');
    }

    public function aging(): View
    {
        $buckets = $this->receivableService->agingBuckets();
        $total = $this->receivableService->totalTaksiranTakTertagih();

        return view('receivables.aging', compact('buckets', 'total'));
    }

    public function recordAllowance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $jumlah = $this->receivableService->totalTaksiranTakTertagih();
        $this->receivableService->recordAllowanceAdjustment($data['tanggal'], $jumlah, $request->user());

        return redirect()->route('receivables.aging')->with('status', 'Jurnal cadangan kerugian piutang berhasil dibuat sebagai draft.');
    }
}
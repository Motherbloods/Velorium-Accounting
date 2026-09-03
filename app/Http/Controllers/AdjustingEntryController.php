<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use App\Models\PrepaidExpense;
use App\Models\UnearnedRevenue;
use App\Services\AdjustingEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdjustingEntryController extends Controller
{
    public function __construct(protected AdjustingEntryService $adjustingEntryService)
    {
    }

    public function index(): View
    {
        $prepaidExpenses = PrepaidExpense::orderByDesc('tanggal_bayar')->get();
        $unearnedRevenues = UnearnedRevenue::orderByDesc('tanggal_terima')->get();

        return view('adjusting-entries.index', compact('prepaidExpenses', 'unearnedRevenues'));
    }

    public function createPrepaid(): View
    {
        $coaAsetOptions = CoaAccount::postable()->active()->where('kode_akun', '118')->get();
        $coaBebanOptions = CoaAccount::postable()->active()->where('tipe_akun', 'beban')->get();
        $coaKasBankOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('adjusting-entries.create-prepaid', compact('coaAsetOptions', 'coaBebanOptions', 'coaKasBankOptions'));
    }

    public function storePrepaid(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'coa_aset_id' => ['required', 'exists:coa_accounts,id'],
            'coa_beban_id' => ['required', 'exists:coa_accounts,id'],
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
            'tanggal_bayar' => ['required', 'date'],
            'total_dibayar' => ['required', 'numeric', 'min:0.01'],
            'jumlah_bulan_cakupan' => ['required', 'integer', 'min:1'],
        ]);

        $this->adjustingEntryService->createPrepaidExpense($data, $request->user());

        return redirect()->route('adjusting-entries.index')->with('status', 'Biaya dibayar dimuka berhasil dicatat.');
    }

    public function createUnearned(): View
    {
        $coaKewajibanOptions = CoaAccount::postable()->active()->where('kode_akun', '215')->get();
        $coaPendapatanOptions = CoaAccount::postable()->active()->where('tipe_akun', 'pendapatan')->get();
        $coaKasBankOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('adjusting-entries.create-unearned', compact('coaKewajibanOptions', 'coaPendapatanOptions', 'coaKasBankOptions'));
    }

    public function storeUnearned(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'coa_kewajiban_id' => ['required', 'exists:coa_accounts,id'],
            'coa_pendapatan_id' => ['required', 'exists:coa_accounts,id'],
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
            'tanggal_terima' => ['required', 'date'],
            'total_diterima' => ['required', 'numeric', 'min:0.01'],
            'jumlah_bulan_cakupan' => ['required', 'integer', 'min:1'],
        ]);

        $this->adjustingEntryService->createUnearnedRevenue($data, $request->user());

        return redirect()->route('adjusting-entries.index')->with('status', 'Pendapatan diterima dimuka berhasil dicatat.');
    }

    public function runPrepaid(Request $request, PrepaidExpense $prepaidExpense): RedirectResponse
    {
        $data = $request->validate(['periode' => ['required', 'date']]);

        $result = $this->adjustingEntryService->runPrepaidExpense($prepaidExpense, $data['periode'], $request->user());

        if (!$result) {
            return back()->withErrors(['periode' => 'Penyesuaian untuk periode ini sudah ada atau sudah tidak ada sisa yang perlu diakui.']);
        }

        return back()->with('status', 'Jurnal penyesuaian berhasil dibuat.');
    }

    public function runUnearned(Request $request, UnearnedRevenue $unearnedRevenue): RedirectResponse
    {
        $data = $request->validate(['periode' => ['required', 'date']]);

        $result = $this->adjustingEntryService->runUnearnedRevenue($unearnedRevenue, $data['periode'], $request->user());

        if (!$result) {
            return back()->withErrors(['periode' => 'Penyesuaian untuk periode ini sudah ada atau sudah tidak ada sisa yang perlu diakui.']);
        }

        return back()->with('status', 'Jurnal penyesuaian berhasil dibuat.');
    }

    public function createAccrued(): View
    {
        $coaBebanOptions = CoaAccount::postable()->active()->where('tipe_akun', 'beban')->get();
        $coaKewajibanOptions = CoaAccount::postable()->active()->where('tipe_akun', 'kewajiban')->get();
        $coaPiutangOptions = CoaAccount::postable()->active()->where('kode_akun', '113')->get();
        $coaPendapatanOptions = CoaAccount::postable()->active()->where('tipe_akun', 'pendapatan')->get();

        return view('adjusting-entries.create-accrued', compact('coaBebanOptions', 'coaKewajibanOptions', 'coaPiutangOptions', 'coaPendapatanOptions'));
    }

    public function storeAccruedExpense(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:255'],
            'coa_beban_id' => ['required', 'exists:coa_accounts,id'],
            'coa_kewajiban_id' => ['required', 'exists:coa_accounts,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
        ]);

        $this->adjustingEntryService->recordAccruedExpense($data, $request->user());

        return redirect()->route('adjusting-entries.index')->with('status', 'Beban masih harus dibayar berhasil dicatat.');
    }

    public function storeAccruedRevenue(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:255'],
            'coa_piutang_id' => ['required', 'exists:coa_accounts,id'],
            'coa_pendapatan_id' => ['required', 'exists:coa_accounts,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
        ]);

        $this->adjustingEntryService->recordAccruedRevenue($data, $request->user());

        return redirect()->route('adjusting-entries.index')->with('status', 'Pendapatan masih harus diterima berhasil dicatat.');
    }
}
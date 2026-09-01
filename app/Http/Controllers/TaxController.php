<?php

namespace App\Http\Controllers;

use App\Exceptions\TaxSettingNotFoundException;
use App\Models\CoaAccount;
use App\Models\PphFinalTransaction;
use App\Models\TaxTransaction;
use App\Services\TaxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxController extends Controller
{
    public function __construct(protected TaxService $taxService)
    {
    }

    public function ppnIndex(Request $request): View
    {
        $periodePajak = $request->get('periode', now()->format('Y-m'));

        $totalKeluaran = (string) TaxTransaction::where('tipe', 'ppn_keluaran')->where('periode_pajak', $periodePajak)->sum('jumlah_pajak');
        $totalMasukan = (string) TaxTransaction::where('tipe', 'ppn_masukan')->where('periode_pajak', $periodePajak)->sum('jumlah_pajak');
        $selisih = bcsub($totalKeluaran, $totalMasukan, 2);

        $transactions = TaxTransaction::where('periode_pajak', $periodePajak)->orderBy('created_at')->get();

        return view('tax.ppn', compact('periodePajak', 'totalKeluaran', 'totalMasukan', 'selisih', 'transactions'));
    }

    public function setorPpn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'periode' => ['required', 'string', 'size:7'],
        ]);

        $this->taxService->setorPpn($data['periode'], $request->user());

        return redirect()->route('tax.ppn', ['periode' => $data['periode']])->with('status', 'Jurnal penyetoran PPN berhasil dibuat sebagai draft.');
    }

    public function pphIndex(): View
    {
        $pphList = PphFinalTransaction::orderByDesc('periode_pajak')->get();
        $coaKasBankOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('tax.pph', compact('pphList', 'coaKasBankOptions'));
    }

    public function recognizePph(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'periode' => ['required', 'string', 'size:7'],
        ]);

        try {
            $this->taxService->recognizePphFinal($data['periode'], $request->user());
        } catch (TaxSettingNotFoundException $e) {
            return back()->withErrors(['periode' => $e->getMessage()]);
        }

        return redirect()->route('tax.pph')->with('status', 'PPh Final berhasil diakui sebagai draft.');
    }

    public function setorPph(Request $request, PphFinalTransaction $pphFinalTransaction): RedirectResponse
    {
        $data = $request->validate([
            'coa_kas_bank_id' => ['required', 'exists:coa_accounts,id'],
        ]);

        $this->taxService->setorPphFinal($pphFinalTransaction, $data['coa_kas_bank_id'], $request->user());

        return redirect()->route('tax.pph')->with('status', 'PPh Final berhasil disetor.');
    }
}
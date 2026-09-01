<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use App\Models\FiscalPeriod;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralLedgerController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function index(Request $request): View
    {
        $periods = FiscalPeriod::orderByDesc('tanggal_mulai')->get();
        $accounts = CoaAccount::postable()->active()->orderBy('kode_akun')->get();

        $fiscalPeriod = $request->filled('fiscal_period_id')
            ? FiscalPeriod::find($request->fiscal_period_id)
            : $periods->first();

        $account = $request->filled('coa_account_id')
            ? CoaAccount::find($request->coa_account_id)
            : $accounts->first();

        $ledger = null;

        if ($fiscalPeriod && $account) {
            $ledger = $this->reportService->generalLedger($account, $fiscalPeriod);
        }

        return view('reports.general-ledger', [
            'periods' => $periods,
            'accounts' => $accounts,
            'selectedPeriod' => $fiscalPeriod,
            'selectedAccount' => $account,
            'ledger' => $ledger,
        ]);
    }
}
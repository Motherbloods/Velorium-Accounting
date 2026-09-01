<?php

namespace App\Http\Controllers;

use App\Models\FiscalPeriod;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrialBalanceController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function index(Request $request): View
    {
        $periods = FiscalPeriod::orderByDesc('tanggal_mulai')->get();

        $fiscalPeriod = $request->filled('fiscal_period_id')
            ? FiscalPeriod::find($request->fiscal_period_id)
            : $periods->first();

        $trialBalance = collect();
        $isBalanced = true;

        if ($fiscalPeriod) {
            $trialBalance = $this->reportService->trialBalance($fiscalPeriod);
            $isBalanced = $this->reportService->trialBalanceIsBalanced($trialBalance);
        }

        return view('reports.trial-balance', [
            'periods' => $periods,
            'selectedPeriod' => $fiscalPeriod,
            'trialBalance' => $trialBalance,
            'isBalanced' => $isBalanced,
        ]);
    }
}
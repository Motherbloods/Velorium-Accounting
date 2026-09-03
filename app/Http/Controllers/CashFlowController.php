<?php

namespace App\Http\Controllers;

use App\Models\FiscalPeriod;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashFlowController extends Controller
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

        $report = $fiscalPeriod ? $this->reportService->cashFlowStatement($fiscalPeriod) : null;

        return view('reports.cash-flow', [
            'periods' => $periods,
            'selectedPeriod' => $fiscalPeriod,
            'report' => $report,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\FiscalPeriod;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialRatioController extends Controller
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

        $report = null;

        if ($fiscalPeriod) {
            $periodeSebelumnya = FiscalPeriod::where('tanggal_selesai', '<', $fiscalPeriod->tanggal_mulai)
                ->orderByDesc('tanggal_selesai')
                ->first();

            $report = $this->reportService->financialRatios($fiscalPeriod, $periodeSebelumnya);
        }

        return view('reports.financial-ratios', [
            'periods' => $periods,
            'selectedPeriod' => $fiscalPeriod,
            'report' => $report,
        ]);
    }
}
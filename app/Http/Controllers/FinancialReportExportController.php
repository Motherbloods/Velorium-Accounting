<?php

namespace App\Http\Controllers;

use App\Exports\FinancialReportExport;
use App\Models\FiscalPeriod;
use App\Services\FinancialReportExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinancialReportExportController extends Controller
{
    public function __construct(protected FinancialReportExportService $exportService)
    {
    }

    public function index(Request $request): View
    {
        $periods = FiscalPeriod::orderByDesc('tanggal_mulai')->get();

        $fiscalPeriod = $request->filled('fiscal_period_id')
            ? FiscalPeriod::find($request->fiscal_period_id)
            : $periods->first();

        return view('financial-report-export.index', compact('periods', 'fiscalPeriod'));
    }

    public function pdf(FiscalPeriod $fiscalPeriod): Response
    {
        $data = $this->exportService->compileData($fiscalPeriod);

        $pdf = Pdf::loadView('exports.financial-report-pdf', $data)->setPaper('a4', 'portrait');

        $filename = "laporan-keuangan-{$fiscalPeriod->nama_periode}.pdf";

        return $pdf->download($filename);
    }

    public function excel(FiscalPeriod $fiscalPeriod): BinaryFileResponse
    {
        $filename = "laporan-keuangan-{$fiscalPeriod->nama_periode}.xlsx";

        return Excel::download(new FinancialReportExport($fiscalPeriod), $filename);
    }
}
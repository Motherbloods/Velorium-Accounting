<?php

namespace App\Exports;

use App\Models\FiscalPeriod;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ArusKasSheet implements FromView, WithTitle
{
    public function __construct(protected FiscalPeriod $fiscalPeriod)
    {
    }

    public function view(): View
    {
        $reportService = app(ReportService::class);

        return view('exports.arus-kas-sheet', [
            'fiscalPeriod' => $this->fiscalPeriod,
            'report' => $reportService->cashFlowStatement($this->fiscalPeriod),
        ]);
    }

    public function title(): string
    {
        return 'Arus Kas';
    }
}
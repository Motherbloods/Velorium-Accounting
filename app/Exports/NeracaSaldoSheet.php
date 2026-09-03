<?php

namespace App\Exports;

use App\Models\FiscalPeriod;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class NeracaSaldoSheet implements FromView, WithTitle
{
    public function __construct(protected FiscalPeriod $fiscalPeriod)
    {
    }

    public function view(): View
    {
        $reportService = app(ReportService::class);
        $trialBalance = $reportService->trialBalance($this->fiscalPeriod);

        return view('exports.neraca-saldo-sheet', [
            'fiscalPeriod' => $this->fiscalPeriod,
            'trialBalance' => $trialBalance,
        ]);
    }

    public function title(): string
    {
        return 'Neraca Saldo';
    }
}
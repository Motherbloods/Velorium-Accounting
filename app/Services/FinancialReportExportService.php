<?php

namespace App\Services;

use App\Models\FinancialNote;
use App\Models\FiscalPeriod;

class FinancialReportExportService
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function compileData(FiscalPeriod $fiscalPeriod): array
    {
        $periodeSebelumnya = FiscalPeriod::where('tanggal_selesai', '<', $fiscalPeriod->tanggal_mulai)
            ->orderByDesc('tanggal_selesai')
            ->first();

        $note = FinancialNote::where('fiscal_period_id', $fiscalPeriod->id)->first();

        return [
            'fiscal_period' => $fiscalPeriod,
            'trial_balance' => $this->reportService->trialBalance($fiscalPeriod),
            'trial_balance_is_balanced' => $this->reportService->trialBalanceIsBalanced($this->reportService->trialBalance($fiscalPeriod)),
            'income_statement' => $this->reportService->incomeStatement($fiscalPeriod),
            'balance_sheet' => $this->reportService->balanceSheet($fiscalPeriod),
            'equity_change' => $this->reportService->equityChangeStatement($fiscalPeriod),
            'cash_flow' => $this->reportService->cashFlowStatement($fiscalPeriod),
            'financial_note' => $note,
        ];
    }
}
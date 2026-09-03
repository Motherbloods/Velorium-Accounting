<?php

namespace App\Exports;

use App\Models\FiscalPeriod;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinancialReportExport implements WithMultipleSheets
{
    public function __construct(protected FiscalPeriod $fiscalPeriod)
    {
    }

    public function sheets(): array
    {
        return [
            new NeracaSaldoSheet($this->fiscalPeriod),
            new LabaRugiSheet($this->fiscalPeriod),
            new NeracaSheet($this->fiscalPeriod),
            new PerubahanModalSheet($this->fiscalPeriod),
            new ArusKasSheet($this->fiscalPeriod),
            new CalkSheet($this->fiscalPeriod),
        ];
    }
}
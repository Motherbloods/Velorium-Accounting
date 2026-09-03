<?php

namespace App\Exports;

use App\Models\FinancialNote;
use App\Models\FiscalPeriod;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CalkSheet implements FromView, WithTitle
{
    public function __construct(protected FiscalPeriod $fiscalPeriod)
    {
    }

    public function view(): View
    {
        $note = FinancialNote::where('fiscal_period_id', $this->fiscalPeriod->id)->first();

        return view('exports.calk-sheet', [
            'fiscalPeriod' => $this->fiscalPeriod,
            'konten' => $note->konten ?? '',
        ]);
    }

    public function title(): string
    {
        return 'CALK';
    }
}
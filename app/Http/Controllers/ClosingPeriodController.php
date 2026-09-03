<?php

namespace App\Http\Controllers;

use App\Models\ClosingPeriod;
use App\Models\FiscalPeriod;
use App\Services\ClosingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClosingPeriodController extends Controller
{
    public function __construct(protected ClosingService $closingService)
    {
    }

    public function index(): View
    {
        $closingPeriods = ClosingPeriod::with('fiscalPeriod', 'closingJournalEntry', 'reversingJournalEntry')
            ->orderByDesc('id')
            ->get();

        $eligiblePeriods = FiscalPeriod::where('status', 'open')
            ->whereNotIn('id', $closingPeriods->pluck('fiscal_period_id'))
            ->get();

        return view('closing-periods.index', compact('eligiblePeriods', 'closingPeriods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fiscal_period_id' => ['required', 'exists:fiscal_periods,id'],
        ]);

        $fiscalPeriod = FiscalPeriod::findOrFail($data['fiscal_period_id']);
        $closingPeriod = $this->closingService->prepareClosingEntry($fiscalPeriod, $request->user());

        return redirect()->route('journal.show', $closingPeriod->closing_journal_entry_id)
            ->with('status', 'Jurnal penutup berhasil dibuat sebagai draft. Ikuti alur persetujuan sebelum menutup periode.');
    }

    public function finalize(Request $request, ClosingPeriod $closingPeriod): RedirectResponse
    {
        $this->closingService->finalizeClosing($closingPeriod, $request->user());

        return redirect()->route('closing-periods.index')->with('status', 'Periode berhasil ditutup.');
    }
}
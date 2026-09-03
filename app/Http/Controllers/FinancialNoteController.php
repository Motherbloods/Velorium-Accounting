<?php

namespace App\Http\Controllers;

use App\Models\FinancialNote;
use App\Models\FiscalPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialNoteController extends Controller
{
    public function index(Request $request): View
    {
        $periods = FiscalPeriod::orderByDesc('tanggal_mulai')->get();

        $fiscalPeriod = $request->filled('fiscal_period_id')
            ? FiscalPeriod::find($request->fiscal_period_id)
            : $periods->first();

        $note = $fiscalPeriod
            ? FinancialNote::where('fiscal_period_id', $fiscalPeriod->id)->first()
            : null;

        return view('financial-notes.index', compact('periods', 'fiscalPeriod', 'note'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fiscal_period_id' => ['required', 'exists:fiscal_periods,id'],
            'konten' => ['required', 'string'],
        ]);

        $fiscalPeriod = FiscalPeriod::findOrFail($data['fiscal_period_id']);

        if (!$fiscalPeriod->isOpen()) {
            return back()->withErrors(['konten' => 'CALK hanya bisa diedit selama periode masih terbuka.']);
        }

        FinancialNote::updateOrCreate(
            ['fiscal_period_id' => $data['fiscal_period_id']],
            ['konten' => $data['konten'], 'updated_by' => $request->user()->id]
        );

        return redirect()->route('financial-notes.index', ['fiscal_period_id' => $data['fiscal_period_id']])
            ->with('status', 'CALK berhasil disimpan.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\FiscalPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FiscalPeriodController extends Controller
{
    public function index(): View
    {
        $periods = FiscalPeriod::orderByDesc('tanggal_mulai')->get();

        return view('fiscal-periods.index', compact('periods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_periode' => ['required', 'string', 'max:50'],
            'tanggal_mulai' => ['required', 'date', 'before_or_equal:today'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        FiscalPeriod::create($data);

        return redirect()->route('fiscal-periods.index')->with('status', 'Periode berhasil ditambahkan.');
    }

    public function close(FiscalPeriod $fiscalPeriod): RedirectResponse
    {
        $fiscalPeriod->update(['status' => 'closed']);

        return redirect()->route('fiscal-periods.index')->with('status', 'Periode ditutup.');
    }
}
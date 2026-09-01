<?php

namespace App\Http\Controllers;

use App\Models\TaxSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxSettingController extends Controller
{
    public function index(): View
    {
        $settings = TaxSetting::orderBy('nama_pajak')->orderByDesc('berlaku_sejak')->get();

        return view('tax.settings', compact('settings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pajak' => ['required', 'string', 'max:50'],
            'tarif_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'berlaku_sejak' => ['required', 'date'],
        ]);

        TaxSetting::create($data);

        return redirect()->route('tax.settings')->with('status', 'Tarif pajak berhasil ditambahkan.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Consignee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsigneeController extends Controller
{
    public function index(): View
    {
        $consignees = Consignee::orderBy('nama')->get();

        return view('consignees.index', compact('consignees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'persentase_komisi' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Consignee::create($data);

        return redirect()->route('consignees.index')->with('status', 'Consignee berhasil ditambahkan.');
    }
}
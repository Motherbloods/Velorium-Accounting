<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::with('warehouses')->orderBy('nama_cabang')->get();

        return view('branches.index', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_cabang' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        Branch::create($data);

        return redirect()->route('branches.index')->with('status', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'nama_cabang' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        $branch->update($data);

        return redirect()->route('branches.index')->with('status', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('status', 'Cabang berhasil dihapus.');
    }
}
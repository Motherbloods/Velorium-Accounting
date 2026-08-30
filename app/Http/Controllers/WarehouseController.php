<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'nama_gudang' => ['required', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        DB::transaction(function () use ($data) {
            if ($data['is_default']) {
                Warehouse::where('is_default', true)->update(['is_default' => false]);
            }

            Warehouse::create($data);
        });

        return redirect()->route('branches.index')->with('status', 'Gudang berhasil ditambahkan.');
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $data = $request->validate([
            'nama_gudang' => ['required', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        DB::transaction(function () use ($data, $warehouse) {
            if ($data['is_default']) {
                Warehouse::where('id', '!=', $warehouse->id)->where('is_default', true)->update(['is_default' => false]);
            }

            $warehouse->update($data);
        });

        return redirect()->route('branches.index')->with('status', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        $warehouse->delete();

        return redirect()->route('branches.index')->with('status', 'Gudang berhasil dihapus.');
    }
}
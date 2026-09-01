<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryValuationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(protected InventoryValuationService $inventoryValuationService)
    {
    }

    public function index(): View
    {
        $products = Product::with('stocks.warehouse')->orderBy('nama_produk')->get();
        $warehouses = Warehouse::orderBy('nama_gudang')->get();

        return view('stock.index', compact('products', 'warehouses'));
    }

    public function adjustIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'harga_per_unit' => ['required', 'numeric', 'min:0'],
            'tanggal' => ['required', 'date'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $warehouse = Warehouse::findOrFail($data['warehouse_id']);

        $this->inventoryValuationService->stockIn(
            $product,
            $warehouse,
            $data['qty'],
            (string) $data['harga_per_unit'],
            $data['tanggal'],
            'ManualAdjustment',
            null
        );

        return back()->with('status', 'Stok masuk berhasil dicatat.');
    }

    public function adjustOut(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $warehouse = Warehouse::findOrFail($data['warehouse_id']);

        try {
            $this->inventoryValuationService->stockOut(
                $product,
                $warehouse,
                $data['qty'],
                $data['tanggal'],
                'ManualAdjustment',
                null
            );
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['qty' => $e->getMessage()]);
        }

        return back()->with('status', 'Stok keluar berhasil dicatat.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Exceptions\ValuationMethodLockedException;
use App\Models\Product;
use App\Services\InventoryValuationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected InventoryValuationService $inventoryValuationService)
    {
    }

    public function index(): View
    {
        $products = Product::orderBy('nama_produk')->paginate(20);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products,kode_produk'],
            'nama_produk' => ['required', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'metode_penilaian' => ['required', 'in:fifo,rata_rata'],
        ]);

        Product::create($data);

        return redirect()->route('products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $metodeTerkunci = $this->inventoryValuationService->hasAnyMovement($product);

        return view('products.edit', compact('product', 'metodeTerkunci'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'metode_penilaian' => ['required', 'in:fifo,rata_rata'],
        ]);

        $metodeBaru = $data['metode_penilaian'];
        unset($data['metode_penilaian']);

        $product->update($data);

        if ($metodeBaru !== $product->metode_penilaian) {
            try {
                $this->inventoryValuationService->changeValuationMethod($product, $metodeBaru);
            } catch (ValuationMethodLockedException $e) {
                return back()->withErrors(['metode_penilaian' => $e->getMessage()]);
            }
        }

        return redirect()->route('products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Produk berhasil dihapus.');
    }
}
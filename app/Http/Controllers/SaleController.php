<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\CoaAccount;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Warehouse;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(protected SaleService $saleService)
    {
    }

    public function index(): View
    {
        $sales = Sale::with('customer')->orderByDesc('tanggal')->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('nama')->get();
        $products = Product::orderBy('nama_produk')->get();
        $warehouses = Warehouse::orderBy('nama_gudang')->get();
        $kasBankAccounts = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('sales.create', compact('customers', 'products', 'warehouses', 'kasBankAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'tanggal' => ['required', 'date'],
            'tipe' => ['required', 'in:tunai,kredit'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'diskon_dagang' => ['nullable', 'numeric', 'min:0'],
            'kena_ppn' => ['nullable', 'boolean'],
            'coa_kas_bank_id' => ['required_if:tipe,tunai', 'nullable', 'exists:coa_accounts,id'],
            'termin_jatuh_tempo_hari' => ['nullable', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $sale = $this->saleService->create($data, $data['items'], $request->user());
        } catch (InsufficientStockException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('sales.show', $sale)->with('status', 'Penjualan berhasil dicatat.');
    }

    public function show(Sale $sale): View
    {
        $sale->load('items.product', 'customer', 'receivable', 'returns.items');

        return view('sales.show', compact('sale'));
    }

    public function createReturn(Sale $sale): View
    {
        $sale->load('items.product');

        return view('sales.create-return', compact('sale'));
    }

    public function storeReturn(Request $request, Sale $sale): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_item_id' => ['required', 'exists:sale_items,id'],
            'items.*.qty_retur' => ['required', 'integer', 'min:1'],
        ]);

        $this->saleService->createReturn($sale, $data['items'], $data['tanggal'], $data['keterangan'] ?? null, $request->user());

        return redirect()->route('sales.show', $sale)->with('status', 'Retur penjualan berhasil dicatat.');
    }
}
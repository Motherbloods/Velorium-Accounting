<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService)
    {
    }

    public function index(): View
    {
        $purchases = Purchase::with('supplier')->orderByDesc('tanggal')->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $products = Product::orderBy('nama_produk')->get();
        $warehouses = Warehouse::orderBy('nama_gudang')->get();
        $kasBankAccounts = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112'])->get();

        return view('purchases.create', compact('suppliers', 'products', 'warehouses', 'kasBankAccounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'tanggal' => ['required', 'date'],
            'tipe' => ['required', 'in:tunai,kredit'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'diskon_dagang' => ['nullable', 'numeric', 'min:0'],
            'kena_ppn' => ['nullable', 'boolean'],
            'coa_kas_bank_id' => ['required_if:tipe,tunai', 'nullable', 'exists:coa_accounts,id'],
            'termin_jatuh_tempo_hari' => ['nullable', 'integer', 'min:1'],
            'termin_diskon_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'termin_diskon_hari' => ['nullable', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
        ]);

        $purchase = $this->purchaseService->create($data, $data['items'], $request->user());

        return redirect()->route('purchases.show', $purchase)->with('status', 'Pembelian berhasil dicatat.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load('items.product', 'supplier', 'payable', 'returns.items');

        return view('purchases.show', compact('purchase'));
    }

    public function createReturn(Purchase $purchase): View
    {
        $purchase->load('items.product');

        return view('purchases.create-return', compact('purchase'));
    }

    public function storeReturn(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_item_id' => ['required', 'exists:purchase_items,id'],
            'items.*.qty_retur' => ['required', 'integer', 'min:1'],
        ]);

        $this->purchaseService->createReturn($purchase, $data['items'], $data['tanggal'], $data['keterangan'] ?? null, $request->user());

        return redirect()->route('purchases.show', $purchase)->with('status', 'Retur pembelian berhasil dicatat.');
    }
}
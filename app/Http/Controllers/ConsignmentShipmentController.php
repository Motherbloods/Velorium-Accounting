<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Consignee;
use App\Models\ConsignmentShipment;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\ConsignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsignmentShipmentController extends Controller
{
    public function __construct(protected ConsignmentService $consignmentService)
    {
    }

    public function index(): View
    {
        $shipments = ConsignmentShipment::with('consignee')->orderByDesc('tanggal_kirim')->paginate(20);

        return view('consignment.shipments.index', compact('shipments'));
    }

    public function create(): View
    {
        $consignees = Consignee::orderBy('nama')->get();
        $products = Product::orderBy('nama_produk')->get();
        $warehouses = Warehouse::orderBy('nama_gudang')->get();

        return view('consignment.shipments.create', compact('consignees', 'products', 'warehouses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'consignee_id' => ['required', 'exists:consignees,id'],
            'tanggal_kirim' => ['required', 'date'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty_kirim' => ['required', 'integer', 'min:1'],
            'items.*.harga_titip' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $shipment = $this->consignmentService->createShipment($data, $data['items'], $request->user());
        } catch (InsufficientStockException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('consignment.shipments.show', $shipment)->with('status', 'Pengiriman konsinyasi berhasil dicatat.');
    }

    public function show(ConsignmentShipment $shipment): View
    {
        $shipment->load('items.product', 'consignee', 'salesReports.items', 'returns.product');

        return view('consignment.shipments.show', compact('shipment'));
    }
}
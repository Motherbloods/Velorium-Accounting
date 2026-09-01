<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentShipment;
use App\Services\ConsignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsignmentSalesReportController extends Controller
{
    public function __construct(protected ConsignmentService $consignmentService)
    {
    }

    public function create(ConsignmentShipment $shipment): View
    {
        $shipment->load('items.product');

        return view('consignment.sales-reports.create', compact('shipment'));
    }

    public function store(Request $request, ConsignmentShipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_lapor' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.shipment_item_id' => ['required', 'exists:consignment_shipment_items,id'],
            'items.*.qty_terjual' => ['required', 'integer', 'min:0'],
        ]);

        $items = array_filter($data['items'], fn($item) => (int) $item['qty_terjual'] > 0);

        if (empty($items)) {
            return back()->withErrors(['items' => 'Isi minimal satu qty terjual.']);
        }

        $this->consignmentService->recordSalesReport($shipment, $items, $data['tanggal_lapor'], $request->user());

        return redirect()->route('consignment.shipments.show', $shipment)->with('status', 'Laporan penjualan konsinyasi berhasil dicatat.');
    }
}
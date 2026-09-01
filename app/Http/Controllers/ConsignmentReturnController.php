<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentShipment;
use App\Services\ConsignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsignmentReturnController extends Controller
{
    public function __construct(protected ConsignmentService $consignmentService)
    {
    }

    public function create(ConsignmentShipment $shipment): View
    {
        $shipment->load('items.product');

        return view('consignment.returns.create', compact('shipment'));
    }

    public function store(Request $request, ConsignmentShipment $shipment): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_retur' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.shipment_item_id' => ['required', 'exists:consignment_shipment_items,id'],
            'items.*.qty_retur' => ['required', 'integer', 'min:0'],
        ]);

        $items = array_filter($data['items'], fn($item) => (int) $item['qty_retur'] > 0);

        if (empty($items)) {
            return back()->withErrors(['items' => 'Isi minimal satu qty retur.']);
        }

        $this->consignmentService->recordReturn($shipment, $items, $data['tanggal_retur'], $request->user());

        return redirect()->route('consignment.shipments.show', $shipment)->with('status', 'Retur konsinyasi berhasil dicatat.');
    }
}
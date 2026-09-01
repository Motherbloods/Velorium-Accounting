<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\ConsignmentReturn;
use App\Models\ConsignmentSalesReport;
use App\Models\ConsignmentSalesReportItem;
use App\Models\ConsignmentShipment;
use App\Models\ConsignmentShipmentItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class ConsignmentService
{
    public function __construct(
        protected JournalService $journalService,
        protected DocumentNumberService $documentNumberService,
        protected InventoryValuationService $inventoryValuationService
    ) {
    }

    public function createShipment(array $data, array $items, User $user): ConsignmentShipment
    {
        return DB::transaction(function () use ($data, $items, $user) {
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);

            $totalHpp = '0';
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qtyKirim = (int) $item['qty_kirim'];

                $hppTotal = $this->inventoryValuationService->stockOut(
                    $product,
                    $warehouse,
                    $qtyKirim,
                    $data['tanggal_kirim'],
                    ConsignmentShipmentItem::class,
                    null
                );

                $hppSatuan = bcdiv($hppTotal, (string) $qtyKirim, 2);
                $totalHpp = bcadd($totalHpp, $hppTotal, 2);

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'qty_kirim' => $qtyKirim,
                    'harga_titip' => $item['harga_titip'],
                    'hpp_satuan' => $hppSatuan,
                ];

                $product->update(['stok_konsinyasi' => $product->stok_konsinyasi + $qtyKirim]);
            }

            $coaPersediaanKonsinyasi = CoaAccount::where('kode_akun', '116')->firstOrFail();
            $coaPersediaan = CoaAccount::where('kode_akun', '115')->firstOrFail();

            $nomorKonsinyasi = $this->documentNumberService->generate('KS', $data['tanggal_kirim']);

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal_kirim'],
                'keterangan' => "Pengiriman konsinyasi {$nomorKonsinyasi}",
                'referensi_type' => ConsignmentShipment::class,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaPersediaanKonsinyasi->id, 'debit' => $totalHpp, 'kredit' => 0],
                ['coa_account_id' => $coaPersediaan->id, 'debit' => 0, 'kredit' => $totalHpp],
            ], 'KS');

            $shipment = ConsignmentShipment::create([
                'nomor_konsinyasi' => $nomorKonsinyasi,
                'consignee_id' => $data['consignee_id'],
                'tanggal_kirim' => $data['tanggal_kirim'],
                'status' => 'berjalan',
                'journal_entry_id' => $entry->id,
                'branch_id' => $data['branch_id'] ?? null,
                'warehouse_id' => $warehouse->id,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $preparedItem['shipment_id'] = $shipment->id;
                ConsignmentShipmentItem::create($preparedItem);
            }

            $entry->update(['referensi_id' => $shipment->id]);

            return $shipment->fresh('items');
        });
    }

    public function recordSalesReport(ConsignmentShipment $shipment, array $items, string $tanggalLapor, User $user): ConsignmentSalesReport
    {
        return DB::transaction(function () use ($shipment, $items, $tanggalLapor, $user) {
            $totalQtyTerjual = 0;
            $totalPenjualan = '0';
            $totalHpp = '0';
            $preparedItems = [];

            foreach ($items as $item) {
                $shipmentItem = ConsignmentShipmentItem::findOrFail($item['shipment_item_id']);
                $qtyTerjual = (int) $item['qty_terjual'];

                if ($qtyTerjual > $shipmentItem->sisaBelumTerjual()) {
                    abort(422, "Qty terjual melebihi sisa barang konsinyasi untuk produk {$shipmentItem->product->nama_produk}.");
                }

                $subtotalPenjualan = bcmul((string) $qtyTerjual, (string) $shipmentItem->harga_titip, 2);
                $subtotalHpp = bcmul((string) $qtyTerjual, (string) $shipmentItem->hpp_satuan, 2);

                $totalQtyTerjual += $qtyTerjual;
                $totalPenjualan = bcadd($totalPenjualan, $subtotalPenjualan, 2);
                $totalHpp = bcadd($totalHpp, $subtotalHpp, 2);

                $preparedItems[] = [
                    'shipment_item_id' => $shipmentItem->id,
                    'product_id' => $shipmentItem->product_id,
                    'qty_terjual' => $qtyTerjual,
                    'harga_titip' => $shipmentItem->harga_titip,
                    'hpp_satuan' => $shipmentItem->hpp_satuan,
                    'subtotal_penjualan' => $subtotalPenjualan,
                    'subtotal_hpp' => $subtotalHpp,
                ];

                $shipmentItem->update(['qty_terjual' => $shipmentItem->qty_terjual + $qtyTerjual]);

                $product = $shipmentItem->product;
                $product->update(['stok_konsinyasi' => max(0, $product->stok_konsinyasi - $qtyTerjual)]);
            }

            $persentaseKomisi = $shipment->consignee->persentase_komisi;
            $totalKomisi = bcmul($totalPenjualan, bcdiv((string) $persentaseKomisi, '100', 6), 2);
            $totalDiterima = bcsub($totalPenjualan, $totalKomisi, 2);

            $coaPiutangConsignee = CoaAccount::where('kode_akun', '113')->firstOrFail();
            $coaPendapatanKonsinyasi = CoaAccount::where('kode_akun', '42')->firstOrFail();
            $coaHpp = CoaAccount::where('kode_akun', '51')->firstOrFail();
            $coaPersediaanKonsinyasi = CoaAccount::where('kode_akun', '116')->firstOrFail();
            $coaBebanKomisi = CoaAccount::where('kode_akun', '52')->firstOrFail();

            $nomorLaporan = $this->documentNumberService->generate('LK', $tanggalLapor);

            $entry = $this->journalService->create([
                'tanggal' => $tanggalLapor,
                'keterangan' => "Laporan penjualan konsinyasi {$nomorLaporan} — {$shipment->nomor_konsinyasi}",
                'referensi_type' => ConsignmentSalesReport::class,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaPiutangConsignee->id, 'debit' => $totalPenjualan, 'kredit' => 0],
                ['coa_account_id' => $coaPendapatanKonsinyasi->id, 'debit' => 0, 'kredit' => $totalPenjualan],
                ['coa_account_id' => $coaHpp->id, 'debit' => $totalHpp, 'kredit' => 0],
                ['coa_account_id' => $coaPersediaanKonsinyasi->id, 'debit' => 0, 'kredit' => $totalHpp],
                ['coa_account_id' => $coaBebanKomisi->id, 'debit' => $totalKomisi, 'kredit' => 0],
                ['coa_account_id' => $coaPiutangConsignee->id, 'debit' => 0, 'kredit' => $totalKomisi],
            ], 'LK');

            $salesReport = ConsignmentSalesReport::create([
                'shipment_id' => $shipment->id,
                'tanggal_lapor' => $tanggalLapor,
                'total_qty_terjual' => $totalQtyTerjual,
                'total_penjualan' => $totalPenjualan,
                'total_hpp' => $totalHpp,
                'total_komisi' => $totalKomisi,
                'total_diterima' => $totalDiterima,
                'journal_entry_id' => $entry->id,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $preparedItem['consignment_sales_report_id'] = $salesReport->id;
                ConsignmentSalesReportItem::create($preparedItem);
            }

            $entry->update(['referensi_id' => $salesReport->id]);

            $this->syncShipmentStatus($shipment);

            return $salesReport->fresh('items');
        });
    }

    public function recordReturn(ConsignmentShipment $shipment, array $items, string $tanggalRetur, User $user): array
    {
        return DB::transaction(function () use ($shipment, $items, $tanggalRetur, $user) {
            $totalHpp = '0';
            $consignmentReturns = [];

            foreach ($items as $item) {
                $shipmentItem = ConsignmentShipmentItem::findOrFail($item['shipment_item_id']);
                $qtyRetur = (int) $item['qty_retur'];

                if ($qtyRetur > $shipmentItem->sisaBelumTerjual()) {
                    abort(422, "Qty retur melebihi sisa barang konsinyasi untuk produk {$shipmentItem->product->nama_produk}.");
                }

                $subtotalHpp = bcmul((string) $qtyRetur, (string) $shipmentItem->hpp_satuan, 2);
                $totalHpp = bcadd($totalHpp, $subtotalHpp, 2);

                $shipmentItem->update(['qty_retur' => $shipmentItem->qty_retur + $qtyRetur]);

                $product = $shipmentItem->product;
                $product->update(['stok_konsinyasi' => max(0, $product->stok_konsinyasi - $qtyRetur)]);

                $consignmentReturns[] = [
                    'shipment_id' => $shipment->id,
                    'product_id' => $shipmentItem->product_id,
                    'qty_retur' => $qtyRetur,
                    'tanggal_retur' => $tanggalRetur,
                ];
            }

            $coaPersediaan = CoaAccount::where('kode_akun', '115')->firstOrFail();
            $coaPersediaanKonsinyasi = CoaAccount::where('kode_akun', '116')->firstOrFail();

            $entry = $this->journalService->create([
                'tanggal' => $tanggalRetur,
                'keterangan' => "Retur konsinyasi {$shipment->nomor_konsinyasi}",
                'referensi_type' => ConsignmentShipment::class,
                'referensi_id' => $shipment->id,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaPersediaan->id, 'debit' => $totalHpp, 'kredit' => 0],
                ['coa_account_id' => $coaPersediaanKonsinyasi->id, 'debit' => 0, 'kredit' => $totalHpp],
            ]);

            $created = [];

            foreach ($consignmentReturns as $data) {
                $data['journal_entry_id'] = $entry->id;
                $created[] = ConsignmentReturn::create($data);

                $product = Product::findOrFail($data['product_id']);
                $warehouse = Warehouse::findOrFail($shipment->warehouse_id);

                $shipmentItem = ConsignmentShipmentItem::where('shipment_id', $shipment->id)
                    ->where('product_id', $data['product_id'])
                    ->first();

                $this->inventoryValuationService->returPenjualanMasukKembali(
                    $product,
                    $warehouse,
                    $data['qty_retur'],
                    (string) $shipmentItem->hpp_satuan,
                    $tanggalRetur,
                    ConsignmentReturn::class,
                    null
                );
            }

            $this->syncShipmentStatus($shipment);

            return $created;
        });
    }

    protected function syncShipmentStatus(ConsignmentShipment $shipment): void
    {
        $shipment->load('items');

        $semuaSelesai = $shipment->items->every(fn(ConsignmentShipmentItem $item) => $item->sisaBelumTerjual() <= 0);

        $shipment->update(['status' => $semuaSelesai ? 'selesai' : 'berjalan']);
    }
}
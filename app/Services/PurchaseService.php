<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected JournalService $journalService,
        protected DocumentNumberService $documentNumberService,
        protected InventoryValuationService $inventoryValuationService,
        protected PayableService $payableService,
        protected TaxService $taxService
    ) {
    }

    public function create(array $data, array $items, User $user): Purchase
    {
        return DB::transaction(function () use ($data, $items, $user) {
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);

            $subtotal = '0';
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineSubtotal = bcmul((string) $item['qty'], (string) $item['harga_satuan'], 2);
                $subtotal = bcadd($subtotal, $lineSubtotal, 2);

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $lineSubtotal,
                    'warehouse_id' => $warehouse->id,
                    '_product' => $product,
                ];
            }

            $diskonDagang = (string) ($data['diskon_dagang'] ?? 0);
            $dppPpn = bcsub($subtotal, $diskonDagang, 2);

            $ppn = '0';
            $tarifPpn = '0';

            if (!empty($data['kena_ppn'])) {
                $hitung = $this->taxService->hitungPpn($dppPpn, $data['tanggal']);
                $ppn = $hitung['jumlah_pajak'];
                $tarifPpn = $hitung['tarif_persen'];
            }

            $total = bcadd($dppPpn, $ppn, 2);

            $coaPersediaan = CoaAccount::where('kode_akun', '115')->firstOrFail();

            $coaPembayaran = $data['tipe'] === 'tunai'
                ? CoaAccount::findOrFail($data['coa_kas_bank_id'])
                : CoaAccount::where('kode_akun', '211')->firstOrFail();

            $lines = [
                ['coa_account_id' => $coaPersediaan->id, 'debit' => $dppPpn, 'kredit' => 0],
            ];

            if (bccomp($ppn, '0', 2) > 0) {
                $coaPpnMasukan = CoaAccount::where('kode_akun', '117')->firstOrFail();
                $lines[] = ['coa_account_id' => $coaPpnMasukan->id, 'debit' => $ppn, 'kredit' => 0];
            }

            $lines[] = ['coa_account_id' => $coaPembayaran->id, 'debit' => 0, 'kredit' => $total];

            $nomorTransaksi = $this->documentNumberService->generate('PB', $data['tanggal']);

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => "Pembelian {$nomorTransaksi}",
                'referensi_type' => Purchase::class,
                'created_by' => $user->id,
            ], $lines, 'PB');

            $purchase = Purchase::create([
                'nomor_transaksi' => $nomorTransaksi,
                'supplier_id' => $data['supplier_id'],
                'tanggal' => $data['tanggal'],
                'tipe' => $data['tipe'],
                'subtotal' => $subtotal,
                'diskon_dagang' => $diskonDagang,
                'dpp_ppn' => $dppPpn,
                'ppn' => $ppn,
                'total' => $total,
                'termin_diskon_persen' => $data['termin_diskon_persen'] ?? null,
                'termin_diskon_hari' => $data['termin_diskon_hari'] ?? null,
                'coa_pembayaran_id' => $coaPembayaran->id,
                'journal_entry_id' => $entry->id,
                'branch_id' => $data['branch_id'] ?? null,
                'warehouse_id' => $warehouse->id,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $product = $preparedItem['_product'];
                unset($preparedItem['_product']);

                $preparedItem['purchase_id'] = $purchase->id;
                $purchaseItem = PurchaseItem::create($preparedItem);

                $this->inventoryValuationService->stockIn(
                    $product,
                    $warehouse,
                    $preparedItem['qty'],
                    (string) $preparedItem['harga_satuan'],
                    $data['tanggal'],
                    PurchaseItem::class,
                    $purchaseItem->id
                );
            }

            $entry->update(['referensi_id' => $purchase->id]);

            if (bccomp($ppn, '0', 2) > 0) {
                $this->taxService->recordPpnMasukan(
                    Purchase::class,
                    $purchase->id,
                    $dppPpn,
                    $tarifPpn,
                    $ppn,
                    date('Y-m', strtotime($data['tanggal'])),
                    $entry->id
                );
            }

            if ($data['tipe'] === 'kredit') {
                $payable = $this->payableService->create([
                    'nomor_hutang' => $nomorTransaksi,
                    'supplier_id' => $data['supplier_id'],
                    'tanggal' => $data['tanggal'],
                    'jenis' => 'usaha',
                    'total_hutang' => $total,
                    'termin_jatuh_tempo_hari' => $data['termin_jatuh_tempo_hari'] ?? 30,
                    'termin_diskon_persen' => $data['termin_diskon_persen'] ?? null,
                    'termin_diskon_hari' => $data['termin_diskon_hari'] ?? null,
                    'referensi_type' => Purchase::class,
                    'referensi_id' => $purchase->id,
                    'journal_entry_id' => $entry->id,
                    'branch_id' => $data['branch_id'] ?? null,
                ]);

                $purchase->update(['payable_id' => $payable->id]);
            }

            return $purchase->fresh('items');
        });
    }

    public function createReturn(Purchase $purchase, array $items, string $tanggal, ?string $keterangan, User $user): PurchaseReturn
    {
        return DB::transaction(function () use ($purchase, $items, $tanggal, $keterangan, $user) {
            $jumlahTotal = '0';
            $preparedItems = [];

            foreach ($items as $item) {
                $purchaseItem = PurchaseItem::findOrFail($item['purchase_item_id']);
                $product = Product::findOrFail($purchaseItem->product_id);
                $warehouse = Warehouse::findOrFail($purchaseItem->warehouse_id);

                $qtyRetur = (int) $item['qty_retur'];
                $subtotalRetur = bcmul((string) $qtyRetur, (string) $purchaseItem->harga_satuan, 2);
                $jumlahTotal = bcadd($jumlahTotal, $subtotalRetur, 2);

                if ($product->metode_penilaian === 'fifo') {
                    $this->inventoryValuationService->returFifoLayerAsal(
                        $product,
                        $warehouse,
                        PurchaseItem::class,
                        $purchaseItem->id,
                        $qtyRetur
                    );
                } else {
                    $this->inventoryValuationService->stockOut(
                        $product,
                        $warehouse,
                        $qtyRetur,
                        $tanggal,
                        PurchaseReturnItem::class,
                        null
                    );
                }

                $preparedItems[] = [
                    'purchase_item_id' => $purchaseItem->id,
                    'product_id' => $product->id,
                    'qty_retur' => $qtyRetur,
                    'harga_satuan' => $purchaseItem->harga_satuan,
                    'subtotal' => $subtotalRetur,
                ];
            }

            $coaPersediaan = CoaAccount::where('kode_akun', '115')->firstOrFail();
            $coaPembayaran = CoaAccount::findOrFail($purchase->coa_pembayaran_id);

            $entry = $this->journalService->create([
                'tanggal' => $tanggal,
                'keterangan' => $keterangan ?? "Retur pembelian {$purchase->nomor_transaksi}",
                'referensi_type' => Purchase::class,
                'referensi_id' => $purchase->id,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaPembayaran->id, 'debit' => $jumlahTotal, 'kredit' => 0],
                ['coa_account_id' => $coaPersediaan->id, 'debit' => 0, 'kredit' => $jumlahTotal],
            ], 'RB');

            $purchaseReturn = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'jumlah' => $jumlahTotal,
                'journal_entry_id' => $entry->id,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $preparedItem['purchase_return_id'] = $purchaseReturn->id;
                PurchaseReturnItem::create($preparedItem);
            }

            return $purchaseReturn->fresh('items');
        });
    }
}
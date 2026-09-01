<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\CoaAccount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected JournalService $journalService,
        protected DocumentNumberService $documentNumberService,
        protected InventoryValuationService $inventoryValuationService,
        protected ReceivableService $receivableService,
        protected TaxService $taxService
    ) {
    }

    public function create(array $data, array $items, User $user): Sale
    {
        return DB::transaction(function () use ($data, $items, $user) {
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);

            $subtotal = '0';
            $subtotalHpp = '0';
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineSubtotal = bcmul((string) $item['qty'], (string) $item['harga_satuan'], 2);
                $subtotal = bcadd($subtotal, $lineSubtotal, 2);

                $hppTotal = $this->inventoryValuationService->stockOut(
                    $product,
                    $warehouse,
                    (int) $item['qty'],
                    $data['tanggal'],
                    Sale::class,
                    null
                );

                $hppSatuan = bcdiv($hppTotal, (string) $item['qty'], 2);
                $subtotalHpp = bcadd($subtotalHpp, $hppTotal, 2);

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $lineSubtotal,
                    'hpp_satuan' => $hppSatuan,
                    'subtotal_hpp' => $hppTotal,
                    'warehouse_id' => $warehouse->id,
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

            $coaPendapatan = CoaAccount::where('kode_akun', '41')->firstOrFail();
            $coaHpp = CoaAccount::where('kode_akun', '51')->firstOrFail();
            $coaPersediaan = CoaAccount::where('kode_akun', '115')->firstOrFail();

            $coaPembayaran = $data['tipe'] === 'tunai'
                ? CoaAccount::findOrFail($data['coa_kas_bank_id'])
                : CoaAccount::where('kode_akun', '113')->firstOrFail();

            $lines = [
                ['coa_account_id' => $coaPembayaran->id, 'debit' => $total, 'kredit' => 0],
                ['coa_account_id' => $coaPendapatan->id, 'debit' => 0, 'kredit' => $dppPpn],
            ];

            if (bccomp($ppn, '0', 2) > 0) {
                $coaPpnKeluaran = CoaAccount::where('kode_akun', '216')->firstOrFail();
                $lines[] = ['coa_account_id' => $coaPpnKeluaran->id, 'debit' => 0, 'kredit' => $ppn];
            }

            $lines[] = ['coa_account_id' => $coaHpp->id, 'debit' => $subtotalHpp, 'kredit' => 0];
            $lines[] = ['coa_account_id' => $coaPersediaan->id, 'debit' => 0, 'kredit' => $subtotalHpp];

            $nomorTransaksi = $this->documentNumberService->generate('PJ', $data['tanggal']);

            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => "Penjualan {$nomorTransaksi}",
                'referensi_type' => Sale::class,
                'created_by' => $user->id,
            ], $lines, 'PJ');

            $sale = Sale::create([
                'nomor_transaksi' => $nomorTransaksi,
                'customer_id' => $data['customer_id'],
                'tanggal' => $data['tanggal'],
                'tipe' => $data['tipe'],
                'subtotal' => $subtotal,
                'diskon_dagang' => $diskonDagang,
                'dpp_ppn' => $dppPpn,
                'ppn' => $ppn,
                'total' => $total,
                'termin_diskon_persen' => $data['termin_diskon_persen'] ?? null,
                'termin_diskon_hari' => $data['termin_diskon_hari'] ?? null,
                'termin_jatuh_tempo_hari' => $data['termin_jatuh_tempo_hari'] ?? null,
                'coa_pembayaran_id' => $coaPembayaran->id,
                'journal_entry_id' => $entry->id,
                'branch_id' => $data['branch_id'] ?? null,
                'warehouse_id' => $warehouse->id,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $preparedItem['sale_id'] = $sale->id;
                SaleItem::create($preparedItem);
            }

            $entry->update(['referensi_id' => $sale->id]);

            if (bccomp($ppn, '0', 2) > 0) {
                $this->taxService->recordPpnKeluaran(
                    Sale::class,
                    $sale->id,
                    $dppPpn,
                    $tarifPpn,
                    $ppn,
                    date('Y-m', strtotime($data['tanggal'])),
                    $entry->id
                );
            }

            if ($data['tipe'] === 'kredit') {
                $receivable = $this->receivableService->create([
                    'nomor_invoice' => $nomorTransaksi,
                    'customer_id' => $data['customer_id'],
                    'tanggal' => $data['tanggal'],
                    'total_tagihan' => $total,
                    'termin_jatuh_tempo_hari' => $data['termin_jatuh_tempo_hari'] ?? 30,
                    'termin_diskon_persen' => $data['termin_diskon_persen'] ?? null,
                    'termin_diskon_hari' => $data['termin_diskon_hari'] ?? null,
                    'referensi_type' => Sale::class,
                    'referensi_id' => $sale->id,
                    'journal_entry_id' => $entry->id,
                    'branch_id' => $data['branch_id'] ?? null,
                ]);

                $sale->update(['receivable_id' => $receivable->id]);
            }

            return $sale->fresh('items');
        });
    }

    public function createReturn(Sale $sale, array $items, string $tanggal, ?string $keterangan, User $user): SalesReturn
    {
        return DB::transaction(function () use ($sale, $items, $tanggal, $keterangan, $user) {
            $jumlahTotal = '0';
            $hppTotal = '0';
            $preparedItems = [];

            foreach ($items as $item) {
                $saleItem = SaleItem::findOrFail($item['sale_item_id']);
                $product = Product::findOrFail($saleItem->product_id);
                $warehouse = Warehouse::findOrFail($saleItem->warehouse_id);

                $qtyRetur = (int) $item['qty_retur'];
                $subtotalRetur = bcmul((string) $qtyRetur, (string) $saleItem->harga_satuan, 2);
                $subtotalHppRetur = bcmul((string) $qtyRetur, (string) $saleItem->hpp_satuan, 2);

                $jumlahTotal = bcadd($jumlahTotal, $subtotalRetur, 2);
                $hppTotal = bcadd($hppTotal, $subtotalHppRetur, 2);

                $this->inventoryValuationService->returPenjualanMasukKembali(
                    $product,
                    $warehouse,
                    $qtyRetur,
                    (string) $saleItem->hpp_satuan,
                    $tanggal,
                    SalesReturnItem::class,
                    null
                );

                $preparedItems[] = [
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $product->id,
                    'qty_retur' => $qtyRetur,
                    'hpp_satuan' => $saleItem->hpp_satuan,
                    'subtotal_hpp' => $subtotalHppRetur,
                ];
            }

            $coaRetur = CoaAccount::where('kode_akun', '411')->firstOrFail();
            $coaPersediaan = CoaAccount::where('kode_akun', '115')->firstOrFail();
            $coaHpp = CoaAccount::where('kode_akun', '51')->firstOrFail();
            $coaPembayaran = CoaAccount::findOrFail($sale->coa_pembayaran_id);

            $entry = $this->journalService->create([
                'tanggal' => $tanggal,
                'keterangan' => $keterangan ?? "Retur penjualan {$sale->nomor_transaksi}",
                'referensi_type' => Sale::class,
                'referensi_id' => $sale->id,
                'created_by' => $user->id,
            ], [
                ['coa_account_id' => $coaRetur->id, 'debit' => $jumlahTotal, 'kredit' => 0],
                ['coa_account_id' => $coaPembayaran->id, 'debit' => 0, 'kredit' => $jumlahTotal],
                ['coa_account_id' => $coaPersediaan->id, 'debit' => $hppTotal, 'kredit' => 0],
                ['coa_account_id' => $coaHpp->id, 'debit' => 0, 'kredit' => $hppTotal],
            ], 'RJ');

            $salesReturn = SalesReturn::create([
                'sale_id' => $sale->id,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'jumlah' => $jumlahTotal,
                'hpp_retur' => $hppTotal,
                'journal_entry_id' => $entry->id,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $preparedItem['sales_return_id'] = $salesReturn->id;
                SalesReturnItem::create($preparedItem);
            }

            return $salesReturn->fresh('items');
        });
    }
}
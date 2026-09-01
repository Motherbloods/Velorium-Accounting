<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ValuationMethodLockedException;
use App\Models\InventoryLayer;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class InventoryValuationService
{
    public function stockIn(
        Product $product,
        Warehouse $warehouse,
        int $qty,
        string $hargaPerUnit,
        string $tanggal,
        ?string $referensiType = null,
        ?int $referensiId = null
    ): void {
        DB::transaction(function () use ($product, $warehouse, $qty, $hargaPerUnit, $tanggal, $referensiType, $referensiId) {
            if ($product->metode_penilaian === 'fifo') {
                InventoryLayer::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'tanggal_masuk' => $tanggal,
                    'qty_masuk' => $qty,
                    'qty_sisa' => $qty,
                    'harga_per_unit' => $hargaPerUnit,
                    'referensi_type' => $referensiType,
                    'referensi_id' => $referensiId,
                ]);
            } else {
                $stock = $this->lockProductStock($product, $warehouse);
                $qtyLama = $stock->qty;
                $hargaLama = $product->harga_rata_rata;

                $hargaBaruNumerator = bcadd(
                    bcmul((string) $qtyLama, (string) $hargaLama, 2),
                    bcmul((string) $qty, $hargaPerUnit, 2),
                    2
                );
                $qtyBaru = $qtyLama + $qty;
                $hargaBaru = $qtyBaru > 0 ? bcdiv($hargaBaruNumerator, (string) $qtyBaru, 2) : '0';

                $product->update(['harga_rata_rata' => $hargaBaru]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'tanggal' => $tanggal,
                    'tipe' => 'masuk',
                    'qty' => $qty,
                    'harga_per_unit' => $hargaPerUnit,
                    'referensi_type' => $referensiType,
                    'referensi_id' => $referensiId,
                ]);
            }

            $this->adjustStock($product, $warehouse, $qty);
        });
    }

    public function stockOut(
        Product $product,
        Warehouse $warehouse,
        int $qty,
        string $tanggal,
        ?string $referensiType = null,
        ?int $referensiId = null
    ): string {
        return DB::transaction(function () use ($product, $warehouse, $qty, $tanggal, $referensiType, $referensiId) {
            $tersedia = $this->availableQty($product, $warehouse);

            if ($qty > $tersedia) {
                throw new InsufficientStockException($product->nama_produk, $tersedia, $qty);
            }

            if ($product->metode_penilaian === 'fifo') {
                $hpp = $this->consumeFifoLayers($product, $warehouse, $qty);
            } else {
                $hargaBerlaku = $product->harga_rata_rata;
                $hpp = bcmul((string) $qty, $hargaBerlaku, 2);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'tanggal' => $tanggal,
                    'tipe' => 'keluar',
                    'qty' => $qty,
                    'harga_per_unit' => $hargaBerlaku,
                    'referensi_type' => $referensiType,
                    'referensi_id' => $referensiId,
                ]);
            }

            $this->adjustStock($product, $warehouse, -$qty);

            return $hpp;
        });
    }

    protected function consumeFifoLayers(Product $product, Warehouse $warehouse, int $qty): string
    {
        $sisaDibutuhkan = $qty;
        $totalHpp = '0';

        $layers = InventoryLayer::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($layers as $layer) {
            if ($sisaDibutuhkan <= 0) {
                break;
            }

            $ambil = min($layer->qty_sisa, $sisaDibutuhkan);
            $totalHpp = bcadd($totalHpp, bcmul((string) $ambil, (string) $layer->harga_per_unit, 2), 2);

            $layer->qty_sisa -= $ambil;
            $layer->save();

            $sisaDibutuhkan -= $ambil;
        }

        return $totalHpp;
    }

    public function availableQty(Product $product, Warehouse $warehouse): int
    {
        return (int) ProductStock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->value('qty') ?? 0;
    }

    protected function lockProductStock(Product $product, Warehouse $warehouse): ProductStock
    {
        $stock = ProductStock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            $stock = ProductStock::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'qty' => 0,
            ]);
        }

        return $stock;
    }

    protected function adjustStock(Product $product, Warehouse $warehouse, int $delta): void
    {
        $stock = $this->lockProductStock($product, $warehouse);
        $stock->qty += $delta;
        $stock->save();

        $totalStok = (int) ProductStock::where('product_id', $product->id)->sum('qty');
        $product->update(['stok_gudang' => $totalStok]);
    }

    public function returFifoLayerAsal(
        Product $product,
        Warehouse $warehouse,
        string $referensiType,
        int $referensiId,
        int $qtyRetur
    ): void {
        $layer = InventoryLayer::where('referensi_type', $referensiType)
            ->where('referensi_id', $referensiId)
            ->where('product_id', $product->id)
            ->lockForUpdate()
            ->first();

        if ($layer) {
            $layer->qty_sisa = max(0, $layer->qty_sisa - $qtyRetur);
            $layer->save();

            $this->adjustStock($product, $warehouse, -$qtyRetur);
        }
    }

    public function returPenjualanMasukKembali(
        Product $product,
        Warehouse $warehouse,
        int $qtyRetur,
        string $hppSatuan,
        string $tanggal,
        ?string $referensiType = null,
        ?int $referensiId = null
    ): void {
        DB::transaction(function () use ($product, $warehouse, $qtyRetur, $hppSatuan, $tanggal, $referensiType, $referensiId) {
            if ($product->metode_penilaian === 'fifo') {
                InventoryLayer::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'tanggal_masuk' => $tanggal,
                    'qty_masuk' => $qtyRetur,
                    'qty_sisa' => $qtyRetur,
                    'harga_per_unit' => $hppSatuan,
                    'referensi_type' => $referensiType,
                    'referensi_id' => $referensiId,
                ]);
            } else {
                $stock = $this->lockProductStock($product, $warehouse);
                $qtyLama = $stock->qty;
                $hargaLama = $product->harga_rata_rata;

                $hargaBaruNumerator = bcadd(
                    bcmul((string) $qtyLama, (string) $hargaLama, 2),
                    bcmul((string) $qtyRetur, $hppSatuan, 2),
                    2
                );
                $qtyBaru = $qtyLama + $qtyRetur;
                $hargaBaru = $qtyBaru > 0 ? bcdiv($hargaBaruNumerator, (string) $qtyBaru, 2) : '0';

                $product->update(['harga_rata_rata' => $hargaBaru]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'tanggal' => $tanggal,
                    'tipe' => 'masuk',
                    'qty' => $qtyRetur,
                    'harga_per_unit' => $hppSatuan,
                    'referensi_type' => $referensiType,
                    'referensi_id' => $referensiId,
                ]);
            }

            $this->adjustStock($product, $warehouse, $qtyRetur);
        });
    }

    public function hasAnyMovement(Product $product): bool
    {
        return InventoryLayer::where('product_id', $product->id)->exists()
            || InventoryMovement::where('product_id', $product->id)->exists();
    }

    public function changeValuationMethod(Product $product, string $metodeBaru): void
    {
        if ($this->hasAnyMovement($product)) {
            throw new ValuationMethodLockedException();
        }

        $product->update(['metode_penilaian' => $metodeBaru]);
    }
}
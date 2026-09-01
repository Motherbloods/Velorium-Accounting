<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentSalesReportItem extends Model
{
    protected $fillable = [
        'consignment_sales_report_id',
        'shipment_item_id',
        'product_id',
        'qty_terjual',
        'harga_titip',
        'hpp_satuan',
        'subtotal_penjualan',
        'subtotal_hpp',
    ];

    public function salesReport(): BelongsTo
    {
        return $this->belongsTo(ConsignmentSalesReport::class, 'consignment_sales_report_id');
    }

    public function shipmentItem(): BelongsTo
    {
        return $this->belongsTo(ConsignmentShipmentItem::class, 'shipment_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
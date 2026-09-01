<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentShipmentItem extends Model
{
    protected $fillable = [
        'shipment_id',
        'product_id',
        'qty_kirim',
        'qty_terjual',
        'qty_retur',
        'harga_titip',
        'hpp_satuan',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ConsignmentShipment::class, 'shipment_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sisaBelumTerjual(): int
    {
        return $this->qty_kirim - $this->qty_terjual - $this->qty_retur;
    }
}
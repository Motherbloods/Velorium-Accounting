<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentReturn extends Model
{
    protected $fillable = [
        'shipment_id',
        'product_id',
        'qty_retur',
        'tanggal_retur',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_retur' => 'date',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ConsignmentShipment::class, 'shipment_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
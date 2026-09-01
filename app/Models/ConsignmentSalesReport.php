<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentSalesReport extends Model
{
    protected $fillable = [
        'shipment_id',
        'tanggal_lapor',
        'total_qty_terjual',
        'total_penjualan',
        'total_hpp',
        'total_komisi',
        'total_diterima',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lapor' => 'date',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ConsignmentShipment::class, 'shipment_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentSalesReportItem::class, 'consignment_sales_report_id');
    }
}
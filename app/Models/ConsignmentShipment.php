<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentShipment extends Model
{
    protected $fillable = [
        'nomor_konsinyasi',
        'consignee_id',
        'tanggal_kirim',
        'status',
        'journal_entry_id',
        'branch_id',
        'warehouse_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kirim' => 'date',
        ];
    }

    public function consignee(): BelongsTo
    {
        return $this->belongsTo(Consignee::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentShipmentItem::class, 'shipment_id');
    }

    public function salesReports(): HasMany
    {
        return $this->hasMany(ConsignmentSalesReport::class, 'shipment_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ConsignmentReturn::class, 'shipment_id');
    }
}